<?php
/**
 * Réservations réelles (type de contenu « reservation »).
 * -------------------------------------------------------------
 * Remplace l'ancienne démo localStorage : chaque demande envoyée depuis
 * le tunnel « Confirmer et payer » est désormais enregistrée en base,
 * rattachée au locataire (post_author) et à l'hôte propriétaire du bien
 * (méta pp_hote_id). Le locataire retrouve ses demandes et leur statut
 * sur « Mes réservations » ; l'hôte les accepte ou les refuse depuis sa
 * page « Demandes de réservation ». Deux points d'entrée AJAX sécurisés
 * (jeton pp_reservation) : création et changement de statut.
 */

if (!defined('ABSPATH')) {
    exit;
}

/* =============================================================
   1. TYPE DE CONTENU
   ============================================================= */

function poolparty_g4_enregistrer_cpt_reservation() {
    register_post_type('reservation', array(
        'labels' => array(
            'name'          => 'Réservations',
            'singular_name' => 'Réservation',
            'menu_name'     => 'Réservations',
        ),
        // Non public (aucune page ni archive côté visiteurs) mais visible
        // dans l'administration pour que l'équipe suive les demandes.
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_icon'           => 'dashicons-calendar-alt',
        'menu_position'       => 26,
        'supports'            => array('title', 'author'),
        'exclude_from_search' => true,
        'has_archive'         => false,
        'rewrite'             => false,
        'capability_type'     => 'post',
        'map_meta_cap'        => true,
    ));
}
add_action('init', 'poolparty_g4_enregistrer_cpt_reservation', 5);

/**
 * Libellés lisibles des statuts d'une demande.
 */
function poolparty_g4_statuts_reservation() {
    return array(
        'en-attente'   => 'En attente de confirmation',
        'acceptee'     => 'Confirmée par l\'hôte',
        'refusee'      => 'Non retenue',
        'annulee'      => 'Annulée',
        'annulee-hote' => 'Annulée par l\'hôte',
    );
}

/**
 * Une réservation est passée quand sa date (JJ/MM/AAAA) est antérieure
 * à aujourd'hui ; date absente ou illisible = à venir.
 */
function poolparty_g4_reservation_passee($resa_id) {
    $date = get_post_meta($resa_id, 'pp_date', true);
    $d    = $date ? DateTime::createFromFormat('d/m/Y', $date) : false;
    if (!$d) {
        return false;
    }
    $aujourdhui = new DateTime('today');
    $d->setTime(0, 0);
    return $d < $aujourdhui;
}

/* =============================================================
   2. REQUÊTES
   ============================================================= */

/** Demandes envoyées par un locataire (hors demandes annulées). */
function poolparty_g4_reservations_locataire($user_id) {
    if (!$user_id) {
        return array();
    }
    return get_posts(array(
        'post_type'      => 'reservation',
        'author'         => $user_id,
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => array(array(
            'key'     => 'pp_statut',
            'value'   => 'annulee',
            'compare' => '!=',
        )),
    ));
}

/** Demandes reçues par un hôte (sur ses biens). */
function poolparty_g4_reservations_hote($user_id) {
    if (!$user_id) {
        return array();
    }
    return get_posts(array(
        'post_type'      => 'reservation',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => array(array(
            'key'   => 'pp_hote_id',
            'value' => (int) $user_id,
        )),
    ));
}

/**
 * Met en forme les réservations d'un locataire pour main.js (page « Mes
 * réservations »), dans le même format que l'ancienne démo (mêmes clés).
 */
function poolparty_g4_reservations_pour_js($user_id) {
    $items = array();
    foreach (poolparty_g4_reservations_locataire($user_id) as $post) {
        $bien_id   = (int) get_post_meta($post->ID, 'pp_bien_id', true);
        $hote_data = $bien_id ? poolparty_g4_get_hote(poolparty_g4_meta($bien_id, 'id_hote')) : null;
        $items[]   = array(
            'id'      => $post->ID,
            'titre'   => $bien_id ? get_the_title($bien_id) : $post->post_title,
            'image'   => $bien_id ? poolparty_g4_image_url($bien_id) : '',
            'alt'     => $bien_id ? poolparty_g4_meta($bien_id, 'alt') : '',
            'lien'    => $bien_id ? get_permalink($bien_id) : '',
            'hote'    => ($hote_data && !empty($hote_data['prenom'])) ? $hote_data['prenom'] : '',
            'date'    => get_post_meta($post->ID, 'pp_date', true),
            'creneau' => get_post_meta($post->ID, 'pp_creneau', true),
            'invites' => get_post_meta($post->ID, 'pp_invites', true),
            'formule' => get_post_meta($post->ID, 'pp_formule', true),
            'total'   => get_post_meta($post->ID, 'pp_total', true),
            'statut'  => get_post_meta($post->ID, 'pp_statut', true),
        );
    }
    return $items;
}

