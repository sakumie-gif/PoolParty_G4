<?php
/**
 * Authentification réelle des membres Pool Party.
 * -------------------------------------------------------------
 * Remplace la connexion « simulée » du site : la pop-up de connexion,
 * le formulaire d'inscription et l'écran « mot de passe oublié » passent
 * désormais par WordPress (vrais comptes en base). Trois points d'entrée
 * AJAX protégés par le jeton pp_auth :
 *   - pp_login     : vérifie e-mail + mot de passe (wp_signon).
 *   - pp_register  : crée un compte (rôle Locataire) puis connecte.
 *   - pp_reset     : lance la vraie procédure de réinitialisation.
 *
 * La bascule menu visiteur / connecté n'est plus pilotée par le
 * navigateur mais par WordPress : la classe is-connected est posée sur
 * <body> quand l'utilisateur est réellement authentifié.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Classe is-connected sur <body> quand un membre est connecté : c'est
 * elle qui affiche les sections « connecté » du menu et débloque les
 * fonctionnalités membres (favoris, réservations, messagerie).
 */
function poolparty_g4_body_class_connecte($classes) {
    if (is_user_logged_in()) {
        $classes[] = 'is-connected';
    }
    return $classes;
}
add_filter('body_class', 'poolparty_g4_body_class_connecte');

/* =============================================================
   1. CONNEXION
   ============================================================= */

function poolparty_g4_ajax_login() {
    check_ajax_referer('pp_auth', 'nonce');

    $identifiant = isset($_POST['email']) ? sanitize_text_field(wp_unslash($_POST['email'])) : '';
    $mot_de_passe = isset($_POST['password']) ? (string) $_POST['password'] : '';

    if ($identifiant === '' || $mot_de_passe === '') {
        wp_send_json_error(array('message' => 'Merci d\'indiquer votre e-mail et votre mot de passe.'));
    }

    // On repère d'abord si un compte existe pour cette adresse afin de
    // renvoyer un message utile (créer un compte vs mot de passe erroné).
    $compte_existe = false;
    if (is_email($identifiant)) {
        $compte_existe = (bool) get_user_by('email', $identifiant);
    } else {
        $compte_existe = (bool) get_user_by('login', $identifiant);
    }

    if (!$compte_existe) {
        wp_send_json_error(array('message' => 'Aucun compte n\'est associé à cette adresse. Créez-en un via « Inscription ».'));
    }

    // wp_signon accepte l'e-mail ou l'identifiant : WordPress teste les deux.
    $user = wp_signon(array(
        'user_login'    => $identifiant,
        'user_password' => $mot_de_passe,
        'remember'      => true,
    ), is_ssl());

    if (is_wp_error($user)) {
        wp_send_json_error(array('message' => 'Mot de passe incorrect. Réessayez ou utilisez « Mot de passe oublié ? ».'));
    }

    wp_send_json_success(array('message' => 'Connexion réussie.'));
}
add_action('wp_ajax_nopriv_pp_login', 'poolparty_g4_ajax_login');
add_action('wp_ajax_pp_login', 'poolparty_g4_ajax_login');

/* =============================================================
   2. INSCRIPTION
   ============================================================= */

function poolparty_g4_ajax_register() {
    check_ajax_referer('pp_auth', 'nonce');

    // Anti-spam : champ appât invisible (rempli seulement par les robots).
    if (!empty($_POST['pp_site_web'])) {
        wp_send_json_success(array('message' => 'ok'));
    }

    $prenom = isset($_POST['prenom']) ? sanitize_text_field(wp_unslash($_POST['prenom'])) : '';
    $nom    = isset($_POST['nom']) ? sanitize_text_field(wp_unslash($_POST['nom'])) : '';
    $email  = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $pass   = isset($_POST['password']) ? (string) $_POST['password'] : '';
    $cgu    = !empty($_POST['cgu']);

    if ($prenom === '' || $nom === '') {
        wp_send_json_error(array('message' => 'Merci d\'indiquer votre prénom et votre nom.'));
    }
    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Cette adresse e-mail n\'est pas valide.'));
    }
    if (strlen($pass) < 8) {
        wp_send_json_error(array('message' => 'Le mot de passe doit contenir au moins 8 caractères.'));
    }
    if (!$cgu) {
        wp_send_json_error(array('message' => 'Merci d\'accepter les conditions d\'utilisation.'));
    }
    if (email_exists($email)) {
        wp_send_json_error(array('message' => 'Un compte existe déjà avec cette adresse e-mail. Connectez-vous.'));
    }

    // Identifiant dérivé de l'e-mail, rendu unique.
    $base = sanitize_user(current(explode('@', $email)), true);
    if ($base === '') {
        $base = 'membre';
    }
    $login = $base;
    $i = 1;
    while (username_exists($login)) {
        $login = $base . $i;
        $i++;
    }

    $user_id = wp_insert_user(array(
        'user_login'   => $login,
        'user_email'   => $email,
        'user_pass'    => $pass,
        'first_name'   => $prenom,
        'last_name'    => $nom,
        'display_name' => $prenom,
        'role'         => 'locataire',
    ));

    if (is_wp_error($user_id)) {
        wp_send_json_error(array('message' => 'La création du compte a échoué. Réessayez.'));
    }

    // Connexion immédiate (cookie d'authentification).
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);

    // Jetons frais pour le nouvel utilisateur : les jetons émis pour le
    // visiteur non connecté ne seraient plus valides après connexion (ils
    // dépendent de l'identifiant du compte). Utile au tunnel « Proposer »
    // qui enchaîne inscription puis publication de l'annonce.
    wp_send_json_success(array(
        'message'          => 'Votre compte est créé.',
        'bienNonce'        => wp_create_nonce('pp_bien'),
        'reservationNonce' => wp_create_nonce('pp_reservation'),
    ));
}
add_action('wp_ajax_nopriv_pp_register', 'poolparty_g4_ajax_register');
add_action('wp_ajax_pp_register', 'poolparty_g4_ajax_register');

