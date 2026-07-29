<?php
/**
 * Messagerie interne (réelle, en base de données).
 * -------------------------------------------------------------
 * Les conversations vivent dans un type de contenu privé
 * « pp_conversation » (une conversation = un bien + deux membres) ;
 * chaque message est un commentaire WordPress de type « pp_message »
 * posé sur la conversation, comme les avis (pp_avis). Le non-lu est
 * suivi par une méta de conversation par participant (horodatage de
 * dernière lecture).
 *
 * Règle du projet : aucun e-mail ni téléphone n'est jamais partagé
 * entre membres. La plateforme envoie seulement une notification
 * « vous avez reçu un message » qui renvoie vers la page Messages.
 *
 * Le circuit côté navigateur (page Messages, pop-up « Écrire à
 * l'hôte » de la fiche bien) passe par les points d'entrée AJAX
 * déclarés ici, protégés par le jeton pp_messages.
 */

if (!defined('ABSPATH')) {
    exit;
}

/* =============================================================
   1. TYPE DE CONTENU « Conversation »
   ============================================================= */

function poolparty_g4_enregistrer_cpt_conversation() {
    register_post_type('pp_conversation', array(
        'labels' => array(
            'name'          => 'Conversations',
            'singular_name' => 'Conversation',
        ),
        'public'              => false,
        'show_ui'             => false,
        'exclude_from_search' => true,
        'supports'            => array('title'),
    ));
}
add_action('init', 'poolparty_g4_enregistrer_cpt_conversation', 5);

/* =============================================================
   2. LECTURE : conversations et messages d'un membre
   ============================================================= */

/** Conversations où le membre est l'un des deux participants. */
function poolparty_g4_conversations_utilisateur($user_id) {
    $user_id = (int) $user_id;
    if (!$user_id) {
        return array();
    }
    return get_posts(array(
        'post_type'      => 'pp_conversation',
        'post_status'    => 'publish',
        'posts_per_page' => 100,
        'orderby'        => 'modified',
        'order'          => 'DESC',
        'meta_query'     => array(
            'relation' => 'OR',
            array('key' => 'pp_membre_a', 'value' => $user_id),
            array('key' => 'pp_membre_b', 'value' => $user_id),
        ),
    ));
}

/** Messages d'une conversation, du plus ancien au plus récent. */
function poolparty_g4_messages_conversation($conv_id) {
    return get_comments(array(
        'post_id' => (int) $conv_id,
        'type'    => 'pp_message',
        'status'  => 'approve',
        'orderby' => 'comment_date_gmt',
        'order'   => 'ASC',
    ));
}

/** L'autre participant d'une conversation, pour un membre donné. */
function poolparty_g4_conversation_interlocuteur($conv_id, $user_id) {
    $a = (int) get_post_meta($conv_id, 'pp_membre_a', true);
    $b = (int) get_post_meta($conv_id, 'pp_membre_b', true);
    return ((int) $user_id === $a) ? $b : $a;
}

/** Le membre participe-t-il à cette conversation ? */
function poolparty_g4_conversation_autorisee($conv_id, $user_id) {
    $a = (int) get_post_meta($conv_id, 'pp_membre_a', true);
    $b = (int) get_post_meta($conv_id, 'pp_membre_b', true);
    return (int) $user_id === $a || (int) $user_id === $b;
}

/**
 * Horodatage (unix) de dernière lecture d'une conversation par un
 * membre. 0 si jamais ouverte.
 */
function poolparty_g4_conversation_lue_le($conv_id, $user_id) {
    return (int) get_post_meta($conv_id, 'pp_lu_' . (int) $user_id, true);
}

/** Nombre de messages non lus d'une conversation pour un membre. */
function poolparty_g4_conversation_non_lus($conv_id, $user_id) {
    $depuis = poolparty_g4_conversation_lue_le($conv_id, $user_id);
    $nb = 0;
    foreach (poolparty_g4_messages_conversation($conv_id) as $message) {
        if ((int) $message->user_id === (int) $user_id) {
            continue;
        }
        if (strtotime($message->comment_date) > $depuis) {
            $nb++;
        }
    }
    return $nb;
}

