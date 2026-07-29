<?php
/**
 * Compte administrateur de démonstration.
 * -------------------------------------------------------------
 * Pendant du compte membre de démo (Sophie Marchand, voir seed-hote.php) :
 * un compte à présenter le jour J pour montrer la console d'administration
 * côté site (page /administration/). C'est un vrai administrateur WordPress
 * (rôle « administrator », capacité manage_options), condition d'accès à la
 * console et au back-office.
 *
 * Distinct des comptes admin techniques (PoolParty_G4_local / _online) qui,
 * eux, servent à gérer WordPress : ce compte-ci est prévu pour la démo.
 *
 * Identifiants du compte admin de démonstration (à présenter le jour J) :
 *   identifiant   : admin-demo
 *   e-mail        : poolparty.g4@gmail.com
 *   mot de passe  : PoolPartyAdmin2026
 *
 * Idempotent : verrou par option. Récupère un compte déjà présent (login ou
 * e-mail de démo) au lieu d'en créer un doublon, puis le met à jour.
 * Incrémenter la version pour rejouer.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PP_ADMIN_DEMO_VERSION', '2');
define('PP_ADMIN_DEMO_LOGIN', 'admin-demo');
define('PP_ADMIN_DEMO_EMAIL', 'poolparty.g4@gmail.com');
define('PP_ADMIN_DEMO_PASS', 'PoolPartyAdmin2026');
define('PP_ADMIN_DEMO_PRENOM', 'Julie');
define('PP_ADMIN_DEMO_NOM', 'Bernard');

/**
 * Crée (ou retrouve et met à jour) le compte administrateur de démonstration.
 */
function poolparty_g4_creer_admin_demo() {
    if (get_option('pp_admin_demo_version') === PP_ADMIN_DEMO_VERSION) {
        return;
    }
    // Prise de verrou atomique : une seule requête lance l'opération.
    if (!add_option('pp_admin_demo_claim_' . PP_ADMIN_DEMO_VERSION, 1, '', false)) {
        return;
    }

    $u = get_user_by('login', PP_ADMIN_DEMO_LOGIN);
    if (!$u) {
        $u = get_user_by('email', PP_ADMIN_DEMO_EMAIL);
    }

    if ($u) {
        $admin_id = (int) $u->ID;

        $donnees = array(
            'ID'           => $admin_id,
            'first_name'   => PP_ADMIN_DEMO_PRENOM,
            'last_name'    => PP_ADMIN_DEMO_NOM,
            'display_name' => PP_ADMIN_DEMO_PRENOM . ' ' . PP_ADMIN_DEMO_NOM,
            'role'         => 'administrator',
        );
        $proprio_email = email_exists(PP_ADMIN_DEMO_EMAIL);
        if (!$proprio_email || (int) $proprio_email === $admin_id) {
            $donnees['user_email'] = PP_ADMIN_DEMO_EMAIL;
        }
        wp_update_user($donnees);
        wp_set_password(PP_ADMIN_DEMO_PASS, $admin_id);
    } else {
        $admin_id = wp_insert_user(array(
            'user_login'   => PP_ADMIN_DEMO_LOGIN,
            'user_email'   => PP_ADMIN_DEMO_EMAIL,
            'user_pass'    => PP_ADMIN_DEMO_PASS,
            'first_name'   => PP_ADMIN_DEMO_PRENOM,
            'last_name'    => PP_ADMIN_DEMO_NOM,
            'display_name' => PP_ADMIN_DEMO_PRENOM . ' ' . PP_ADMIN_DEMO_NOM,
            'role'         => 'administrator',
        ));
    }

    if (!is_wp_error($admin_id) && $admin_id) {
        update_option('pp_admin_demo_id', (int) $admin_id);
        update_option('pp_admin_demo_version', PP_ADMIN_DEMO_VERSION);
    }
}
add_action('init', 'poolparty_g4_creer_admin_demo', 24);
