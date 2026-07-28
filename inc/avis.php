<?php
/**
 * Avis de la page « Mes réservations » V2, sur commentaires WordPress
 * natifs (argument soutenance : modération possible dans l'admin).
 * -----------------------------------------------------------------
 * - Avis d'un locataire sur un espace : commentaire de type pp_avis
 *   posé sur le bien, note en meta pp_note, réservation d'origine en
 *   meta pp_resa_id. Publié directement.
 * - Réponse publique de l'hôte : commentaire enfant de type
 *   pp_avis_reponse sous l'avis.
 * - Avis d'un hôte sur son locataire : commentaire de type
 *   pp_avis_locataire posé sur la réservation.
 * Une venue est « à évaluer » quand la réservation est confirmée,
 * passée, et sans avis. Jamais d'e-mail affiché : les échanges
 * passent par la messagerie de la plateforme.
 */

if (!defined('ABSPATH')) {
    exit;
}

/* =============================================================
   1. REQUÊTES
   ============================================================= */

/** Valeur AAAAMMJJ d'une date JJ/MM/AAAA pour trier côté JS (0 si illisible). */
function poolparty_g4_avis_ts($date) {
    $d = $date ? DateTime::createFromFormat('d/m/Y', $date) : false;
    return $d ? (int) $d->format('Ymd') : 0;
}

/** Avis (pp_avis) déjà publié pour une réservation donnée. */
function poolparty_g4_avis_de_resa($resa_id) {
    $avis = get_comments(array(
        'type'       => 'pp_avis',
        'meta_key'   => 'pp_resa_id',
        'meta_value' => (int) $resa_id,
        'number'     => 1,
    ));
    return $avis ? $avis[0] : null;
}

/** Réponse de l'hôte sous un avis (commentaire enfant pp_avis_reponse). */
function poolparty_g4_reponse_d_avis($avis_id) {
    $reponses = get_comments(array(
        'type'   => 'pp_avis_reponse',
        'parent' => (int) $avis_id,
        'number' => 1,
    ));
    return $reponses ? $reponses[0] : null;
}

/** Avis de l'hôte (pp_avis_locataire) posé sur une réservation. */
function poolparty_g4_avis_locataire_de_resa($resa_id) {
    $avis = get_comments(array(
        'type'    => 'pp_avis_locataire',
        'post_id' => (int) $resa_id,
        'number'  => 1,
    ));
    return $avis ? $avis[0] : null;
}

/**
 * Données de l'onglet Avis pour le JS de la page Mes réservations :
 * un tableau par vue, dans le format attendu par mes-reservations-v2.js.
 */
