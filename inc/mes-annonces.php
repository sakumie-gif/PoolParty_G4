<?php
/**
 * Espace membre « Mes annonces » (page /mes-annonces/).
 * -------------------------------------------------------------
 * L'hôte retrouve les annonces qu'il a déposées (en ligne, en attente de
 * validation, refusées avec motif) et peut les modifier : description,
 * prix, capacité, équipements, jours d'ouverture, dates d'indisponibilité
 * et ajout de photos.
 *
 * Règles :
 *  - la modification est bloquée tant qu'une réservation est en cours sur
 *    l'annonce (demande en attente, ou venue confirmée à venir) ;
 *  - modifier une annonce refusée la renvoie en validation (pending), le
 *    circuit admin reprend ; une annonce en ligne reste en ligne ;
 *  - seul le propriétaire (ou un administrateur) peut modifier ;
 *  - l'entrée de menu « Mes annonces » n'apparaît que si le membre a au
 *    moins une annonce (voir header.php).
 */

if (!defined('ABSPATH')) {
    exit;
}

/* =============================================================
   1. REQUÊTES
   ============================================================= */

/** Les annonces d'un membre, tous statuts utiles confondus. */
function poolparty_g4_mes_annonces($user_id) {
    if (!$user_id) {
        return array();
    }
    return get_posts(array(
        'post_type'      => 'bien',
        'author'         => (int) $user_id,
        'post_status'    => array('publish', 'pending', 'draft'),
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));
}

/** Le membre connecté a-t-il au moins une annonce ? (entrée de menu) */
function poolparty_g4_membre_a_des_annonces() {
    if (!is_user_logged_in()) {
        return false;
    }
    $q = new WP_Query(array(
        'post_type'      => 'bien',
        'author'         => get_current_user_id(),
        'post_status'    => array('publish', 'pending', 'draft'),
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ));
    return $q->found_posts > 0;
}

/**
 * Une réservation est-elle en cours sur cette annonce ? En cours = demande
 * en attente de réponse, ou venue confirmée pas encore passée. Dans ce cas
 * la modification est bloquée (les conditions vues par le locataire ne
 * doivent pas changer sous ses pieds).
 */
function poolparty_g4_bien_a_resa_en_cours($bien_id) {
    $resas = get_posts(array(
        'post_type'      => 'reservation',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_query'     => array(
            array('key' => 'pp_bien_id', 'value' => (int) $bien_id),
            array('key' => 'pp_statut', 'value' => array('en-attente', 'acceptee'), 'compare' => 'IN'),
        ),
    ));
    foreach ($resas as $resa_id) {
        $statut = get_post_meta($resa_id, 'pp_statut', true);
        if ($statut === 'en-attente') {
            return true;
        }
        if ($statut === 'acceptee' && !poolparty_g4_reservation_passee($resa_id)) {
            return true;
        }
    }
    return false;
}

/** Libellé et classe de badge du statut d'une annonce. */
function poolparty_g4_mes_annonces_badge($bien) {
    if ($bien->post_status === 'publish') {
        return array('mes-annonces__etat--ok', 'En ligne');
    }
    if ($bien->post_status === 'pending') {
        return array('mes-annonces__etat--attente', 'En attente de validation');
    }
    if (get_post_meta($bien->ID, 'pp_refus_motif', true)) {
        return array('mes-annonces__etat--refus', 'Refusée, à corriger');
    }
    return array('mes-annonces__etat--neutre', 'Brouillon');
}

/* =============================================================
   2. ENREGISTREMENT DES MODIFICATIONS (Post / Redirect / Get)
   ============================================================= */

/**
 * Suppression d'une annonce par son propriétaire (bouton corbeille des
 * tuiles) : mise en corbeille WordPress, jamais de suppression définitive.
 * L'annonce disparaît de « Mes annonces » et du site ; l'équipe peut la
 * récupérer depuis le back-office si besoin.
 */
function poolparty_g4_mes_annonces_supprimer() {
    if (!isset($_POST['pp_annonce_action']) || $_POST['pp_annonce_action'] !== 'supprimer') {
        return;
    }
    if (!is_page('mes-annonces') || !is_user_logged_in()) {
        return;
    }

    $bien_id = isset($_POST['bien_id']) ? absint($_POST['bien_id']) : 0;
    $bien    = $bien_id ? get_post($bien_id) : null;

    check_admin_referer('pp_annonce_supprimer_' . $bien_id);

    if (!$bien || $bien->post_type !== 'bien'
        || ((int) $bien->post_author !== get_current_user_id() && !current_user_can('manage_options'))) {
        wp_die('Vous ne pouvez pas supprimer cette annonce.', 403);
    }

    wp_trash_post($bien_id);
    wp_safe_redirect(add_query_arg('pp_msg', 'annonce-supprimee', home_url('/mes-annonces/')));
    exit;
}
add_action('template_redirect', 'poolparty_g4_mes_annonces_supprimer');