/**
 * Met en forme les demandes reçues par un hôte pour le JS de la page
 * « Mes réservations » V2 (vue Hôte). Jamais d'e-mail du locataire :
 * le contact passe par la messagerie de la plateforme.
 */
function poolparty_g4_reservations_hote_pour_js($user_id) {
    $items = array();
    foreach (poolparty_g4_reservations_hote($user_id) as $post) {
        $bien_id   = (int) get_post_meta($post->ID, 'pp_bien_id', true);
        $locataire = get_userdata((int) $post->post_author);
        $items[]   = array(
            'id'        => $post->ID,
            'titre'     => $bien_id ? get_the_title($bien_id) : $post->post_title,
            'image'     => $bien_id ? poolparty_g4_image_url($bien_id) : '',
            'alt'       => $bien_id ? poolparty_g4_meta($bien_id, 'alt') : '',
            'lien'      => $bien_id ? get_permalink($bien_id) : '',
            'locataire' => $locataire ? $locataire->display_name : '',
            'date'      => get_post_meta($post->ID, 'pp_date', true),
            'creneau'   => get_post_meta($post->ID, 'pp_creneau', true),
            'invites'   => get_post_meta($post->ID, 'pp_invites', true),
            'total'     => get_post_meta($post->ID, 'pp_total', true),
            'message'   => get_post_meta($post->ID, 'pp_message', true),
            'statut'    => get_post_meta($post->ID, 'pp_statut', true),
        );
    }
    return $items;
}

/**
 * Total d'une demande, calculé côté serveur à partir du prix réel du
 * bien (méta prix_heure) et non des montants envoyés par le navigateur.
 * Reproduit exactement le barème de la fiche bien (single-bien.php) et
 * du tunnel : forfaits demi-journée = 4 h, journée = 7,5 h, formule à
 * l'heure facturée par occupant ; frais de service de 15 % ; garantie
 * annulation optionnelle de 6 € (data-garantie du gabarit réservation).
 * Retourne un montant numérique (float).
 */
function poolparty_g4_reservation_total($bien_id, $formule_type, $occupants, $garantie_oui) {
    $prix_heure   = (float) poolparty_g4_meta($bien_id, 'prix_heure');
    $prix_demi    = round($prix_heure * 4);
    $prix_journee = round($prix_heure * 7.5);
    $occupants    = max(1, (int) $occupants);

    switch ($formule_type) {
        case 'heure':
            $sous_total = $prix_heure * $occupants;
            break;
        case 'journee':
            $sous_total = $prix_journee;
            break;
        case 'demi-journee':
        default:
            $sous_total = $prix_demi;
            break;
    }

    $frais    = $sous_total * 0.15;
    $garantie = $garantie_oui ? 6 : 0;
    return $sous_total + $frais + $garantie;
}

/**
 * Met en forme un montant comme le tunnel (« 204,00€ », virgule décimale,
 * sans séparateur de milliers).
 */
function poolparty_g4_reservation_format_montant($montant) {
    return number_format((float) $montant, 2, ',', '') . '€';
}

/* =============================================================
   3. AJAX : création d'une demande
   ============================================================= */