/** Total des messages non lus d'un membre (pastille du menu). */
function poolparty_g4_messages_non_lus($user_id) {
    $total = 0;
    foreach (poolparty_g4_conversations_utilisateur($user_id) as $conv) {
        $total += poolparty_g4_conversation_non_lus($conv->ID, $user_id);
    }
    return $total;
}

/**
 * Nom et photo affichés pour un interlocuteur. Si c'est l'auteur du
 * bien de la conversation et que le bien a un hôte de la table de
 * référence (id_hote), on garde le prénom et le portrait de la fiche ;
 * sinon prénom du compte et avatar WordPress.
 */
function poolparty_g4_interlocuteur_affichage($user_id, $bien_id) {
    $user = get_userdata((int) $user_id);
    $nom  = $user ? ($user->first_name ?: $user->display_name) : 'Membre Pool Party';
    $photo = $user ? get_avatar_url($user->ID, array('size' => 96)) : '';

    if ($bien_id && (int) get_post_field('post_author', $bien_id) === (int) $user_id) {
        $id_hote = poolparty_g4_meta($bien_id, 'id_hote');
        $hote    = $id_hote ? poolparty_g4_get_hote($id_hote) : null;
        if ($hote) {
            $nom   = $hote['prenom'];
            $photo = pp_asset($hote['photo']);
        }
    }
    return array('nom' => $nom, 'photo' => $photo);
}

/**
 * Une conversation mise au format attendu par main.js :
 * { id, hote, photo, bienId, bienTitre, bienLien, maj, lu, messages }.
 * « hote » désigne simplement l'interlocuteur (nom hérité du premier
 * prototype) ; « de » vaut « moi » ou « hote » selon l'expéditeur.
 */
function poolparty_g4_conversation_pour_js($conv, $user_id) {
    $bien_id      = (int) get_post_meta($conv->ID, 'pp_bien_id', true);
    $interlocuteur = poolparty_g4_conversation_interlocuteur($conv->ID, $user_id);
    $affichage    = poolparty_g4_interlocuteur_affichage($interlocuteur, $bien_id);
    $maintenant   = current_time('timestamp');

    $messages = array();
    $dernier  = 0;
    foreach (poolparty_g4_messages_conversation($conv->ID) as $message) {
        $ts = strtotime($message->comment_date);
        $dernier = max($dernier, $ts);
        $messages[] = array(
            'de'    => ((int) $message->user_id === (int) $user_id) ? 'moi' : 'hote',
            'texte' => $message->comment_content,
            'label' => poolparty_g4_messagerie_label($ts, $maintenant),
        );
    }

    return array(
        'id'        => (int) $conv->ID,
        'hote'      => $affichage['nom'],
        'photo'     => $affichage['photo'],
        'bienId'    => $bien_id,
        'bienTitre' => $bien_id ? get_the_title($bien_id) : 'Espace Pool Party',
        'bienLien'  => $bien_id ? get_permalink($bien_id) : '',
        'maj'       => $dernier * 1000,
        'lu'        => poolparty_g4_conversation_non_lus($conv->ID, $user_id) === 0,
        'messages'  => $messages,
    );
}

/* =============================================================
   3. ÉCRITURE : trouver ou créer une conversation, envoyer
   ============================================================= */

/**
 * Conversation entre un membre et l'hôte d'un bien (créée au premier
 * message). Renvoie l'ID de la conversation, ou WP_Error si le bien
 * est invalide ou appartient au membre lui-même.
 */
