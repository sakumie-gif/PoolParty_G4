<?php
/**
 * Espace membre « Mon compte » (page /mon-compte/).
 * -------------------------------------------------------------
 * Le back-office WordPress étant fermé aux membres (inc/auth.php),
 * cette page leur donne la main sur leur compte : informations
 * personnelles, mot de passe, préférences de notification, copie
 * des données (JSON) et fermeture du compte.
 *
 * Schéma Post/Redirect/Get de inc/mes-annonces.php : chaque bloc
 * poste pp_compte_action + nonce dédié, le traitement redirige avec
 * ?pp_msg=. Les succès s'affichent en toast (bas droite), les
 * erreurs en bandeau au-dessus du bloc concerné.
 */

if (!defined('ABSPATH')) {
    exit;
}

/* =============================================================
   1. PRÉFÉRENCES DE NOTIFICATION
   ============================================================= */

/**
 * Types de notification proposés au membre : clé de méta => libellé.
 * Méta absente ou '1' = activé (les comptes existants restent notifiés),
 * '0' = désactivé. Les e-mails de sécurité du compte et la confirmation
 * d'une réservation ne passent jamais par ces préférences.
 */
function poolparty_g4_types_notification() {
    return array(
        'reservation' => 'Réservations : demandes envoyées et reçues',
        'message'     => 'Messagerie : nouveau message reçu',
        'avis'        => 'Avis : nouvel avis déposé sur une de vos annonces',
        'annonce'     => 'Annonces : suivi de vos annonces (validation, mise en ligne)',
    );
}

/** Le membre accepte-t-il ce type de notification ? */
function poolparty_g4_notif_active($user_id, $type) {
    return get_user_meta((int) $user_id, 'pp_notif_' . $type, true) !== '0';
}

/* =============================================================
   2. TRAITEMENT DES FORMULAIRES (Post / Redirect / Get)
   ============================================================= */

/** Redirige vers /mon-compte/ avec un indicateur de message. */
function poolparty_g4_mon_compte_rediriger($msg, $ancre = '') {
    $url = add_query_arg('pp_msg', $msg, home_url('/mon-compte/'));
    if ($ancre !== '') {
        $url .= '#' . $ancre;
    }
    wp_safe_redirect($url);
    exit;
}