function poolparty_g4_ajax_creer_reservation() {
    check_ajax_referer('pp_reservation', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connectez-vous pour envoyer votre demande.'), 401);
    }

    $bien_id = isset($_POST['bien_id']) ? absint($_POST['bien_id']) : 0;
    if (!$bien_id || get_post_type($bien_id) !== 'bien' || get_post_status($bien_id) !== 'publish') {
        wp_send_json_error(array('message' => 'Cet espace n\'est plus disponible.'), 400);
    }

    $user    = wp_get_current_user();
    $hote_id = (int) get_post_field('post_author', $bien_id);
    $titre   = get_the_title($bien_id);

    // Total recalculé côté serveur : jamais celui envoyé par le navigateur.
    // Le type de formule et le nombre d'occupants sont les seules valeurs
    // reçues qui entrent dans le calcul ; le prix vient de la base.
    $formule_type  = isset($_POST['formule_type']) ? sanitize_key($_POST['formule_type']) : '';
    $occupants     = isset($_POST['occupants']) ? absint($_POST['occupants']) : 0;
    $garantie_oui  = (isset($_POST['garantie']) && sanitize_text_field(wp_unslash($_POST['garantie'])) === 'Oui');
    $total_calcule = poolparty_g4_reservation_total($bien_id, $formule_type, $occupants, $garantie_oui);

    $champs = array(
        'pp_date'     => isset($_POST['date']) ? sanitize_text_field(wp_unslash($_POST['date'])) : '',
        'pp_creneau'  => isset($_POST['creneau']) ? sanitize_text_field(wp_unslash($_POST['creneau'])) : '',
        'pp_invites'  => isset($_POST['invites']) ? sanitize_text_field(wp_unslash($_POST['invites'])) : '',
        'pp_formule'  => isset($_POST['formule']) ? sanitize_text_field(wp_unslash($_POST['formule'])) : '',
        'pp_total'    => poolparty_g4_reservation_format_montant($total_calcule),
        'pp_echeance' => isset($_POST['echeance']) ? sanitize_text_field(wp_unslash($_POST['echeance'])) : '',
        'pp_paiement' => isset($_POST['paiement']) ? sanitize_text_field(wp_unslash($_POST['paiement'])) : '',
        'pp_garantie' => isset($_POST['garantie']) ? sanitize_text_field(wp_unslash($_POST['garantie'])) : '',
        'pp_message'  => isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '',
    );

    $resa_id = wp_insert_post(array(
        'post_type'   => 'reservation',
        'post_status' => 'publish',
        'post_author' => $user->ID,
        'post_title'  => sprintf('%s, %s', $titre, ($champs['pp_date'] ?: 'sans date')),
    ), true);

    if (is_wp_error($resa_id) || !$resa_id) {
        wp_send_json_error(array('message' => 'Impossible d\'enregistrer votre demande. Réessayez.'), 500);
    }

    update_post_meta($resa_id, 'pp_bien_id', $bien_id);
    update_post_meta($resa_id, 'pp_hote_id', $hote_id);
    update_post_meta($resa_id, 'pp_statut', 'en-attente');
    foreach ($champs as $cle => $valeur) {
        update_post_meta($resa_id, $cle, $valeur);
    }

    poolparty_g4_email_reservation_nouvelle($resa_id);

    $hote_data   = poolparty_g4_get_hote(poolparty_g4_meta($bien_id, 'id_hote'));
    $hote_prenom = ($hote_data && !empty($hote_data['prenom'])) ? $hote_data['prenom'] : 'Votre hôte';

    wp_send_json_success(array(
        'hote'    => $hote_prenom,
        'date'    => $champs['pp_date'],
        'creneau' => $champs['pp_creneau'],
    ));
}
add_action('wp_ajax_pp_creer_reservation', 'poolparty_g4_ajax_creer_reservation');
add_action('wp_ajax_nopriv_pp_creer_reservation', 'poolparty_g4_ajax_creer_reservation');

/* =============================================================
   4. AJAX : accepter / refuser / annuler
   ============================================================= */

