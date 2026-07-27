<?php
/**
 * Compte hôte de démonstration + rattachement des biens.
 * -------------------------------------------------------------
 * Les 16 biens sont importés sans propriétaire réel (post_author = admin).
 * Pour que les demandes de réservation arrivent à un vrai compte, on crée
 * un unique compte « Hôte Pool Party » (rôle Hôte) et on lui attribue la
 * propriété de tous les biens (post_author). Les profils d'hôtes affichés
 * sur les fiches (Julien, Paula...) restent inchangés : ils viennent de la
 * méta pp_id_hote, décorrélée de la propriété réelle.
 *
 * Identifiants du compte hôte (à communiquer pour la démo) :
 *   e-mail        : hote@poolparty-demo.fr
 *   identifiant   : hote
 *   mot de passe  : PoolPartyHote2026!
 *
 * Idempotent : verrous par option, ne (re)joue qu'après un changement de
 * version. Incrémenter la constante pour rejouer.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PP_HOTE_DEMO_VERSION', '1');
define('PP_HOTE_DEMO_LOGIN', 'hote');
define('PP_HOTE_DEMO_EMAIL', 'hote@poolparty-demo.fr');
define('PP_HOTE_DEMO_PASS', 'PoolPartyHote2026!');

/**
 * Crée (ou retrouve) le compte hôte de démonstration et mémorise son ID.
 */
function poolparty_g4_creer_hote_demo() {
    if (get_option('pp_hote_demo_version') === PP_HOTE_DEMO_VERSION) {
        return;
    }
    // Prise de verrou atomique : une seule requête lance la création.
    if (!add_option('pp_hote_demo_claim_' . PP_HOTE_DEMO_VERSION, 1, '', false)) {
        return;
    }

    $existant = get_user_by('login', PP_HOTE_DEMO_LOGIN);
    if (!$existant) {
        $existant = get_user_by('email', PP_HOTE_DEMO_EMAIL);
    }

    if ($existant) {
        $existant->add_role('hote');
        $hote_id = $existant->ID;
    } else {
        $hote_id = wp_insert_user(array(
            'user_login'   => PP_HOTE_DEMO_LOGIN,
            'user_email'   => PP_HOTE_DEMO_EMAIL,
            'user_pass'    => PP_HOTE_DEMO_PASS,
            'first_name'   => 'Hôte',
            'last_name'    => 'Pool Party',
            'display_name' => 'Hôte Pool Party',
            'role'         => 'hote',
        ));
    }

    if (!is_wp_error($hote_id) && $hote_id) {
        update_option('pp_hote_demo_id', (int) $hote_id);
        update_option('pp_hote_demo_version', PP_HOTE_DEMO_VERSION);
    }
}
add_action('init', 'poolparty_g4_creer_hote_demo', 25);

/**
 * Attribue la propriété (post_author) de tous les biens au compte hôte.
 * S'exécute après l'import des biens (init 20) et la création du compte
 * (init 25). Verrou par version pour ne rattacher qu'une fois.
 */
function poolparty_g4_attacher_biens_hote() {
    if (get_option('pp_biens_hote_version') === PP_HOTE_DEMO_VERSION) {
        return;
    }
    $hote_id = (int) get_option('pp_hote_demo_id');
    if (!$hote_id) {
        return;
    }

    $biens = get_posts(array(
        'post_type'      => 'bien',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ));

    // Tant qu'aucun bien n'est en base (import pas encore joué), on ne pose
    // pas le verrou : la fonction retentera au prochain chargement.
    if (empty($biens)) {
        return;
    }

    foreach ($biens as $bien_id) {
        if ((int) get_post_field('post_author', $bien_id) !== $hote_id) {
            wp_update_post(array('ID' => $bien_id, 'post_author' => $hote_id));
        }
    }

    update_option('pp_biens_hote_version', PP_HOTE_DEMO_VERSION);
}
add_action('init', 'poolparty_g4_attacher_biens_hote', 30);