function poolparty_g4_mon_compte_traiter() {
    if (!isset($_POST['pp_compte_action']) || !is_page('mon-compte')) {
        return;
    }
    if (!is_user_logged_in()) {
        wp_safe_redirect(home_url('/mon-compte/'));
        exit;
    }

    $action = sanitize_key($_POST['pp_compte_action']);
    $user   = wp_get_current_user();

    switch ($action) {

        // -- Informations personnelles : prénom, nom, nom affiché, e-mail.
        case 'infos':
            check_admin_referer('pp_compte_infos');

            $prenom  = isset($_POST['prenom']) ? sanitize_text_field(wp_unslash($_POST['prenom'])) : '';
            $nom     = isset($_POST['nom']) ? sanitize_text_field(wp_unslash($_POST['nom'])) : '';
            $affiche = isset($_POST['nom_affiche']) ? sanitize_text_field(wp_unslash($_POST['nom_affiche'])) : '';
            $email   = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';

            if ($prenom === '' || $nom === '') {
                poolparty_g4_mon_compte_rediriger('infos-champs', 'bloc-infos');
            }
            if (!is_email($email)) {
                poolparty_g4_mon_compte_rediriger('infos-email', 'bloc-infos');
            }
            // Unicité : email_exists renvoie l'identifiant du compte qui
            // utilise l'adresse ; on accepte seulement le compte courant.
            $existant = email_exists($email);
            if ($existant && (int) $existant !== (int) $user->ID) {
                poolparty_g4_mon_compte_rediriger('infos-email-pris', 'bloc-infos');
            }
            if (function_exists('poolparty_g4_email_banni') && poolparty_g4_email_banni($email)) {
                poolparty_g4_mon_compte_rediriger('infos-email-pris', 'bloc-infos');
            }

            $ancienne = $user->user_email;
            wp_update_user(array(
                'ID'           => $user->ID,
                'first_name'   => $prenom,
                'last_name'    => $nom,
                'display_name' => ($affiche !== '' ? $affiche : $prenom),
                'user_email'   => $email,
            ));

            // Alerte de sécurité à l'ancienne adresse (toujours envoyée).
            if ($email !== $ancienne) {
                poolparty_g4_email_compte_email_change($ancienne, $email, $user->display_name);
            }
            poolparty_g4_mon_compte_rediriger('infos-ok', 'bloc-infos');
            break;

        // -- Mot de passe : vérification de l'actuel, 8 caractères minimum,
        //    et rappel de la session juste après (wp_set_password détruit
        //    les sessions : sans wp_set_auth_cookie le membre serait
        //    déconnecté sans comprendre pourquoi).
        case 'mdp':
            check_admin_referer('pp_compte_mdp');

            $actuel   = isset($_POST['mdp_actuel']) ? (string) $_POST['mdp_actuel'] : '';
            $nouveau  = isset($_POST['mdp_nouveau']) ? (string) $_POST['mdp_nouveau'] : '';
            $confirme = isset($_POST['mdp_confirme']) ? (string) $_POST['mdp_confirme'] : '';

            if (!wp_check_password($actuel, $user->user_pass, $user->ID)) {
                poolparty_g4_mon_compte_rediriger('mdp-actuel', 'bloc-mdp');
            }
            if (strlen($nouveau) < 8) {
                poolparty_g4_mon_compte_rediriger('mdp-court', 'bloc-mdp');
            }
            if ($nouveau !== $confirme) {
                poolparty_g4_mon_compte_rediriger('mdp-differents', 'bloc-mdp');
            }

            wp_set_password($nouveau, $user->ID);
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID, true);

            // E-mail de sécurité (toujours envoyé) : wp_set_password
            // n'envoie aucune notice, contrairement à wp_update_user.
            poolparty_g4_email_compte_mdp_change($user);
            poolparty_g4_mon_compte_rediriger('mdp-ok', 'bloc-mdp');
            break;

        // -- Préférences de notification : une méta par type.
        case 'prefs':
            check_admin_referer('pp_compte_prefs');
            foreach (array_keys(poolparty_g4_types_notification()) as $type) {
                update_user_meta($user->ID, 'pp_notif_' . $type, empty($_POST['notif_' . $type]) ? '0' : '1');
            }
            poolparty_g4_mon_compte_rediriger('prefs-ok', 'bloc-notifs');
            break;

        // -- Copie des données : fichier JSON généré à la volée et envoyé
        //    en téléchargement direct, rien n'est écrit sur le serveur.
        case 'export':
            check_admin_referer('pp_compte_export');
            poolparty_g4_mon_compte_exporter($user);
            break;

        // -- Fermeture du compte : mot de passe exigé, refus si des
        //    réservations sont actives, contenus réattribués à
        //    l'administration (même mécanique que la console).
        case 'fermer':
            check_admin_referer('pp_compte_fermer');

            if (current_user_can('manage_options')) {
                poolparty_g4_mon_compte_rediriger('fermer-admin', 'bloc-fermer');
            }
            $mdp = isset($_POST['mdp_fermeture']) ? (string) $_POST['mdp_fermeture'] : '';
            if (!wp_check_password($mdp, $user->user_pass, $user->ID)) {
                poolparty_g4_mon_compte_rediriger('fermer-mdp', 'bloc-fermer');
            }
            if (function_exists('poolparty_g4_membre_a_resas_actives') && poolparty_g4_membre_a_resas_actives($user->ID)) {
                poolparty_g4_mon_compte_rediriger('fermer-resas', 'bloc-fermer');
            }

            // Confirmation envoyée avant la suppression (l'adresse
            // n'existera plus en base ensuite).
            poolparty_g4_email_compte_ferme($user);

            // Réattribution des contenus au premier administrateur du site,
            // comme le fait la console quand elle supprime un membre.
            $admins   = get_users(array('role' => 'administrator', 'number' => 1, 'orderby' => 'ID', 'order' => 'ASC', 'fields' => 'ID'));
            $admin_id = $admins ? (int) $admins[0] : 0;
            require_once ABSPATH . 'wp-admin/includes/user.php';
            wp_delete_user($user->ID, $admin_id);

            wp_logout();
            wp_safe_redirect(add_query_arg('pp_compte', 'ferme', home_url('/')));
            exit;
    }
}
add_action('template_redirect', 'poolparty_g4_mon_compte_traiter');

/**
 * Messages de la page (indicateur ?pp_msg=). Renvoie
 * array(texte, type, bloc) : type « ok » = toast bas droite,
 * « alerte » = bandeau au-dessus du bloc indiqué.
 */