function poolparty_g4_ajax_maj_reservation() {
    check_ajax_referer('pp_reservation', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connectez-vous pour continuer.'), 401);
    }

    $resa_id = isset($_POST['resa_id']) ? absint($_POST['resa_id']) : 0;
    $action  = isset($_POST['statut']) ? sanitize_key($_POST['statut']) : '';
    $resa    = $resa_id ? get_post($resa_id) : null;

    if (!$resa || $resa->post_type !== 'reservation') {
        wp_send_json_error(array('message' => 'Demande introuvable.'), 404);
    }

    $map = array(
        'accepter'     => 'acceptee',
        'refuser'      => 'refusee',
        'annuler'      => 'annulee',
        'annuler-hote' => 'annulee-hote',
    );
    if (!isset($map[$action])) {
        wp_send_json_error(array('message' => 'Action inconnue.'), 400);
    }

    $user_id = get_current_user_id();
    $hote_id = (int) get_post_meta($resa_id, 'pp_hote_id', true);
    $auteur  = (int) $resa->post_author;
    $admin   = current_user_can('manage_options');

    // Accepter / refuser / annuler côté hôte : réservé à l'hôte du bien.
    // Annuler une demande : au locataire.
    if ($action === 'annuler') {
        if ($user_id !== $auteur && !$admin) {
            wp_send_json_error(array('message' => 'Vous ne pouvez pas annuler cette demande.'), 403);
        }
    } else {
        if ($user_id !== $hote_id && !$admin) {
            wp_send_json_error(array('message' => 'Seul l\'hôte de cet espace peut répondre à cette demande.'), 403);
        }
    }

    // Annulation par l'hôte : uniquement une réservation confirmée, avec
    // une raison obligatoire, transmise au locataire.
    $raison = '';
    if ($action === 'annuler-hote') {
        if (get_post_meta($resa_id, 'pp_statut', true) !== 'acceptee') {
            wp_send_json_error(array('message' => 'Seule une réservation confirmée peut être annulée.'), 400);
        }
        $raison = isset($_POST['raison']) ? sanitize_textarea_field(wp_unslash($_POST['raison'])) : '';
        if ($raison === '') {
            wp_send_json_error(array('message' => 'Expliquez la raison de l\'annulation au locataire.'), 400);
        }
        update_post_meta($resa_id, 'pp_annulation_raison', $raison);
    }

    update_post_meta($resa_id, 'pp_statut', $map[$action]);

    if ($action === 'annuler-hote') {
        poolparty_g4_email_reservation_annulee_hote($resa_id, $raison);
    } elseif ($action !== 'annuler') {
        poolparty_g4_email_reservation_statut($resa_id, $map[$action]);
    }

    wp_send_json_success(array('statut' => $map[$action]));
}
add_action('wp_ajax_pp_maj_reservation', 'poolparty_g4_ajax_maj_reservation');

/* =============================================================
   5. E-MAILS
   ============================================================= */

/**
 * Nouvelle demande : accusé au locataire + notification à l'hôte.
 * Aucune coordonnée d'un membre n'est transmise à l'autre : les
 * e-mails renvoient vers « Mes réservations » et la messagerie interne.
 */
function poolparty_g4_email_reservation_nouvelle($resa_id) {
    $bien_id = (int) get_post_meta($resa_id, 'pp_bien_id', true);
    $hote_id = (int) get_post_meta($resa_id, 'pp_hote_id', true);
    $auteur  = get_userdata((int) get_post_field('post_author', $resa_id));
    $titre   = $bien_id ? get_the_title($bien_id) : 'un espace Pool Party';
    $date    = get_post_meta($resa_id, 'pp_date', true);
    $creneau = get_post_meta($resa_id, 'pp_creneau', true);
    $invites = get_post_meta($resa_id, 'pp_invites', true);
    $total   = get_post_meta($resa_id, 'pp_total', true);
    $message = get_post_meta($resa_id, 'pp_message', true);

    // Accusé de réception au locataire (préférence « Réservations » de la
    // page Mon compte ; la réponse de l'hôte, elle, est toujours envoyée).
    if ($auteur && is_email($auteur->user_email) && poolparty_g4_notif_active($auteur->ID, 'reservation')) {
        $corps = '<p>Bonjour ' . esc_html($auteur->display_name) . ',</p>'
            . '<p>Votre demande de réservation pour <strong>' . esc_html($titre) . '</strong> a bien été envoyée à l\'hôte.</p>'
            . '<p><strong>Date :</strong> ' . esc_html($date . ($creneau ? ' · ' . $creneau : '')) . '<br>'
            . '<strong>Invités :</strong> ' . esc_html($invites) . '<br>'
            . '<strong>Total :</strong> ' . esc_html($total) . '</p>'
            . '<p>L\'hôte dispose de 24h pour confirmer la disponibilité. Vous ne serez débité qu\'après sa confirmation. '
            . 'Vous pouvez suivre le statut de votre demande dans « Mes réservations ».</p>';
        poolparty_g4_email_envoyer(
            $auteur->user_email,
            'Votre demande de réservation Pool Party',
            'Demande envoyée',
            $corps
        );
    }

    // Notification à l'hôte (même préférence « Réservations »).
    $hote = get_userdata($hote_id);
    if ($hote && is_email($hote->user_email) && poolparty_g4_notif_active($hote_id, 'reservation')) {
        $corps = '<p>Bonjour ' . esc_html($hote->display_name) . ',</p>'
            . '<p>Vous avez reçu une nouvelle demande de réservation pour <strong>' . esc_html($titre) . '</strong>.</p>'
            . '<p><strong>De :</strong> ' . esc_html($auteur ? $auteur->display_name : 'Un membre') . '<br>'
            . '<strong>Date :</strong> ' . esc_html($date . ($creneau ? ' · ' . $creneau : '')) . '<br>'
            . '<strong>Invités :</strong> ' . esc_html($invites) . '<br>'
            . '<strong>Total :</strong> ' . esc_html($total) . '</p>'
            . ($message ? '<p><strong>Message :</strong><br>' . nl2br(esc_html($message)) . '</p>' : '')
            . '<p>Retrouvez cette demande dans « Mes réservations », vue Hôte, pour l\'accepter ou la refuser. '
            . 'Pour échanger avec le locataire, passez par la messagerie Pool Party : vos coordonnées restent privées.</p>'
            . '<p><a href="' . esc_url(home_url('/mes-reservations/?vue=hote')) . '" style="color:#CA8171;">Voir la demande</a></p>';
        poolparty_g4_email_envoyer(
            $hote->user_email,
            'Nouvelle demande de réservation',
            'Nouvelle demande',
            $corps
        );
    }
}

