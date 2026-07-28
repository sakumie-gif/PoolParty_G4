<?php
/**
 * Création d'une annonce (bien) depuis le site — tunnel « Proposer ».
 * -------------------------------------------------------------
 * Le membre remplit le tunnel « Proposer votre espace » et publie : son
 * annonce est réellement enregistrée en base (type de contenu « bien »),
 * rattachée à son compte, avec ses photos téléversées. Aucun passage par
 * le back-office : tout se fait depuis le site.
 *
 * L'annonce est créée au statut « en attente » (pending) : elle n'apparaît
 * sur le site qu'après validation par l'administrateur (Publier depuis
 * WordPress). Le passage pending → publish déclenche déjà l'e-mail « votre
 * annonce est en ligne » au propriétaire (voir inc/emails.php).
 *
 * Point d'entrée AJAX unique, protégé par le jeton pp_bien :
 *   - pp_creer_bien : crée l'annonce + ses photos.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Associe un « type » du tunnel (libellé) à un slug de catégorie existant.
 */
function poolparty_g4_type_vers_categorie($type) {
    $map = array(
        'Piscine enterrée'   => 'piscine',
        'Piscine hors-sol'   => 'piscine',
        'Piscine intérieure' => 'piscine',
        'Piscine naturelle'  => 'piscine',
        'Jacuzzi'            => 'jacuzzi',
        'Spa'                => 'spa',
        'Sauna'              => 'sauna',
        'Hammam'             => 'hammam',
        'Bain nordique'      => 'jacuzzi',
    );
    return isset($map[$type]) ? $map[$type] : 'piscine';
}

/**
 * Téléverse les photos envoyées (champ multiple $_FILES['photos']) et les
 * rattache à l'annonce : renvoie la liste des IDs de pièces jointes créées.
 */
