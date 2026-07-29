<?php
/**
 * Rôle utilisateur de PoolParty.
 *
 * Modèle unifié type Airbnb : un seul profil de membre, à la fois locataire
 * ET hôte. Tout inscrit peut réserver un espace et proposer le sien. Il n'y a
 * donc qu'un rôle métier, « Membre », en plus de l'Administrateur natif
 * (l'équipe PoolParty), qui n'est pas touché.
 *
 * Historique : le site distinguait avant « Locataire » (lecture seule) et
 * « Hôte » (publication), cumulables via « Devenir partenaire ». La refonte
 * des réservations a rendu tout membre hôte potentiel : les deux rôles ne
 * pilotaient plus rien côté site. On les fusionne ici et on migre les comptes
 * existants vers « Membre ».
 */

if (!defined('ABSPATH')) {
    exit;
}

// Verrou de version : le rôle vit en base une fois créé. On ne rejoue la
// définition qu'après un changement. Incrémenter à chaque modification des
// capacités ci-dessous. v2 : fusion locataire + hote en « membre ».
define('PP_ROLES_VERSION', 2);

/**
 * Crée / met à jour le rôle Membre et migre les anciens comptes. Idempotent
 * grâce au verrou de version.
 */
function poolparty_g4_enregistrer_roles() {
    if ((int) get_option('pp_roles_version') === PP_ROLES_VERSION) {
        return;
    }

    // Membre : peut réserver (côté site) et publier / gérer ses propres
    // annonces. Le CPT « bien » utilise les capacités standard de type post.
    // Le back-office reste fermé aux membres (voir inc/auth.php) : la
    // publication se fait par le tunnel « Proposer » côté site.
    remove_role('membre');
    add_role('membre', 'Membre', array(
        'read'                   => true,
        'upload_files'           => true,
        'edit_posts'             => true,
        'publish_posts'          => true,
        'edit_published_posts'   => true,
        'delete_posts'           => true,
        'delete_published_posts' => true,
    ));

    // Migration : tout compte encore en « locataire » ou « hote » bascule en
    // « membre ». La propriété des annonces (post_author) est indépendante du
    // rôle, rien n'est perdu.
    poolparty_g4_migrer_vers_membre();

    // Les anciens rôles n'ont plus aucun porteur : on les retire du site.
    remove_role('locataire');
    remove_role('hote');

    update_option('pp_roles_version', PP_ROLES_VERSION);
}
add_action('after_setup_theme', 'poolparty_g4_enregistrer_roles');

/**
 * Bascule vers « Membre » tout utilisateur portant encore un ancien rôle
 * métier. Ne touche pas aux administrateurs ni aux rôles natifs.
 */
function poolparty_g4_migrer_vers_membre() {
    $anciens = get_users(array(
        'role__in' => array('locataire', 'hote'),
        'fields'   => 'ID',
    ));
    foreach ($anciens as $user_id) {
        $user = get_userdata($user_id);
        if (!$user || in_array('administrator', (array) $user->roles, true)) {
            continue;
        }
        // set_role remplace tous les rôles par « membre » (nettoie d'un coup
        // le cumul locataire + hote éventuel).
        $user->set_role('membre');
    }
}

/**
 * Compat : garantit le rôle « Membre ». Dans le modèle unifié, tout membre est
 * déjà hôte potentiel ; cette fonction ne sert plus qu'à d'anciens appels
 * éventuels. Conservée pour ne rien casser.
 *
 * @param int $user_id Identifiant du membre.
 */
function poolparty_g4_promouvoir_hote($user_id) {
    $user = get_userdata($user_id);
    if (!$user) {
        return;
    }
    if (!in_array('membre', (array) $user->roles, true)) {
        $user->set_role('membre');
    }
}