function poolparty_g4_avis_pour_js($user_id) {
    $donnees = array('locataire' => array(), 'hote' => array());
    if (!$user_id) {
        return $donnees;
    }

    /* ---- Vue locataire : venues à évaluer puis avis publiés ---- */
    foreach (poolparty_g4_reservations_locataire($user_id) as $resa) {
        if (get_post_meta($resa->ID, 'pp_statut', true) !== 'acceptee' || !poolparty_g4_reservation_passee($resa->ID)) {
            continue;
        }
        if (poolparty_g4_avis_de_resa($resa->ID)) {
            continue;
        }
        $bien_id   = (int) get_post_meta($resa->ID, 'pp_bien_id', true);
        $hote_data = $bien_id ? poolparty_g4_get_hote(poolparty_g4_meta($bien_id, 'id_hote')) : null;
        $date      = get_post_meta($resa->ID, 'pp_date', true);
        $donnees['locataire'][] = array(
            'id'       => 'resa-' . $resa->ID,
            'resaId'   => $resa->ID,
            'aEvaluer' => true,
            'bien'     => $bien_id ? get_the_title($bien_id) : $resa->post_title,
            'lien'     => $bien_id ? get_permalink($bien_id) : '',
            'hote'     => ($hote_data && !empty($hote_data['prenom'])) ? $hote_data['prenom'] : 'votre hôte',
            'date'     => $date,
            'ts'       => poolparty_g4_avis_ts($date),
        );
    }
    foreach (get_comments(array('type' => 'pp_avis', 'user_id' => $user_id)) as $avis) {
        $resa_id   = (int) get_comment_meta($avis->comment_ID, 'pp_resa_id', true);
        $bien_id   = (int) $avis->comment_post_ID;
        $hote_data = $bien_id ? poolparty_g4_get_hote(poolparty_g4_meta($bien_id, 'id_hote')) : null;
        $date      = $resa_id ? get_post_meta($resa_id, 'pp_date', true) : '';
        $reponse   = poolparty_g4_reponse_d_avis($avis->comment_ID);
        $donnees['locataire'][] = array(
            'id'      => 'avis-' . $avis->comment_ID,
            'bien'    => get_the_title($bien_id),
            'lien'    => get_permalink($bien_id),
            'hote'    => ($hote_data && !empty($hote_data['prenom'])) ? $hote_data['prenom'] : 'votre hôte',
            'date'    => $date ?: get_comment_date('d/m/Y', $avis),
            'ts'      => poolparty_g4_avis_ts($date ?: get_comment_date('d/m/Y', $avis)),
            'note'    => (int) get_comment_meta($avis->comment_ID, 'pp_note', true),
            'texte'   => $avis->comment_content,
            'reponse' => $reponse ? $reponse->comment_content : '',
        );
    }

    /* ---- Vue hôte : avis reçus sur ses espaces, puis ses locataires ---- */
    $biens = get_posts(array(
        'post_type'      => 'bien',
        'author'         => $user_id,
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ));
    if ($biens) {
        foreach (get_comments(array('type' => 'pp_avis', 'post__in' => $biens)) as $avis) {
            $resa_id = (int) get_comment_meta($avis->comment_ID, 'pp_resa_id', true);
            $auteur  = get_userdata((int) $avis->user_id);
            $date    = $resa_id ? get_post_meta($resa_id, 'pp_date', true) : '';
            $reponse = poolparty_g4_reponse_d_avis($avis->comment_ID);
            $donnees['hote'][] = array(
                'id'      => 'avis-' . $avis->comment_ID,
                'avisId'  => (int) $avis->comment_ID,
                'type'    => 'recu',
                'bien'    => get_the_title((int) $avis->comment_post_ID),
                'auteur'  => $auteur ? $auteur->display_name : 'Un membre',
                'date'    => $date ?: get_comment_date('d/m/Y', $avis),
                'ts'      => poolparty_g4_avis_ts($date ?: get_comment_date('d/m/Y', $avis)),
                'note'    => (int) get_comment_meta($avis->comment_ID, 'pp_note', true),
                'texte'   => $avis->comment_content,
                'reponse' => $reponse ? $reponse->comment_content : '',
            );
        }
    }
    foreach (poolparty_g4_reservations_hote($user_id) as $resa) {
        if (get_post_meta($resa->ID, 'pp_statut', true) !== 'acceptee' || !poolparty_g4_reservation_passee($resa->ID)) {
            continue;
        }
        $bien_id   = (int) get_post_meta($resa->ID, 'pp_bien_id', true);
        $locataire = get_userdata((int) $resa->post_author);
        $date      = get_post_meta($resa->ID, 'pp_date', true);
        $evaluation = poolparty_g4_avis_locataire_de_resa($resa->ID);
        if ($evaluation) {
            $donnees['hote'][] = array(
                'id'        => 'avisloc-' . $evaluation->comment_ID,
                'type'      => 'locataire',
                'locataire' => $locataire ? $locataire->display_name : 'Un membre',
                'bien'      => $bien_id ? get_the_title($bien_id) : $resa->post_title,
                'date'      => $date,
                'ts'        => poolparty_g4_avis_ts($date),
                'note'      => (int) get_comment_meta($evaluation->comment_ID, 'pp_note', true),
                'texte'     => $evaluation->comment_content,
            );
        } else {
            $donnees['hote'][] = array(
                'id'        => 'resa-l-' . $resa->ID,
                'resaId'    => $resa->ID,
                'type'      => 'locataire',
                'aEvaluer'  => true,
                'locataire' => $locataire ? $locataire->display_name : 'Un membre',
                'bien'      => $bien_id ? get_the_title($bien_id) : $resa->post_title,
                'date'      => $date,
                'ts'        => poolparty_g4_avis_ts($date),
            );
        }
    }

    return $donnees;
}

/* =============================================================
   2. AJAX
   ============================================================= */

/** Le locataire publie son avis sur l'espace (note obligatoire). */
function poolparty_g4_ajax_creer_avis() {
    check_ajax_referer('pp_avis', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connectez-vous pour laisser un avis.'), 401);
    }

    $resa_id = isset($_POST['resa_id']) ? absint($_POST['resa_id']) : 0;
    $note    = isset($_POST['note']) ? absint($_POST['note']) : 0;
    $texte   = isset($_POST['texte']) ? sanitize_textarea_field(wp_unslash($_POST['texte'])) : '';
    $resa    = $resa_id ? get_post($resa_id) : null;
    $user    = wp_get_current_user();

    if (!$resa || $resa->post_type !== 'reservation' || (int) $resa->post_author !== $user->ID) {
        wp_send_json_error(array('message' => 'Réservation introuvable.'), 404);
    }
    if (get_post_meta($resa_id, 'pp_statut', true) !== 'acceptee' || !poolparty_g4_reservation_passee($resa_id)) {
        wp_send_json_error(array('message' => 'Cette venue ne peut pas encore être évaluée.'), 400);
    }
    if (poolparty_g4_avis_de_resa($resa_id)) {
        wp_send_json_error(array('message' => 'Vous avez déjà évalué cette venue.'), 400);
    }
    if ($note < 1 || $note > 5) {
        wp_send_json_error(array('message' => 'Choisissez une note de 1 à 5.'), 400);
    }

    $bien_id = (int) get_post_meta($resa_id, 'pp_bien_id', true);
    $avis_id = wp_insert_comment(array(
        'comment_post_ID'      => $bien_id,
        'comment_type'         => 'pp_avis',
        'user_id'              => $user->ID,
        'comment_author'       => $user->display_name,
        'comment_author_email' => $user->user_email,
        'comment_content'      => $texte,
        'comment_approved'     => 1,
    ));
    if (!$avis_id) {
        wp_send_json_error(array('message' => 'Impossible d\'enregistrer votre avis. Réessayez.'), 500);
    }
    update_comment_meta($avis_id, 'pp_note', $note);
    update_comment_meta($avis_id, 'pp_resa_id', $resa_id);

    wp_send_json_success(array('avisId' => (int) $avis_id));
}
add_action('wp_ajax_pp_creer_avis', 'poolparty_g4_ajax_creer_avis');