function poolparty_g4_uploader_photos_bien($post_id) {
    if (empty($_FILES['photos']) || empty($_FILES['photos']['name'])) {
        return array();
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $ids     = array();
    $fichiers = $_FILES['photos'];
    $total    = is_array($fichiers['name']) ? count($fichiers['name']) : 0;

    for ($i = 0; $i < $total; $i++) {
        if ((int) $fichiers['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }
        // On reconstruit une entrée $_FILES simple attendue par WordPress.
        $_FILES['pp_photo_courante'] = array(
            'name'     => $fichiers['name'][$i],
            'type'     => $fichiers['type'][$i],
            'tmp_name' => $fichiers['tmp_name'][$i],
            'error'    => $fichiers['error'][$i],
            'size'     => $fichiers['size'][$i],
        );
        // test_form => false : on ne passe pas par un formulaire admin classique.
        $attachment_id = media_handle_upload('pp_photo_courante', $post_id, array(), array('test_form' => false));
        if (!is_wp_error($attachment_id)) {
            $ids[] = (int) $attachment_id;
        }
    }
    unset($_FILES['pp_photo_courante']);

    return $ids;
}

function poolparty_g4_ajax_creer_bien() {
    check_ajax_referer('pp_bien', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(array('message' => 'Connectez-vous pour publier votre annonce.'), 401);
    }

    $user = wp_get_current_user();

    // --- Champs texte principaux ------------------------------------
    $titre       = isset($_POST['titre']) ? sanitize_text_field(wp_unslash($_POST['titre'])) : '';
    $description = isset($_POST['description']) ? sanitize_textarea_field(wp_unslash($_POST['description'])) : '';
    $type        = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : '';
    $acces       = isset($_POST['acces']) ? sanitize_text_field(wp_unslash($_POST['acces'])) : '';
    $commune     = isset($_POST['commune']) ? sanitize_text_field(wp_unslash($_POST['commune'])) : '';
    $voie        = isset($_POST['voie']) ? sanitize_text_field(wp_unslash($_POST['voie'])) : '';
    $cp          = isset($_POST['cp']) ? sanitize_text_field(wp_unslash($_POST['cp'])) : '';
    $prix        = isset($_POST['prix']) ? (int) preg_replace('/[^0-9]/', '', (string) $_POST['prix']) : 0;
    $invites     = isset($_POST['invites']) ? absint($_POST['invites']) : 0;
    $reservation = isset($_POST['reservation']) ? sanitize_text_field(wp_unslash($_POST['reservation'])) : '';

    // Listes (envoyées séparées par des virgules)
    $decouper = function ($cle) {
        if (empty($_POST[$cle])) {
            return array();
        }
        $brut = sanitize_text_field(wp_unslash($_POST[$cle]));
        return array_values(array_filter(array_map('trim', explode(',', $brut))));
    };
    $equipements = $decouper('equipements');
    $securite    = $decouper('securite');
    $jours       = $decouper('jours');

    // --- Validation minimale ----------------------------------------
    if ($titre === '') {
        wp_send_json_error(array('message' => 'Le titre de votre annonce est manquant.'), 400);
    }
    if ($description === '') {
        wp_send_json_error(array('message' => 'La description de votre annonce est manquante.'), 400);
    }
    if ($prix <= 0) {
        wp_send_json_error(array('message' => 'Le prix par heure est invalide.'), 400);
    }
    if ($commune === '') {
        wp_send_json_error(array('message' => 'La commune de votre espace est manquante.'), 400);
    }
    if (empty($_FILES['photos']) || empty($_FILES['photos']['name'][0])) {
        wp_send_json_error(array('message' => 'Ajoutez au moins une photo de votre espace.'), 400);
    }

    // --- Création de l'annonce (en attente de validation) -----------
    $post_id = wp_insert_post(array(
        'post_type'    => 'bien',
        'post_status'  => 'pending',
        'post_author'  => $user->ID,
        'post_title'   => $titre,
        'post_content' => $description,
    ), true);

    if (is_wp_error($post_id) || !$post_id) {
        wp_send_json_error(array('message' => 'La publication a échoué. Réessayez.'), 500);
    }

    // Catégorie (taxonomie) d'après le type choisi.
    if ($type !== '') {
        wp_set_object_terms($post_id, poolparty_g4_type_vers_categorie($type), 'categorie_bien', false);
    }

    // Métas d'affichage. Une annonce neuve n'a ni note ni avis, et porte le
    // badge « Nouveau ». Les compteurs de tri (note/prix/distance) sont
    // remplis pour rester compatibles avec le catalogue.
    $metas = array(
        'pp_ville'        => $commune,
        'pp_pays'         => 'France',
        'pp_prix_heure'   => $prix,
        'pp_capacite_min' => 1,
        'pp_capacite_max' => max(1, $invites),
        'pp_note'         => '0',
        'pp_nb_avis'      => 0,
        'pp_distance_km'  => 0,
        'pp_tag'          => 'nouveau',
        'pp_alt'          => $titre,
        // Détails complémentaires (non affichés partout, conservés pour la fiche).
        'pp_acces'        => $acces,
        'pp_equipements'  => implode(',', $equipements),
        'pp_securite'     => implode(',', $securite),
        'pp_jours'        => implode(',', $jours),
        'pp_mode_resa'    => $reservation,
        'pp_adresse_voie' => $voie,
        'pp_cp'           => $cp,
    );
    foreach ($metas as $cle => $valeur) {
        update_post_meta($post_id, $cle, $valeur);
    }

    // Photos : la 1re devient l'image à la une, toutes vont dans la galerie.
    $photos = poolparty_g4_uploader_photos_bien($post_id);
    if (!empty($photos)) {
        set_post_thumbnail($post_id, $photos[0]);
        update_post_meta($post_id, 'pp_galerie', implode(',', $photos));
    }

    wp_send_json_success(array(
        'message' => 'Votre annonce a bien été envoyée pour validation.',
        'id'      => $post_id,
        'titre'   => $titre,
    ));
}
add_action('wp_ajax_pp_creer_bien', 'poolparty_g4_ajax_creer_bien');
add_action('wp_ajax_nopriv_pp_creer_bien', 'poolparty_g4_ajax_creer_bien');
