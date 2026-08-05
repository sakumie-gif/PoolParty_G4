<?php
/**
 * Incidents sur les réservations (type de contenu pp_incident).
 * -------------------------------------------------------------
 * Un membre, hôte ou locataire, signale un problème sur une réservation
 * confirmée depuis la page Mes réservations (motif, description, photos).
 * L'incident est enregistré en base, rattaché à la réservation, à
 * l'annonce et aux deux membres, puis traité par l'équipe depuis la
 * console (statut ouvert / en cours de traitement / clos, note interne).
 *
 * E-mails : notification à l'équipe, accusé au déclarant, information
 * neutre à l'autre membre (sans le motif ni le détail), puis e-mail de
 * clôture aux deux membres quand l'équipe clôt le dossier. Jamais de
 * coordonnées d'un membre transmises à un autre.
 */

if (!defined('ABSPATH')) {
    exit;
}

/* =============================================================
   1. TYPE DE CONTENU ET RÉFÉRENTIELS
   ============================================================= */

function poolparty_g4_enregistrer_cpt_incident() {
    register_post_type('pp_incident', array(
        'labels' => array(
            'name'          => 'Incidents',
            'singular_name' => 'Incident',
        ),
        // Privé et sans écran WordPress : tout passe par la console du site.
        'public'              => false,
        'show_ui'             => false,
        'exclude_from_search' => true,
        'supports'            => array('title', 'editor', 'author'),
    ));
}
add_action('init', 'poolparty_g4_enregistrer_cpt_incident', 5);

/** Motifs proposés au déclarant : clé => libellé. */
function poolparty_g4_incident_motifs() {
    return array(
        'degradation'  => 'Dégradation',
        'non-conforme' => 'Espace non conforme à l\'annonce',
        'acces'        => 'Accès impossible',
        'securite'     => 'Problème de sécurité',
        'comportement' => 'Comportement inapproprié',
        'autre'        => 'Autre',
    );
}

/** Statuts de traitement : clé => libellé. */
function poolparty_g4_incident_statuts() {
    return array(
        'ouvert'   => 'Ouvert',
        'en-cours' => 'En cours de traitement',
        'clos'     => 'Clos',
    );
}

/* =============================================================
   2. REQUÊTES
   ============================================================= */