/** L'hôte répond publiquement à un avis reçu sur son espace. */
function poolparty_g4_ajax_reponse_avis() {
    check_ajax_referer('pp_avis', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connectez-vous pour répondre.'), 401);
    }

    $avis_id = isset($_POST['avis_id']) ? absint($_POST['avis_id']) : 0;
    $texte   = isset($_POST['texte']) ? sanitize_textarea_field(wp_unslash($_POST['texte'])) : '';
    $avis    = $avis_id ? get_comment($avis_id) : null;
    $user    = wp_get_current_user();

    if (!$avis || $avis->comment_type !== 'pp_avis') {
        wp_send_json_error(array('message' => 'Avis introuvable.'), 404);
    }
    $bien = get_post((int) $avis->comment_post_ID);
    if (!$bien || ((int) $bien->post_author !== $user->ID && !current_user_can('manage_options'))) {
        wp_send_json_error(array('message' => 'Seul l\'hôte de cet espace peut répondre.'), 403);
    }
    if (poolparty_g4_reponse_d_avis($avis_id)) {
        wp_send_json_error(array('message' => 'Vous avez déjà répondu à cet avis.'), 400);
    }
    if ($texte === '') {
        wp_send_json_error(array('message' => 'Écrivez votre réponse avant de publier.'), 400);
    }

    $reponse_id = wp_insert_comment(array(
        'comment_post_ID'      => (int) $avis->comment_post_ID,
        'comment_parent'       => $avis_id,
        'comment_type'         => 'pp_avis_reponse',
        'user_id'              => $user->ID,
        'comment_author'       => $user->display_name,
        'comment_author_email' => $user->user_email,
        'comment_content'      => $texte,
        'comment_approved'     => 1,
    ));
    if (!$reponse_id) {
        wp_send_json_error(array('message' => 'Impossible d\'enregistrer votre réponse. Réessayez.'), 500);
    }

    wp_send_json_success();
}
add_action('wp_ajax_pp_reponse_avis', 'poolparty_g4_ajax_reponse_avis');

/** L'hôte évalue son locataire après la venue (note obligatoire). */
function poolparty_g4_ajax_avis_locataire() {
    check_ajax_referer('pp_avis', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connectez-vous pour évaluer votre locataire.'), 401);
    }

    $resa_id = isset($_POST['resa_id']) ? absint($_POST['resa_id']) : 0;
    $note    = isset($_POST['note']) ? absint($_POST['note']) : 0;
    $texte   = isset($_POST['texte']) ? sanitize_textarea_field(wp_unslash($_POST['texte'])) : '';
    $resa    = $resa_id ? get_post($resa_id) : null;
    $user    = wp_get_current_user();

    if (!$resa || $resa->post_type !== 'reservation') {
        wp_send_json_error(array('message' => 'Réservation introuvable.'), 404);
    }
    if ((int) get_post_meta($resa_id, 'pp_hote_id', true) !== $user->ID && !current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Seul l\'hôte de cette réservation peut évaluer le locataire.'), 403);
    }
    if (get_post_meta($resa_id, 'pp_statut', true) !== 'acceptee' || !poolparty_g4_reservation_passee($resa_id)) {
        wp_send_json_error(array('message' => 'Cette venue ne peut pas encore être évaluée.'), 400);
    }
    if (poolparty_g4_avis_locataire_de_resa($resa_id)) {
        wp_send_json_error(array('message' => 'Vous avez déjà évalué ce locataire.'), 400);
    }
    if ($note < 1 || $note > 5) {
        wp_send_json_error(array('message' => 'Choisissez une note de 1 à 5.'), 400);
    }

    $avis_id = wp_insert_comment(array(
        'comment_post_ID'      => $resa_id,
        'comment_type'         => 'pp_avis_locataire',
        'user_id'              => $user->ID,
        'comment_author'       => $user->display_name,
        'comment_author_email' => $user->user_email,
        'comment_content'      => $texte,
        'comment_approved'     => 1,
    ));
    if (!$avis_id) {
        wp_send_json_error(array('message' => 'Impossible d\'enregistrer votre avis. Réessayez.'), 500);
    }
    update_comment_meta($avis_id, 'pp_note', $note);

    wp_send_json_success(array('avisId' => (int) $avis_id));
}
add_action('wp_ajax_pp_avis_locataire', 'poolparty_g4_ajax_avis_locataire');