/** Réponse de l'hôte : informe le locataire de l'acceptation ou du refus. */
function poolparty_g4_email_reservation_statut($resa_id, $statut) {
    $auteur  = get_userdata((int) get_post_field('post_author', $resa_id));
    if (!$auteur || !is_email($auteur->user_email)) {
        return;
    }
    $bien_id = (int) get_post_meta($resa_id, 'pp_bien_id', true);
    $titre   = $bien_id ? get_the_title($bien_id) : 'votre espace';
    $date    = get_post_meta($resa_id, 'pp_date', true);
    $creneau = get_post_meta($resa_id, 'pp_creneau', true);

    if ($statut === 'acceptee') {
        $sujet = 'Votre réservation est confirmée !';
        $corps = '<p>Bonjour ' . esc_html($auteur->display_name) . ',</p>'
            . '<p>Bonne nouvelle : l\'hôte a <strong>confirmé</strong> votre réservation pour <strong>' . esc_html($titre) . '</strong> le '
            . esc_html($date . ($creneau ? ' · ' . $creneau : '')) . '.</p>'
            . '<p>Rendez-vous dans « Mes réservations » pour retrouver tous les détails.</p>';
    } else {
        $sujet = 'Réponse à votre demande de réservation';
        $corps = '<p>Bonjour ' . esc_html($auteur->display_name) . ',</p>'
            . '<p>L\'hôte n\'a malheureusement pas pu retenir votre demande pour <strong>' . esc_html($titre) . '</strong> le '
            . esc_html($date . ($creneau ? ' · ' . $creneau : '')) . '. Aucun montant ne vous sera débité.</p>'
            . '<p>D\'autres espaces vous attendent sur Pool Party.</p>';
    }
    poolparty_g4_email_envoyer($auteur->user_email, $sujet, 'Pool Party', $corps);
}

/**
 * Annulation par l'hôte : prévient le locataire avec la raison donnée
 * (le message saisi par l'hôte, jamais ses coordonnées).
 */
function poolparty_g4_email_reservation_annulee_hote($resa_id, $raison) {
    $auteur = get_userdata((int) get_post_field('post_author', $resa_id));
    if (!$auteur || !is_email($auteur->user_email)) {
        return;
    }
    $bien_id = (int) get_post_meta($resa_id, 'pp_bien_id', true);
    $titre   = $bien_id ? get_the_title($bien_id) : 'votre espace';
    $date    = get_post_meta($resa_id, 'pp_date', true);
    $creneau = get_post_meta($resa_id, 'pp_creneau', true);

    $corps = '<p>Bonjour ' . esc_html($auteur->display_name) . ',</p>'
        . '<p>Votre hôte a dû <strong>annuler</strong> votre réservation pour <strong>' . esc_html($titre) . '</strong> le '
        . esc_html($date . ($creneau ? ' · ' . $creneau : '')) . '. Vous serez intégralement remboursé.</p>'
        . '<p><strong>Son message :</strong><br>' . nl2br(esc_html($raison)) . '</p>'
        . '<p>D\'autres espaces vous attendent sur Pool Party.</p>';
    poolparty_g4_email_envoyer($auteur->user_email, 'Votre réservation a été annulée', 'Pool Party', $corps);
}