/* =============================================================
   3. MOT DE PASSE OUBLIÉ
   ============================================================= */

function poolparty_g4_ajax_reset() {
    check_ajax_referer('pp_auth', 'nonce');

    $identifiant = isset($_POST['email']) ? sanitize_text_field(wp_unslash($_POST['email'])) : '';

    // retrieve_password() lit $_POST['user_login'] et envoie le vrai e-mail
    // de réinitialisation (fonctionnel en production avec le SMTP configuré).
    if ($identifiant !== '') {
        $_POST['user_login'] = $identifiant;
        retrieve_password();
    }

    // Réponse volontairement générique : on n'indique jamais si un compte
    // existe pour cette adresse (pas d'énumération des membres).
    wp_send_json_success(array(
        'message' => 'Si un compte est associé à cette adresse, un e-mail de réinitialisation vient de partir.',
    ));
}
add_action('wp_ajax_nopriv_pp_reset', 'poolparty_g4_ajax_reset');
add_action('wp_ajax_pp_reset', 'poolparty_g4_ajax_reset');

/**
 * Renvoie des jetons frais pour la session courante. Utile après une
 * connexion / inscription programmatique : les jetons émis dans la même
 * requête que wp_set_auth_cookie() ne sont pas fiables (le token de session
 * n'est lu qu'à la requête suivante). Le tunnel « Proposer » appelle ce
 * point d'entrée juste après l'inscription pour obtenir un jeton pp_bien
 * valide avant de publier. Renvoyer un jeton n'est pas sensible : il est lié
 * à la session de l'appelant.
 */
function poolparty_g4_ajax_nonce() {
    wp_send_json_success(array(
        'bienNonce'        => wp_create_nonce('pp_bien'),
        'reservationNonce' => wp_create_nonce('pp_reservation'),
        'authNonce'        => wp_create_nonce('pp_auth'),
    ));
}
add_action('wp_ajax_pp_nonce', 'poolparty_g4_ajax_nonce');
add_action('wp_ajax_nopriv_pp_nonce', 'poolparty_g4_ajax_nonce');

/**
 * Peut accéder à l'espace « Demandes de réservation » (gérer ses annonces) ?
 * Modèle unifié type Airbnb : un seul type de membre, à la fois locataire ET
 * hôte potentiel. Tout membre connecté y a droit ; il n'y verra toutefois que
 * les demandes portant sur SES propres annonces (contrôle par la propriété du
 * bien, côté serveur, dans inc/reservations.php). Aucun rôle spécial requis.
 */
function poolparty_g4_est_hote($user = null) {
    if ($user === null) {
        return is_user_logged_in();
    }
    return $user && $user->exists();
}

/* =============================================================
   4. BACK-OFFICE RÉSERVÉ À L'ADMINISTRATEUR
   Les membres font tout depuis le site : ils n'ont aucune raison
   d'entrer dans WordPress. On renvoie vers l'accueil tout non-admin
   qui tenterait d'ouvrir /wp-admin, et on masque la barre noire.
   ============================================================= */

function poolparty_g4_bloquer_backoffice() {
    // On laisse passer les appels AJAX (admin-ajax.php) : le front en a besoin.
    if (!current_user_can('manage_options') && !wp_doing_ajax()) {
        wp_safe_redirect(home_url('/'));
        exit;
    }
}
add_action('admin_init', 'poolparty_g4_bloquer_backoffice');

function poolparty_g4_masquer_admin_bar($show) {
    return current_user_can('manage_options') ? $show : false;
}
add_filter('show_admin_bar', 'poolparty_g4_masquer_admin_bar');