function poolparty_g4_mes_annonces_traiter() {
    if (!isset($_POST['pp_annonce_action']) || $_POST['pp_annonce_action'] !== 'modifier') {
        return;
    }
    if (!is_page('mes-annonces')) {
        return;
    }
    if (!is_user_logged_in()) {
        wp_safe_redirect(home_url('/mes-annonces/'));
        exit;
    }

    $bien_id = isset($_POST['bien_id']) ? absint($_POST['bien_id']) : 0;
    $bien    = $bien_id ? get_post($bien_id) : null;

    check_admin_referer('pp_annonce_modifier_' . $bien_id);

    // Propriétaire (ou admin) uniquement.
    if (!$bien || $bien->post_type !== 'bien'
        || ((int) $bien->post_author !== get_current_user_id() && !current_user_can('manage_options'))) {
        wp_die('Vous ne pouvez pas modifier cette annonce.', 403);
    }

    // Réservation en cours : modification refusée pour le membre. L'équipe
    // (admin) garde la main pour aider un membre qui contacte la plateforme.
    if (poolparty_g4_bien_a_resa_en_cours($bien_id) && !current_user_can('manage_options')) {
        wp_safe_redirect(add_query_arg('pp_msg', 'resa-en-cours', home_url('/mes-annonces/')));
        exit;
    }

    // -- Champs texte --------------------------------------------------
    $titre       = isset($_POST['titre']) ? sanitize_text_field(wp_unslash($_POST['titre'])) : '';
    $description = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '';
    $prix        = isset($_POST['prix']) ? (int) preg_replace('/[^0-9]/', '', (string) $_POST['prix']) : 0;
    $capacite    = isset($_POST['capacite']) ? absint($_POST['capacite']) : 0;

    if ($titre === '' || $description === '' || $prix <= 0) {
        wp_safe_redirect(add_query_arg(array('bien' => $bien_id, 'pp_msg' => 'champs-manquants'), home_url('/mes-annonces/')));
        exit;
    }

    // Une annonce refusée corrigée repart en validation ; une annonce en
    // ligne (ou déjà en attente) garde son statut.
    $statut = $bien->post_status === 'draft' ? 'pending' : $bien->post_status;

    wp_update_post(array(
        'ID'           => $bien_id,
        'post_title'   => $titre,
        'post_content' => $description,
        'post_status'  => $statut,
    ));
    if ($statut === 'pending') {
        delete_post_meta($bien_id, 'pp_refus_motif');
    }

    update_post_meta($bien_id, 'pp_prix_heure', $prix);
    if ($capacite > 0) {
        update_post_meta($bien_id, 'pp_capacite_max', $capacite);
    }
    update_post_meta($bien_id, 'pp_alt', $titre);

    // Listes libres, séparées par des virgules.
    foreach (array('equipements' => 'pp_equipements', 'jours' => 'pp_jours', 'indispos' => 'pp_indispos') as $champ => $meta) {
        if (isset($_POST[$champ])) {
            $brut   = sanitize_text_field(wp_unslash($_POST[$champ]));
            $liste  = array_values(array_filter(array_map('trim', explode(',', $brut))));
            update_post_meta($bien_id, $meta, implode(',', $liste));
        }
    }

    // Nouvelles photos : ajoutées à la galerie ; la première devient
    // l'image à la une si l'annonce n'en avait pas.
    if (!empty($_FILES['photos']) && !empty($_FILES['photos']['name'][0])) {
        $nouvelles = poolparty_g4_uploader_photos_bien($bien_id);
        if (!empty($nouvelles)) {
            $galerie = get_post_meta($bien_id, 'pp_galerie', true);
            $ids     = $galerie ? array_map('intval', explode(',', $galerie)) : array();
            $ids     = array_merge($ids, $nouvelles);
            update_post_meta($bien_id, 'pp_galerie', implode(',', $ids));
            if (!has_post_thumbnail($bien_id)) {
                set_post_thumbnail($bien_id, $nouvelles[0]);
            }
        }
    }

    $msg = $statut === 'pending' ? 'annonce-renvoyee' : 'annonce-modifiee';
    wp_safe_redirect(add_query_arg('pp_msg', $msg, home_url('/mes-annonces/')));
    exit;
}
add_action('template_redirect', 'poolparty_g4_mes_annonces_traiter');

/** Messages de confirmation de la page (indicateur ?pp_msg=). */
function poolparty_g4_mes_annonces_flash() {
    $cle = isset($_GET['pp_msg']) ? sanitize_key($_GET['pp_msg']) : '';
    $map = array(
        'annonce-modifiee'  => array('Votre annonce a bien été mise à jour.', 'ok'),
        'annonce-renvoyee'  => array('Votre annonce corrigée a été renvoyée à l\'équipe pour validation.', 'ok'),
        'annonce-supprimee' => array('Votre annonce a été supprimée. Contactez l\'équipe Pool Party si vous souhaitez la récupérer.', 'ok'),
        'resa-en-cours'     => array('Cette annonce a une réservation en cours : elle ne peut pas être modifiée pour le moment.', 'alerte'),
        'champs-manquants'  => array('Le titre, la description et le prix sont obligatoires.', 'alerte'),
    );
    return isset($map[$cle]) ? $map[$cle] : null;
}