function poolparty_g4_conversation_trouver_ou_creer($bien_id, $membre_id) {
    $bien_id  = (int) $bien_id;
    $membre_id = (int) $membre_id;
    if (!$bien_id || get_post_type($bien_id) !== 'bien' || get_post_status($bien_id) !== 'publish') {
        return new WP_Error('bien', 'Cet espace n\'est plus disponible.');
    }
    $hote_id = (int) get_post_field('post_author', $bien_id);
    if ($hote_id === $membre_id) {
        return new WP_Error('soi', 'Vous êtes l\'hôte de cet espace : les demandes des membres arriveront ici.');
    }

    $existantes = get_posts(array(
        'post_type'      => 'pp_conversation',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => array(
            array('key' => 'pp_bien_id', 'value' => $bien_id),
            array('key' => 'pp_membre_a', 'value' => $membre_id),
        ),
    ));
    if ($existantes) {
        return (int) $existantes[0];
    }

    $membre = get_userdata($membre_id);
    $conv_id = wp_insert_post(array(
        'post_type'   => 'pp_conversation',
        'post_status' => 'publish',
        'post_title'  => sprintf('%s : %s', get_the_title($bien_id), $membre ? $membre->display_name : $membre_id),
    ), true);
    if (is_wp_error($conv_id) || !$conv_id) {
        return new WP_Error('creation', 'Impossible d\'ouvrir la conversation. Réessayez.');
    }
    update_post_meta($conv_id, 'pp_bien_id', $bien_id);
    update_post_meta($conv_id, 'pp_membre_a', $membre_id);
    update_post_meta($conv_id, 'pp_membre_b', $hote_id);
    return (int) $conv_id;
}

/**
 * Ajoute un message dans une conversation et prévient le destinataire
 * par e-mail (seulement s'il n'avait rien de non lu : pas de rafale
 * pendant un échange actif). Renvoie l'ID du commentaire ou WP_Error.
 */
function poolparty_g4_message_envoyer($conv_id, $expediteur_id, $texte) {
    $texte = trim((string) $texte);
    if ($texte === '') {
        return new WP_Error('vide', 'Écrivez votre message avant de l\'envoyer.');
    }
    $destinataire_id = poolparty_g4_conversation_interlocuteur($conv_id, $expediteur_id);
    $deja_non_lus    = poolparty_g4_conversation_non_lus($conv_id, $destinataire_id);

    $expediteur = get_userdata((int) $expediteur_id);
    $message_id = wp_insert_comment(array(
        'comment_post_ID'      => (int) $conv_id,
        'comment_type'         => 'pp_message',
        'user_id'              => (int) $expediteur_id,
        'comment_author'       => $expediteur ? $expediteur->display_name : '',
        'comment_author_email' => '',
        'comment_content'      => $texte,
        'comment_approved'     => 1,
    ));
    if (!$message_id) {
        return new WP_Error('envoi', 'Votre message n\'a pas pu être envoyé. Réessayez.');
    }

    // L'expéditeur est forcément à jour de sa propre conversation.
    update_post_meta($conv_id, 'pp_lu_' . (int) $expediteur_id, current_time('timestamp'));

    // Remonte la conversation en tête de liste (tri par date de modification).
    wp_update_post(array('ID' => (int) $conv_id));

    if ($deja_non_lus === 0) {
        poolparty_g4_email_message_recu($conv_id, $expediteur_id, $destinataire_id);
    }
    return (int) $message_id;
}

/**
 * Notification « vous avez reçu un message » : prénom de l'expéditeur
 * et espace concerné, jamais de coordonnées, réponse via la messagerie.
 */
function poolparty_g4_email_message_recu($conv_id, $expediteur_id, $destinataire_id) {
    $destinataire = get_userdata((int) $destinataire_id);
    if (!$destinataire || !is_email($destinataire->user_email)) {
        return;
    }
    $bien_id   = (int) get_post_meta($conv_id, 'pp_bien_id', true);
    $affichage = poolparty_g4_interlocuteur_affichage($expediteur_id, $bien_id);
    $titre     = $bien_id ? get_the_title($bien_id) : 'un espace Pool Party';

    $corps = '<p>Bonjour ' . esc_html($destinataire->first_name ?: $destinataire->display_name) . ',</p>'
        . '<p><strong>' . esc_html($affichage['nom']) . '</strong> vous a envoyé un message au sujet de <strong>' . esc_html($titre) . '</strong>.</p>'
        . '<p>Pour le lire et y répondre, rendez-vous dans votre messagerie Pool Party. Vos coordonnées restent privées : tous les échanges passent par la plateforme.</p>'
        . '<p><a href="' . esc_url(home_url('/messages/')) . '" style="color:#CA8171;">Ouvrir ma messagerie</a></p>';

    poolparty_g4_email_envoyer(
        $destinataire->user_email,
        'Vous avez reçu un message sur Pool Party',
        'Nouveau message reçu',
        $corps
    );
}

