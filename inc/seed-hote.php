<?php
/**
 * Compte membre de démonstration + rattachement des biens.
 * -------------------------------------------------------------
 * Modèle unifié : un seul compte « Membre » sert la démonstration. Il est
 * à la fois hôte (il possède les annonces) et locataire (il peut réserver).
 * Les 16 biens importés sans propriétaire réel lui sont rattachés
 * (post_author) pour que les demandes de réservation aient une destination.
 * Les profils d'hôtes affichés sur les fiches (Julien, Paula...) restent
 * inchangés : ils viennent de la méta pp_id_hote, décorrélée de la propriété.
 *
 * Identifiants du compte de démonstration (à présenter le jour J) :
 *   identifiant   : membre-demo
 *   e-mail        : a.garoscio@gmail.com
 *   mot de passe  : M2P_PoolParty_G4
 *
 * Idempotent : verrous par option. Récupère un compte déjà présent (nouvelle
 * identité, ancien e-mail de démo, ancien compte « hote ») au lieu d'en créer
 * un doublon, puis le met à jour. Incrémenter la version pour rejouer.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PP_MEMBRE_DEMO_VERSION', '2');
define('PP_MEMBRE_DEMO_LOGIN', 'membre-demo');
define('PP_MEMBRE_DEMO_EMAIL', 'a.garoscio@gmail.com');
define('PP_MEMBRE_DEMO_PASS', 'M2P_PoolParty_G4');
define('PP_MEMBRE_DEMO_PRENOM', 'Sophie');
define('PP_MEMBRE_DEMO_NOM', 'Marchand');

/**
 * Crée (ou retrouve et met à jour) le compte membre de démonstration et
 * mémorise son identifiant.
 */
function poolparty_g4_creer_membre_demo() {
    if (get_option('pp_membre_demo_version') === PP_MEMBRE_DEMO_VERSION) {
        return;
    }
    // Prise de verrou atomique : une seule requête lance l'opération.
    if (!add_option('pp_membre_demo_claim_' . PP_MEMBRE_DEMO_VERSION, 1, '', false)) {
        return;
    }

    // Récupère un compte réutilisable, dans l'ordre : nouvelle identité, nouvel
    // e-mail, puis les anciennes identités de démo (pour migrer en place sans
    // créer de doublon).
    $u = get_user_by('login', PP_MEMBRE_DEMO_LOGIN);
    if (!$u) {
        $u = get_user_by('email', PP_MEMBRE_DEMO_EMAIL);
    }
    if (!$u) {
        $u = get_user_by('login', 'hote');
    }
    if (!$u) {
        $u = get_user_by('email', 'hote@poolparty-demo.fr');
    }

    if ($u) {
        $membre_id = (int) $u->ID;

        // Renomme l'identifiant si besoin : WordPress n'expose pas d'API pour
        // cela, on écrit directement dans la table puis on vide le cache.
        if ($u->user_login !== PP_MEMBRE_DEMO_LOGIN && !username_exists(PP_MEMBRE_DEMO_LOGIN)) {
            global $wpdb;
            $wpdb->update($wpdb->users, array('user_login' => PP_MEMBRE_DEMO_LOGIN), array('ID' => $membre_id));
            clean_user_cache($membre_id);
        }

        // Met à jour l'e-mail seulement s'il est libre (évite le conflit si un
        // autre compte le porte déjà).
        $donnees = array(
            'ID'           => $membre_id,
            'first_name'   => PP_MEMBRE_DEMO_PRENOM,
            'last_name'    => PP_MEMBRE_DEMO_NOM,
            'display_name' => PP_MEMBRE_DEMO_PRENOM . ' ' . PP_MEMBRE_DEMO_NOM,
            'role'         => 'membre',
        );
        $proprio_email = email_exists(PP_MEMBRE_DEMO_EMAIL);
        if (!$proprio_email || (int) $proprio_email === $membre_id) {
            $donnees['user_email'] = PP_MEMBRE_DEMO_EMAIL;
        }
        wp_update_user($donnees);
        wp_set_password(PP_MEMBRE_DEMO_PASS, $membre_id);
    } else {
        $membre_id = wp_insert_user(array(
            'user_login'   => PP_MEMBRE_DEMO_LOGIN,
            'user_email'   => PP_MEMBRE_DEMO_EMAIL,
            'user_pass'    => PP_MEMBRE_DEMO_PASS,
            'first_name'   => PP_MEMBRE_DEMO_PRENOM,
            'last_name'    => PP_MEMBRE_DEMO_NOM,
            'display_name' => PP_MEMBRE_DEMO_PRENOM . ' ' . PP_MEMBRE_DEMO_NOM,
            'role'         => 'membre',
        ));
    }

    if (!is_wp_error($membre_id) && $membre_id) {
        update_option('pp_membre_demo_id', (int) $membre_id);
        update_option('pp_membre_demo_version', PP_MEMBRE_DEMO_VERSION);
    }
}
add_action('init', 'poolparty_g4_creer_membre_demo', 25);

/**
 * Attribue la propriété (post_author) de tous les biens au compte de
 * démonstration. S'exécute après l'import des biens (init 20) et la création
 * du compte (init 25). Verrou par version pour ne rattacher qu'une fois.
 */
function poolparty_g4_attacher_biens_membre_demo() {
    if (get_option('pp_membre_demo_biens_version') === PP_MEMBRE_DEMO_VERSION) {
        return;
    }
    $membre_id = (int) get_option('pp_membre_demo_id');
    if (!$membre_id) {
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
        if ((int) get_post_field('post_author', $bien_id) !== $membre_id) {
            wp_update_post(array('ID' => $bien_id, 'post_author' => $membre_id));
        }
    }

    update_option('pp_membre_demo_biens_version', PP_MEMBRE_DEMO_VERSION);
}
add_action('init', 'poolparty_g4_attacher_biens_membre_demo', 30);
