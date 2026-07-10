<?php
/**
 * Rôles utilisateurs de PoolParty.
 *
 * Trois profils :
 *  - Administrateur : l'équipe PoolParty (rôle WordPress natif, non touché).
 *  - Locataire      : profil de départ de tout inscrit ; réserve, envoie des
 *                     messages, gère ses réservations. Droits de lecture.
 *  - Hôte           : cumulé EN PLUS du rôle Locataire quand un membre décide
 *                     de proposer son espace (parcours « Devenir partenaire »).
 *                     Peut publier et gérer ses annonces.
 *
 * Un même compte peut donc être Locataire + Hôte : WordPress autorise le
 * cumul de rôles (add_role/remove_role sur l'utilisateur). Le rôle Hôte
 * s'obtient via poolparty_g4_promouvoir_hote() ; en démo on l'attribue à la
 * main depuis l'admin, la version finale le branchera sur le formulaire.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Verrou de version : les rôles vivent en base une fois créés. On ne
// (re)joue add_role qu'après un changement de définition, pas à chaque
// chargement. Incrémenter à chaque modification des capacités ci-dessous.
define('PP_ROLES_VERSION', 1);

/**
 * Crée / met à jour les rôles métier. Idempotent grâce au verrou.
 */
function poolparty_g4_enregistrer_roles() {
    if ((int) get_option('pp_roles_version') === PP_ROLES_VERSION) {
        return;
    }

    // Locataire : lecture seule (comme l'abonné natif). Réservations et
    // messages passent par le front, pas par des capacités WordPress.
    remove_role('locataire');
    add_role('locataire', 'Locataire', array(
        'read' => true,
    ));

    // Hôte : peut créer et gérer ses propres annonces depuis le back-office
    // (le CPT « bien » utilise les capacités standard de type post).
    remove_role('hote');
    add_role('hote', 'Hôte', array(
        'read'                   => true,
        'upload_files'           => true,
        'edit_posts'             => true,
        'publish_posts'          => true,
        'edit_published_posts'   => true,
        'delete_posts'           => true,
        'delete_published_posts' => true,
    ));

    update_option('pp_roles_version', PP_ROLES_VERSION);
}
add_action('after_setup_theme', 'poolparty_g4_enregistrer_roles');

/**
 * Promeut un membre au rôle Hôte sans lui retirer Locataire (cumul).
 * Point d'accroche pour le futur formulaire « Devenir partenaire » ;
 * en démo, on peut l'appeler à la main ou changer le rôle dans l'admin.
 *
 * @param int $user_id Identifiant du membre à promouvoir.
 */
function poolparty_g4_promouvoir_hote($user_id) {
    $user = get_userdata($user_id);
    if (!$user) {
        return;
    }
    if (!in_array('hote', (array) $user->roles, true)) {
        $user->add_role('hote');
    }
}