/* =============================================================
   4. AJAX (membres connectés uniquement)
   ============================================================= */

/** Boîte de réception du membre connecté. */
function poolparty_g4_ajax_messages_liste() {
    check_ajax_referer('pp_messages', 'nonce');
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connectez-vous pour accéder à vos messages.'), 401);
    }
    $user_id = get_current_user_id();
    $conversations = array();
    foreach (poolparty_g4_conversations_utilisateur($user_id) as $conv) {
        $conversations[] = poolparty_g4_conversation_pour_js($conv, $user_id);
    }
    wp_send_json_success(array(
        'conversations' => $conversations,
        'nonLus'        => poolparty_g4_messages_non_lus($user_id),
    ));
}
add_action('wp_ajax_pp_messages_liste', 'poolparty_g4_ajax_messages_liste');

/** Marque une conversation comme lue (à l'ouverture du fil). */
function poolparty_g4_ajax_conversation_lue() {
    check_ajax_referer('pp_messages', 'nonce');
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connectez-vous pour continuer.'), 401);
    }
    $user_id = get_current_user_id();
    $conv_id = isset($_POST['conversation_id']) ? absint($_POST['conversation_id']) : 0;
    if (!$conv_id || get_post_type($conv_id) !== 'pp_conversation' || !poolparty_g4_conversation_autorisee($conv_id, $user_id)) {
        wp_send_json_error(array('message' => 'Conversation introuvable.'), 404);
    }
    update_post_meta($conv_id, 'pp_lu_' . $user_id, current_time('timestamp'));
    wp_send_json_success(array('nonLus' => poolparty_g4_messages_non_lus($user_id)));
}
add_action('wp_ajax_pp_conversation_lue', 'poolparty_g4_ajax_conversation_lue');

/**
 * Envoi d'un message : dans une conversation existante
 * (conversation_id) ou vers l'hôte d'un bien (bien_id, pop-up
 * « Écrire à l'hôte » de la fiche).
 */
function poolparty_g4_ajax_message_envoyer() {
    check_ajax_referer('pp_messages', 'nonce');
    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connectez-vous pour écrire votre message.'), 401);
    }
    $user_id = get_current_user_id();
    $texte   = isset($_POST['texte']) ? sanitize_textarea_field(wp_unslash($_POST['texte'])) : '';
    $conv_id = isset($_POST['conversation_id']) ? absint($_POST['conversation_id']) : 0;
    $bien_id = isset($_POST['bien_id']) ? absint($_POST['bien_id']) : 0;

    if ($conv_id) {
        if (get_post_type($conv_id) !== 'pp_conversation' || !poolparty_g4_conversation_autorisee($conv_id, $user_id)) {
            wp_send_json_error(array('message' => 'Conversation introuvable.'), 404);
        }
    } else {
        $conv_id = poolparty_g4_conversation_trouver_ou_creer($bien_id, $user_id);
        if (is_wp_error($conv_id)) {
            wp_send_json_error(array('message' => $conv_id->get_error_message()), 400);
        }
    }

    $resultat = poolparty_g4_message_envoyer($conv_id, $user_id, $texte);
    if (is_wp_error($resultat)) {
        wp_send_json_error(array('message' => $resultat->get_error_message()), 400);
    }

    wp_send_json_success(array(
        'conversation' => poolparty_g4_conversation_pour_js(get_post($conv_id), $user_id),
        'nonLus'       => poolparty_g4_messages_non_lus($user_id),
    ));
}
add_action('wp_ajax_pp_message_envoyer', 'poolparty_g4_ajax_message_envoyer');

/* =============================================================
   5. SEED : conversations de démonstration (une seule fois)
   ============================================================= */

/**
 * Amorce quelques conversations réelles entre le compte de
 * démonstration (membre-demo) et les hôtes des premiers biens, pour
 * que la boîte de réception ne soit pas vide à la soutenance.
 * Verrouillé par option, comme les autres seeds du thème.
 */