/** Incidents pour la console, du plus récent au plus ancien. */
function poolparty_g4_incidents_liste($statut = '') {
    $args = array(
        'post_type'      => 'pp_incident',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    if ($statut !== '' && array_key_exists($statut, poolparty_g4_incident_statuts())) {
        $args['meta_query'] = array(array('key' => 'pp_statut', 'value' => $statut));
    }
    return get_posts($args);
}

/** Nombre d'incidents ouverts (tuile du tableau de bord). */
function poolparty_g4_incidents_ouverts_nb() {
    $q = new WP_Query(array(
        'post_type'      => 'pp_incident',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => array(array('key' => 'pp_statut', 'value' => 'ouvert')),
    ));
    return (int) $q->found_posts;
}

/**
 * Le déclarant a-t-il déjà un signalement non clos sur cette réservation ?
 * Garde anti-doublon : un seul dossier en cours par réservation et par
 * déclarant.
 */
function poolparty_g4_incident_actif_existe($resa_id, $declarant_id) {
    $q = new WP_Query(array(
        'post_type'      => 'pp_incident',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => array(
            'relation' => 'AND',
            array('key' => 'pp_resa_id', 'value' => (int) $resa_id),
            array('key' => 'pp_declarant_id', 'value' => (int) $declarant_id),
            array('key' => 'pp_statut', 'value' => 'clos', 'compare' => '!='),
        ),
    ));
    return $q->found_posts > 0;
}

/**
 * Réservations sur lesquelles le membre a un signalement non clos
 * (état initial des boutons de la page Mes réservations).
 */
function poolparty_g4_incidents_resas_declarees($user_id) {
    $incidents = get_posts(array(
        'post_type'      => 'pp_incident',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => array(
            'relation' => 'AND',
            array('key' => 'pp_declarant_id', 'value' => (int) $user_id),
            array('key' => 'pp_statut', 'value' => 'clos', 'compare' => '!='),
        ),
    ));
    $resas = array();
    foreach ($incidents as $incident_id) {
        $resas[] = (int) get_post_meta($incident_id, 'pp_resa_id', true);
    }
    return array_values(array_filter(array_unique($resas)));
}

/* =============================================================
   3. AJAX : créer un signalement
   ============================================================= */

function poolparty_g4_ajax_creer_incident() {
    check_ajax_referer('pp_incident', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connectez-vous pour signaler un problème.'), 401);
    }

    $user_id = get_current_user_id();
    $resa_id = isset($_POST['resa_id']) ? absint($_POST['resa_id']) : 0;
    $resa    = $resa_id ? get_post($resa_id) : null;

    if (!$resa || $resa->post_type !== 'reservation') {
        wp_send_json_error(array('message' => 'Réservation introuvable.'), 404);
    }
    if (get_post_meta($resa_id, 'pp_statut', true) !== 'acceptee') {
        wp_send_json_error(array('message' => 'Un signalement ne peut porter que sur une réservation confirmée.'), 400);
    }

    // Propriété : le déclarant est le locataire OU l'hôte de CETTE
    // réservation ; l'autre devient le membre concerné.
    $locataire_id = (int) $resa->post_author;
    $hote_id      = (int) get_post_meta($resa_id, 'pp_hote_id', true);
    if ($user_id === $locataire_id) {
        $role  = 'locataire';
        $autre = $hote_id;
    } elseif ($user_id === $hote_id) {
        $role  = 'hote';
        $autre = $locataire_id;
    } else {
        wp_send_json_error(array('message' => 'Vous n\'êtes pas concerné par cette réservation.'), 403);
    }

    if (poolparty_g4_incident_actif_existe($resa_id, $user_id)) {
        wp_send_json_error(array('message' => 'Vous avez déjà un signalement en cours sur cette réservation.'), 400);
    }

    $motif = isset($_POST['motif']) ? sanitize_key($_POST['motif']) : '';
    if (!array_key_exists($motif, poolparty_g4_incident_motifs())) {
        wp_send_json_error(array('message' => 'Choisissez un motif dans la liste.'), 400);
    }
    $description = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '';
    if (trim($description) === '') {
        wp_send_json_error(array('message' => 'Décrivez ce qui s\'est passé.'), 400);
    }

    // Photos : images uniquement, 4 au maximum. On filtre $_FILES avant
    // de réutiliser l'upload générique du tunnel Proposer.
    if (!empty($_FILES['photos']) && is_array($_FILES['photos']['name'])) {
        $filtre = array('name' => array(), 'type' => array(), 'tmp_name' => array(), 'error' => array(), 'size' => array());
        $gardees = 0;
        $total   = count($_FILES['photos']['name']);
        for ($i = 0; $i < $total && $gardees < 4; $i++) {
            if ((int) $_FILES['photos']['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            if (strpos((string) $_FILES['photos']['type'][$i], 'image/') !== 0) {
                continue;
            }
            foreach (array_keys($filtre) as $cle) {
                $filtre[$cle][] = $_FILES['photos'][$cle][$i];
            }
            $gardees++;
        }
        $_FILES['photos'] = $filtre;
    }

    $declarant = get_userdata($user_id);
    $bien_id   = (int) get_post_meta($resa_id, 'pp_bien_id', true);
    $titre     = sprintf(
        '%s, %s, %s',
        $bien_id ? get_the_title($bien_id) : $resa->post_title,
        get_post_meta($resa_id, 'pp_date', true) ?: 'sans date',
        $declarant ? $declarant->display_name : 'membre'
    );

    $incident_id = wp_insert_post(array(
        'post_type'    => 'pp_incident',
        'post_status'  => 'publish',
        'post_author'  => $user_id,
        'post_title'   => $titre,
        'post_content' => $description,
    ), true);

    if (is_wp_error($incident_id) || !$incident_id) {
        wp_send_json_error(array('message' => 'Impossible d\'enregistrer votre signalement. Réessayez.'), 500);
    }

    update_post_meta($incident_id, 'pp_resa_id', $resa_id);
    update_post_meta($incident_id, 'pp_bien_id', $bien_id);
    update_post_meta($incident_id, 'pp_declarant_id', $user_id);
    update_post_meta($incident_id, 'pp_autre_id', $autre);
    update_post_meta($incident_id, 'pp_role_declarant', $role);
    update_post_meta($incident_id, 'pp_motif', $motif);
    update_post_meta($incident_id, 'pp_statut', 'ouvert');

    $photos = function_exists('poolparty_g4_uploader_photos_bien') ? poolparty_g4_uploader_photos_bien($incident_id) : array();
    if ($photos) {
        update_post_meta($incident_id, 'pp_photos', implode(',', $photos));
    }

    poolparty_g4_email_incident_nouveau($incident_id);

    wp_send_json_success(array(
        'message' => 'Votre signalement a bien été transmis à l\'équipe Pool Party. Nous revenons vers vous rapidement.',
    ));
}
add_action('wp_ajax_pp_creer_incident', 'poolparty_g4_ajax_creer_incident');

/* =============================================================
   4. E-MAILS
   ============================================================= */

/** Envois au dépôt : équipe, accusé au déclarant, information à l'autre membre. */
function poolparty_g4_email_incident_nouveau($incident_id) {
    $motifs    = poolparty_g4_incident_motifs();
    $motif     = get_post_meta($incident_id, 'pp_motif', true);
    $bien_id   = (int) get_post_meta($incident_id, 'pp_bien_id', true);
    $resa_id   = (int) get_post_meta($incident_id, 'pp_resa_id', true);
    $declarant = get_userdata((int) get_post_meta($incident_id, 'pp_declarant_id', true));
    $autre     = get_userdata((int) get_post_meta($incident_id, 'pp_autre_id', true));
    $titre     = $bien_id ? get_the_title($bien_id) : 'un espace Pool Party';
    $date      = get_post_meta($resa_id, 'pp_date', true);
    $extrait   = wp_trim_words(get_post_field('post_content', $incident_id), 40);

    // L'équipe : tout le contexte et le lien vers la console.
    $admin = get_option('admin_email');
    if ($admin) {
        $corps = '<p>Un nouveau signalement vient d\'être déposé :</p>'
            . '<p style="margin:16px 0;padding:14px 16px;background:#faf7f2;border-radius:10px;">'
            . '<strong>Espace :</strong> ' . esc_html($titre) . '<br>'
            . '<strong>Venue du :</strong> ' . esc_html($date) . '<br>'
            . '<strong>Déclarant :</strong> ' . esc_html($declarant ? $declarant->display_name : 'Un membre') . '<br>'
            . '<strong>Motif :</strong> ' . esc_html(isset($motifs[$motif]) ? $motifs[$motif] : $motif) . '<br>'
            . '<strong>Description :</strong><br>' . esc_html($extrait)
            . '</p>'
            . '<p><a href="' . esc_url(add_query_arg('section', 'incidents', home_url('/administration/'))) . '" style="color:#CA8171;">Traiter le signalement dans la console</a></p>';
        poolparty_g4_email_envoyer($admin, 'Nouveau signalement d\'incident', 'Nouveau signalement', $corps);
    }

    // Accusé de réception au déclarant (toujours envoyé : démarche engagée).
    if ($declarant && is_email($declarant->user_email)) {
        $corps = '<p>Bonjour ' . esc_html($declarant->display_name) . ',</p>'
            . '<p>Votre signalement concernant <strong>' . esc_html($titre) . '</strong>, venue du ' . esc_html($date) . ', a bien été reçu.</p>'
            . '<p>Notre équipe l\'examine et revient vers vous rapidement. Vous pouvez compléter votre dossier à tout moment via la messagerie ou la page Contact.</p>'
            . '<p>L\'équipe Pool Party</p>';
        poolparty_g4_email_envoyer($declarant->user_email, 'Votre signalement a bien été reçu', 'Signalement reçu', $corps);
    }

    // L'autre membre : information neutre, sans le motif ni la description.
    if ($autre && is_email($autre->user_email)) {
        $corps = '<p>Bonjour ' . esc_html($autre->display_name) . ',</p>'
            . '<p>Un signalement a été déposé au sujet de la réservation de <strong>' . esc_html($titre) . '</strong> du ' . esc_html($date) . '.</p>'
            . '<p>L\'équipe Pool Party examine la situation et reviendra vers vous si nécessaire. Vous n\'avez rien à faire pour le moment.</p>'
            . '<p>L\'équipe Pool Party</p>';
        poolparty_g4_email_envoyer($autre->user_email, 'Un signalement concerne une de vos réservations', 'Signalement en cours d\'examen', $corps);
    }
}

/** Clôture : e-mail aux deux membres, avec le message de l'équipe s'il existe. */
function poolparty_g4_email_incident_cloture($incident_id, $message = '') {
    $bien_id = (int) get_post_meta($incident_id, 'pp_bien_id', true);
    $resa_id = (int) get_post_meta($incident_id, 'pp_resa_id', true);
    $titre   = $bien_id ? get_the_title($bien_id) : 'un espace Pool Party';
    $date    = get_post_meta($resa_id, 'pp_date', true);

    $corps = '<p>Bonjour,</p>'
        . '<p>L\'équipe Pool Party a examiné le signalement concernant la réservation de <strong>' . esc_html($titre) . '</strong> du ' . esc_html($date) . '. Le dossier est désormais clos.</p>';
    if (trim($message) !== '') {
        $corps .= '<p style="margin:16px 0;padding:14px 16px;background:#faf7f2;border-radius:10px;"><strong>Message de l\'équipe :</strong><br>' . nl2br(esc_html($message)) . '</p>';
    }
    $corps .= '<p>Si vous avez la moindre question, contactez-nous via la page <a href="' . esc_url(home_url('/contact/')) . '" style="color:#CA8171;">Contact</a>.</p>'
        . '<p>L\'équipe Pool Party</p>';

    foreach (array('pp_declarant_id', 'pp_autre_id') as $meta) {
        $membre = get_userdata((int) get_post_meta($incident_id, $meta, true));
        if ($membre && is_email($membre->user_email)) {
            poolparty_g4_email_envoyer($membre->user_email, 'Le signalement concernant votre réservation est clos', 'Signalement clos', $corps);
        }
    }
}
