<?php
/**
 * Création automatique des Pages WordPress du site.
 * -------------------------------------------------------------
 * Crée une Page par slug (à propos, faq, contact, pages légales,
 * espace personnel...) pour que les URL /a-propos/, /faq/, etc.
 * existent. Le CONTENU de chaque page est fourni par son gabarit
 * page-<slug>.php (WordPress les associe automatiquement au slug) ;
 * le contenu de l'éditeur reste donc vide.
 *
 * Ne s'exécute qu'une fois (verrou par option). Anti-doublon par
 * contrôle d'existence sur le slug. Incrémenter PP_PAGES_SEED_VERSION
 * pour rejouer après avoir ajouté une page ici.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PP_PAGES_SEED_VERSION', '6');

/**
 * Pages à créer : slug => titre affiché (menu, onglet, fil d'Ariane).
 */
function poolparty_g4_seed_pages() {
    return array(
        'a-propos'           => 'À propos',
        'faq'                => 'FAQ',
        'contact'            => 'Contact',
        'presse'             => 'Presse',
        'actualites'         => 'Actualités',
        'partenaires'        => 'Partenaires',
        'devenir-partenaire' => 'Devenir partenaire',
        'moyen-de-paiement'  => 'Moyens de paiement',
        'paiement-securise'  => 'Paiement sécurisé',
        'assurance'          => 'Assurances',
        'accessibilite'      => 'Accessibilité',
        'mentions-legales'   => 'Mentions légales',
        'cgu'                => "Conditions générales d'utilisation",
        'cgv'                => 'Conditions générales de vente',
        'inscription'        => 'Inscription',
        'favoris'            => 'Mes favoris',
        'mes-reservations'   => 'Mes réservations',
        'messages'           => 'Messages',
        'proposer'           => 'Proposer votre espace',
        'reservation'        => 'Réservation',
    );
}

/**
 * Crée les pages manquantes puis pose le verrou.
 */
function poolparty_g4_importer_pages() {
    if (get_option('pp_pages_seed_version') === PP_PAGES_SEED_VERSION) {
        return;
    }
    // Prise de verrou atomique : une seule requête lance l'import de
    // cette version (add_option échoue si l'option existe déjà).
    if (!add_option('pp_pages_seed_claim_' . PP_PAGES_SEED_VERSION, 1, '', false)) {
        return;
    }

    foreach (poolparty_g4_seed_pages() as $slug => $titre) {
        if (get_page_by_path($slug, OBJECT, 'page')) {
            continue;
        }
        wp_insert_post(array(
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_title'   => $titre,
            'post_name'    => $slug,
            'post_content' => '',
        ));
    }

    update_option('pp_pages_seed_version', PP_PAGES_SEED_VERSION);
}
add_action('init', 'poolparty_g4_importer_pages', 20);
