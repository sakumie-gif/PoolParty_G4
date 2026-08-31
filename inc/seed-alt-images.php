<?php
/**
 * Textes alternatifs des photos de galerie.
 * -------------------------------------------------------------
 * Les photos ajoutées à la médiathèque sans texte alternatif
 * ressortent avec un alt vide sur la fiche d'un bien, et le bouton
 * d'agrandissement annonce alors « Agrandir : » suivi de rien. Ce
 * fichier donne une description à celles qui n'en ont pas, à partir
 * du titre de l'annonce et du rang de la photo.
 *
 * Une description saisie dans la médiathèque n'est jamais remplacée.
 * Ne s'exécute qu'une fois (verrou par option) ; incrémenter
 * PP_ALT_SEED_VERSION pour rejouer après ajout de photos.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PP_ALT_SEED_VERSION', '1');

/**
 * Complète les textes alternatifs manquants des galeries de biens.
 */
function poolparty_g4_importer_alt_galeries() {
    if (get_option('pp_alt_seed_version') === PP_ALT_SEED_VERSION) {
        return;
    }

    $biens = get_posts(array(
        'post_type'      => 'bien',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ));

    foreach ($biens as $bien_id) {
        $ids = array_filter(array_map('absint', explode(',', (string) get_post_meta($bien_id, 'pp_galerie', true))));
        if (!$ids) {
            continue;
        }
        $titre = get_the_title($bien_id);
        $rang  = 0;
        foreach ($ids as $piece_id) {
            $rang++;
            if (get_post_type($piece_id) !== 'attachment') {
                continue;
            }
            if (get_post_meta($piece_id, '_wp_attachment_image_alt', true) !== '') {
                continue;
            }
            update_post_meta(
                $piece_id,
                '_wp_attachment_image_alt',
                sprintf('%s, photo %d', $titre, $rang)
            );
        }
    }

    update_option('pp_alt_seed_version', PP_ALT_SEED_VERSION);
}
add_action('init', 'poolparty_g4_importer_alt_galeries', 46);