function poolparty_g4_mon_compte_flash() {
    $cle = isset($_GET['pp_msg']) ? sanitize_key($_GET['pp_msg']) : '';
    $map = array(
        'infos-ok'        => array('Vos informations ont été mises à jour.', 'ok', ''),
        'infos-champs'    => array('Merci d\'indiquer votre prénom et votre nom.', 'alerte', 'infos'),
        'infos-email'     => array('Cette adresse e-mail n\'est pas valide.', 'alerte', 'infos'),
        'infos-email-pris' => array('Cette adresse e-mail est déjà utilisée par un autre compte.', 'alerte', 'infos'),
        'mdp-ok'          => array('Votre mot de passe a été modifié. Vous restez connecté.', 'ok', ''),
        'mdp-actuel'      => array('Le mot de passe actuel est incorrect.', 'alerte', 'mdp'),
        'mdp-court'       => array('Le nouveau mot de passe doit contenir au moins 8 caractères.', 'alerte', 'mdp'),
        'mdp-differents'  => array('Le nouveau mot de passe et sa confirmation ne correspondent pas.', 'alerte', 'mdp'),
        'prefs-ok'        => array('Vos préférences de notification sont enregistrées.', 'ok', ''),
        'fermer-mdp'      => array('Le mot de passe est incorrect. Le compte n\'a pas été fermé.', 'alerte', 'fermer'),
        'fermer-resas'    => array('Vous avez des réservations en cours. Attendez leur terme ou annulez-les avant de fermer votre compte.', 'alerte', 'fermer'),
        'fermer-admin'    => array('Un compte administrateur ne peut pas être fermé depuis cette page.', 'alerte', 'fermer'),
    );
    return isset($map[$cle]) ? $map[$cle] : null;
}

/* =============================================================
   3. COPIE DES DONNÉES (export JSON)
   ============================================================= */

/**
 * Assemble puis envoie en téléchargement les données du membre :
 * profil, annonces, réservations, avis et messages. Règle du projet
 * appliquée au fichier comme à l'interface : jamais d'e-mail ni de
 * téléphone d'un autre membre, seulement des noms d'affichage.
 */