/* =============================================================
   6. AFFICHAGE : carte d'une demande (page hôte)
   ============================================================= */

/**
 * Affiche une demande sous forme de carte, dans le style .reservation-card
 * du site. Sur la page hôte : identité du locataire, détails, et boutons
 * Accepter / Refuser tant que la demande est en attente.
 */
function poolparty_g4_carte_demande($resa) {
    $bien_id = (int) get_post_meta($resa->ID, 'pp_bien_id', true);
    $statut  = get_post_meta($resa->ID, 'pp_statut', true);
    $auteur  = get_userdata((int) $resa->post_author);
    $labels  = poolparty_g4_statuts_reservation();

    $titre   = $bien_id ? get_the_title($bien_id) : $resa->post_title;
    $lien    = $bien_id ? get_permalink($bien_id) : '#';
    $image   = $bien_id ? poolparty_g4_image_url($bien_id) : '';
    $alt     = $bien_id ? poolparty_g4_meta($bien_id, 'alt') : '';
    $date    = get_post_meta($resa->ID, 'pp_date', true);
    $creneau = get_post_meta($resa->ID, 'pp_creneau', true);
    $invites = get_post_meta($resa->ID, 'pp_invites', true);
    $total   = get_post_meta($resa->ID, 'pp_total', true);
    $message = get_post_meta($resa->ID, 'pp_message', true);

    $classe_statut = 'tag--top-vente';
    if ($statut === 'acceptee') {
        $classe_statut = 'tag--succes';
    } elseif ($statut === 'refusee') {
        $classe_statut = '';
    }
    $label_statut = isset($labels[$statut]) ? $labels[$statut] : 'En attente de confirmation';
    ?>
    <article class="reservation-card" data-resa-id="<?php echo esc_attr($resa->ID); ?>">
        <a class="reservation-card__media" href="<?php echo esc_url($lien); ?>">
            <?php if ($image) : ?>
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy" decoding="async">
            <?php endif; ?>
            <span class="tag <?php echo esc_attr($classe_statut); ?> reservation-card__statut"><?php echo esc_html($label_statut); ?></span>
        </a>
        <div class="reservation-card__body">
            <div class="reservation-card__head">
                <h3 class="reservation-card__title"><a href="<?php echo esc_url($lien); ?>"><?php echo esc_html($titre); ?></a></h3>
                <?php if ($auteur) : ?>
                    <p class="reservation-card__hote">Demande de <?php echo esc_html($auteur->display_name); ?></p>
                <?php endif; ?>
            </div>
            <dl class="reservation-card__infos">
                <div class="reservation-card__info">
                    <dt>Date et créneau</dt>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/></svg>
                    <dd><?php echo esc_html($date . ($creneau ? ' · ' . $creneau : '')); ?></dd>
                </div>
                <div class="reservation-card__info">
                    <dt>Invités</dt>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <dd><?php echo esc_html($invites); ?></dd>
                </div>
                <?php if ($message) : ?>
                <div class="reservation-card__info">
                    <dt>Message</dt>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <dd><?php echo esc_html($message); ?></dd>
                </div>
                <?php endif; ?>
            </dl>
            <div class="reservation-card__foot">
                <?php if ($total) : ?>
                    <p class="reservation-card__total">Total<strong><?php echo esc_html($total); ?></strong></p>
                <?php endif; ?>
                <div class="reservation-card__actions">
                    <?php if ($statut === 'en-attente' || $statut === '') : ?>
                        <button type="button" class="btn btn-primary btn-small js-demande-action" data-resa-id="<?php echo esc_attr($resa->ID); ?>" data-action="accepter">Accepter</button>
                        <button type="button" class="btn btn-tertiary btn-small js-demande-action" data-resa-id="<?php echo esc_attr($resa->ID); ?>" data-action="refuser">Refuser</button>
                    <?php else : ?>
                        <a class="btn btn-tertiary btn-small" href="<?php echo esc_url($lien); ?>">Voir l'annonce</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </article>
    <?php
}