function poolparty_g4_seed_conversations() {
    if (get_option('pp_messages_seed_version') === '1') {
        return;
    }
    $membre = get_user_by('login', 'membre-demo');
    if (!$membre) {
        return; // le compte démo n'existe pas encore : on réessaiera
    }

    $trames = array(
        array(
            array('de' => 'hote', 'texte' => "Bonjour et bienvenue ! Merci pour votre intérêt pour mon espace. N'hésitez pas si vous avez la moindre question avant de réserver.", 'delta' => 172800),
            array('de' => 'moi',  'texte' => "Bonjour, merci ! L'eau est-elle chauffée en ce moment, et à quelle température ?", 'delta' => 169200),
            array('de' => 'hote', 'texte' => "Oui, l'eau est chauffée autour de 28°C toute la saison. Vous serez au chaud même en soirée.", 'delta' => 165600),
        ),
        array(
            array('de' => 'moi',  'texte' => "Bonjour, est-il possible de venir à 6 personnes plutôt que 4 ? On serait deux de plus.", 'delta' => 86400),
            array('de' => 'hote', 'texte' => "Bonjour ! Oui, aucun souci pour 6 personnes, l'espace est prévu pour. Je vous confirme dès que votre demande arrive.", 'delta' => 82800),
        ),
        array(
            array('de' => 'hote', 'texte' => "Bonjour, je vois que votre réservation approche. Voici le code du portail : 1974. À très vite !", 'delta' => 10800),
        ),
    );

    $biens = get_posts(array(
        'post_type'      => 'bien',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
    ));

    $index = 0;
    foreach ($biens as $bien_id) {
        if ($index >= count($trames)) {
            break;
        }
        $hote_id = (int) get_post_field('post_author', $bien_id);
        if (!$hote_id || $hote_id === (int) $membre->ID) {
            continue;
        }
        $conv_id = poolparty_g4_conversation_trouver_ou_creer($bien_id, $membre->ID);
        if (is_wp_error($conv_id) || poolparty_g4_messages_conversation($conv_id)) {
            $index++;
            continue; // déjà des messages : on ne double pas
        }
        foreach ($trames[$index] as $m) {
            $expediteur = ($m['de'] === 'moi') ? (int) $membre->ID : $hote_id;
            $auteur     = get_userdata($expediteur);
            $date       = date('Y-m-d H:i:s', current_time('timestamp') - (int) $m['delta']);
            wp_insert_comment(array(
                'comment_post_ID'      => $conv_id,
                'comment_type'         => 'pp_message',
                'user_id'              => $expediteur,
                'comment_author'       => $auteur ? $auteur->display_name : '',
                'comment_author_email' => '',
                'comment_content'      => $m['texte'],
                'comment_approved'     => 1,
                'comment_date'         => $date,
            ));
        }
        // L'expéditeur du dernier message est à jour, l'autre découvre le fil.
        $dernier = end($trames[$index]);
        $expediteur_final = ($dernier['de'] === 'moi') ? (int) $membre->ID : $hote_id;
        update_post_meta($conv_id, 'pp_lu_' . $expediteur_final, current_time('timestamp'));
        $index++;
    }

    if ($index > 0) {
        update_option('pp_messages_seed_version', '1');
    }
}
add_action('init', 'poolparty_g4_seed_conversations', 30);

/* =============================================================
   6. OUTILS
   ============================================================= */

/**
 * Libellé court d'horodatage pour un message (« Aujourd'hui 10:12 »,
 * « Hier 18:30 », sinon « 7 juil. 11:05 »). Indépendant de la locale.
 */
function poolparty_g4_messagerie_label($ts, $maintenant) {
    $heure = date('H:i', $ts);
    $jour  = date('Y-m-d', $ts);
    if ($jour === date('Y-m-d', $maintenant)) {
        return "Aujourd'hui " . $heure;
    }
    if ($jour === date('Y-m-d', $maintenant - 86400)) {
        return 'Hier ' . $heure;
    }
    $mois = array(
        1 => 'janv.', 2 => 'févr.', 3 => 'mars', 4 => 'avr.',
        5 => 'mai', 6 => 'juin', 7 => 'juil.', 8 => 'août',
        9 => 'sept.', 10 => 'oct.', 11 => 'nov.', 12 => 'déc.',
    );
    return intval(date('j', $ts)) . ' ' . $mois[intval(date('n', $ts))] . ' ' . $heure;
}