function poolparty_g4_mon_compte_exporter($user) {
    $statuts = function_exists('poolparty_g4_statuts_reservation') ? poolparty_g4_statuts_reservation() : array();
    $libelle_statut = function ($cle) use ($statuts) {
        return isset($statuts[$cle]) ? $statuts[$cle] : $cle;
    };

    $prefs = array();
    foreach (poolparty_g4_types_notification() as $type => $libelle) {
        $prefs[$libelle] = poolparty_g4_notif_active($user->ID, $type) ? 'activée' : 'désactivée';
    }

    $donnees = array(
        'plateforme' => 'Pool Party',
        'genere_le'  => current_time('d/m/Y H:i'),
        'profil'     => array(
            'prenom'        => $user->first_name,
            'nom'           => $user->last_name,
            'nom_affiche'   => $user->display_name,
            'email'         => $user->user_email,
            'inscrit_le'    => mysql2date('d/m/Y', $user->user_registered),
            'notifications' => $prefs,
        ),
        'annonces'               => array(),
        'reservations_envoyees'  => array(),
        'reservations_recues'    => array(),
        'avis_ecrits'            => array(),
        'avis_recus'             => array(),
        'conversations'          => array(),
    );

    // Annonces du membre.
    if (function_exists('poolparty_g4_mes_annonces')) {
        foreach (poolparty_g4_mes_annonces($user->ID) as $bien) {
            $donnees['annonces'][] = array(
                'titre'       => get_the_title($bien),
                'statut'      => $bien->post_status === 'publish' ? 'en ligne' : ($bien->post_status === 'pending' ? 'en attente de validation' : 'brouillon'),
                'ville'       => get_post_meta($bien->ID, 'pp_ville', true),
                'prix_heure'  => get_post_meta($bien->ID, 'pp_prix_heure', true),
                'description' => $bien->post_content,
                'deposee_le'  => get_the_date('d/m/Y', $bien),
            );
        }
    }

    // Réservations envoyées (locataire) et reçues (hôte).
    foreach (poolparty_g4_reservations_locataire($user->ID) as $resa) {
        $bien_id = (int) get_post_meta($resa->ID, 'pp_bien_id', true);
        $donnees['reservations_envoyees'][] = array(
            'espace'  => $bien_id ? get_the_title($bien_id) : $resa->post_title,
            'date'    => get_post_meta($resa->ID, 'pp_date', true),
            'creneau' => get_post_meta($resa->ID, 'pp_creneau', true),
            'invites' => get_post_meta($resa->ID, 'pp_invites', true),
            'formule' => get_post_meta($resa->ID, 'pp_formule', true),
            'total'   => get_post_meta($resa->ID, 'pp_total', true),
            'statut'  => $libelle_statut(get_post_meta($resa->ID, 'pp_statut', true)),
        );
    }
    foreach (poolparty_g4_reservations_hote($user->ID) as $resa) {
        $bien_id   = (int) get_post_meta($resa->ID, 'pp_bien_id', true);
        $locataire = get_userdata((int) $resa->post_author);
        $donnees['reservations_recues'][] = array(
            'espace'    => $bien_id ? get_the_title($bien_id) : $resa->post_title,
            'locataire' => $locataire ? $locataire->display_name : 'Un membre',
            'date'      => get_post_meta($resa->ID, 'pp_date', true),
            'creneau'   => get_post_meta($resa->ID, 'pp_creneau', true),
            'invites'   => get_post_meta($resa->ID, 'pp_invites', true),
            'total'     => get_post_meta($resa->ID, 'pp_total', true),
            'statut'    => $libelle_statut(get_post_meta($resa->ID, 'pp_statut', true)),
        );
    }

    // Avis écrits par le membre (sur les espaces et sur ses locataires).
    foreach (get_comments(array('user_id' => $user->ID, 'type__in' => array('pp_avis', 'pp_avis_locataire', 'pp_avis_reponse'))) as $avis) {
        $donnees['avis_ecrits'][] = array(
            'type'  => $avis->comment_type === 'pp_avis' ? 'avis sur un espace' : ($avis->comment_type === 'pp_avis_locataire' ? 'avis sur un locataire' : 'réponse à un avis'),
            'sujet' => get_the_title((int) $avis->comment_post_ID),
            'note'  => get_comment_meta($avis->comment_ID, 'pp_note', true),
            'texte' => $avis->comment_content,
            'le'    => get_comment_date('d/m/Y', $avis),
        );
    }

    // Avis reçus sur ses annonces.
    $biens_ids = get_posts(array(
        'post_type'      => 'bien',
        'author'         => $user->ID,
        'post_status'    => array('publish', 'pending', 'draft'),
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ));
    if ($biens_ids) {
        foreach (get_comments(array('type' => 'pp_avis', 'post__in' => $biens_ids)) as $avis) {
            $auteur = get_userdata((int) $avis->user_id);
            $donnees['avis_recus'][] = array(
                'espace' => get_the_title((int) $avis->comment_post_ID),
                'de'     => $auteur ? $auteur->display_name : 'Un membre',
                'note'   => get_comment_meta($avis->comment_ID, 'pp_note', true),
                'texte'  => $avis->comment_content,
                'le'     => get_comment_date('d/m/Y', $avis),
            );
        }
    }

    // Conversations de la messagerie interne.
    if (function_exists('poolparty_g4_conversations_utilisateur')) {
        foreach (poolparty_g4_conversations_utilisateur($user->ID) as $conv) {
            $bien_id       = (int) get_post_meta($conv->ID, 'pp_bien_id', true);
            $interlocuteur = get_userdata(poolparty_g4_conversation_interlocuteur($conv->ID, $user->ID));
            $messages      = array();
            foreach (poolparty_g4_messages_conversation($conv->ID) as $message) {
                $messages[] = array(
                    'de'    => ((int) $message->user_id === (int) $user->ID) ? 'moi' : ($interlocuteur ? $interlocuteur->display_name : 'Un membre'),
                    'texte' => $message->comment_content,
                    'le'    => mysql2date('d/m/Y H:i', $message->comment_date),
                );
            }
            $donnees['conversations'][] = array(
                'espace'        => $bien_id ? get_the_title($bien_id) : 'Espace Pool Party',
                'interlocuteur' => $interlocuteur ? $interlocuteur->display_name : 'Un membre',
                'messages'      => $messages,
            );
        }
    }

    nocache_headers();
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename="poolparty-mes-donnees-' . current_time('Y-m-d') . '.json"');
    echo wp_json_encode($donnees, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/* =============================================================
   4. E-MAILS DE SÉCURITÉ DU COMPTE (toujours envoyés)
   ============================================================= */

/**
 * WordPress envoie sa propre notice de changement d'adresse : on la
 * remplace par l'e-mail au gabarit du site envoyé ci-dessous.
 */
add_filter('send_email_change_email', '__return_false');

/** Alerte envoyée à l'ANCIENNE adresse quand l'e-mail du compte change. */
function poolparty_g4_email_compte_email_change($ancienne, $nouvelle, $nom) {
    $corps = '<p>Bonjour ' . esc_html($nom) . ',</p>'
        . '<p>L\'adresse e-mail de votre compte Pool Party vient d\'être modifiée. Les prochains messages seront envoyés à la nouvelle adresse.</p>'
        . '<p>Si vous êtes à l\'origine de ce changement, vous n\'avez rien à faire. Sinon, contactez immédiatement l\'équipe via la page <a href="' . esc_url(home_url('/contact/')) . '" style="color:#CA8171;">Contact</a>.</p>'
        . '<p>L\'équipe Pool Party</p>';
    poolparty_g4_email_envoyer($ancienne, 'L\'adresse de votre compte a été modifiée', 'Adresse e-mail modifiée', $corps);
}

/** Confirmation de changement de mot de passe. */
function poolparty_g4_email_compte_mdp_change($user) {
    if (!is_email($user->user_email)) {
        return;
    }
    $corps = '<p>Bonjour ' . esc_html($user->display_name) . ',</p>'
        . '<p>Le mot de passe de votre compte Pool Party vient d\'être modifié.</p>'
        . '<p>Si vous n\'êtes pas à l\'origine de ce changement, réinitialisez votre mot de passe depuis la pop-up de connexion (« Mot de passe oublié ? ») puis contactez l\'équipe.</p>'
        . '<p>L\'équipe Pool Party</p>';
    poolparty_g4_email_envoyer($user->user_email, 'Votre mot de passe a été modifié', 'Mot de passe modifié', $corps);
}

/** Confirmation de fermeture du compte, envoyée avant la suppression. */
function poolparty_g4_email_compte_ferme($user) {
    if (!is_email($user->user_email)) {
        return;
    }
    $corps = '<p>Bonjour ' . esc_html($user->display_name) . ',</p>'
        . '<p>Votre compte Pool Party a été fermé à votre demande. Vos favoris et vos préférences ont été supprimés ; vos annonces et réservations passées sont conservées par la plateforme de façon anonyme.</p>'
        . '<p>Merci d\'avoir fait partie de Pool Party. Vous pourrez créer un nouveau compte à tout moment.</p>'
        . '<p>L\'équipe Pool Party</p>';
    poolparty_g4_email_envoyer($user->user_email, 'Votre compte a été fermé', 'Compte fermé', $corps);
}

/* =============================================================
   5. BANDEAU D'ADIEU SUR L'ACCUEIL
   ============================================================= */

/**
 * Petit mot affiché une fois sur l'accueil après la fermeture d'un
 * compte (?pp_compte=ferme). Style embarqué : mon-compte.css ne
 * charge pas sur l'accueil.
 */
function poolparty_g4_mon_compte_adieu() {
    if (!is_front_page() || !isset($_GET['pp_compte']) || $_GET['pp_compte'] !== 'ferme') {
        return;
    }
    ?>
    <div class="pp-compte-adieu" role="status">Votre compte a été fermé. Merci d'avoir fait partie de Pool Party.</div>
    <style>
        .pp-compte-adieu {
            position: fixed;
            left: var(--padding-page-mobile);
            right: 90px;
            bottom: 20px;
            z-index: 1100;
            padding: var(--padding-p3) var(--padding-p2);
            background: linear-gradient(rgba(21, 107, 103, 0.10), rgba(21, 107, 103, 0.10)) var(--color-white);
            color: var(--color-accent-bleu-green);
            border: 1px solid rgba(21, 107, 103, 0.22);
            border-radius: var(--radius-r2);
            box-shadow: var(--shadow-2);
            font-family: var(--font-input);
            font-size: var(--fs-p3);
        }
        @media (min-width: 768px) {
            .pp-compte-adieu { left: auto; max-width: 420px; }
        }
    </style>
    <script>
    setTimeout(function () {
        var note = document.querySelector('.pp-compte-adieu');
        if (note) { note.remove(); }
    }, 8000);
    </script>
    <?php
}
add_action('wp_footer', 'poolparty_g4_mon_compte_adieu');
