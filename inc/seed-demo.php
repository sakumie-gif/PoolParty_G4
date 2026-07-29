<?php
/**
 * Jeu de démonstration de la page « Mes réservations » (V2).
 * -------------------------------------------------------------
 * Garnit le compte de démonstration (Sophie Marchand) des deux côtés de la
 * bascule Hôte / Locataire, pour la soutenance :
 *  - onglet Locataire : ce qu'elle a réservé chez un autre membre ;
 *  - onglet Hôte : les demandes reçues sur ses annonces.
 *
 * Une réservation met toujours en jeu deux membres différents (le même type
 * de compte « Membre »). On crée donc un membre « contrepartie », Camille
 * Laurent, qui réserve chez Sophie et chez qui Sophie réserve. Camille n'est
 * pas présenté : il sert uniquement à rendre les données crédibles.
 *
 * Tout est marqué d'une méta / méta de commentaire pp_demo = 1 : la fonction
 * nettoie ses propres données avant de les recréer, elle est donc rejouable
 * sans laisser de doublons. Incrémenter la version pour rejouer.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PP_DEMO_DATA_VERSION', '2');
define('PP_DEMO_CLIENT_LOGIN', 'camille.laurent');
define('PP_DEMO_CLIENT_EMAIL', 'camille.laurent@poolparty-demo.fr');

/**
 * Crée / retrouve le membre « contrepartie » et renvoie son identifiant.
 */
function poolparty_g4_demo_client_id() {
    $u = get_user_by('login', PP_DEMO_CLIENT_LOGIN);
    if (!$u) {
        $u = get_user_by('email', PP_DEMO_CLIENT_EMAIL);
    }
    if (!$u) {
        $u = get_user_by('email', 'camille.laurent@example.com');
    }
    if ($u) {
        $u->set_role('membre');
        return (int) $u->ID;
    }
    $id = wp_insert_user(array(
        'user_login'   => PP_DEMO_CLIENT_LOGIN,
        'user_email'   => PP_DEMO_CLIENT_EMAIL,
        'user_pass'    => wp_generate_password(20),
        'first_name'   => 'Camille',
        'last_name'    => 'Laurent',
        'display_name' => 'Camille Laurent',
        'role'         => 'membre',
    ));
    return is_wp_error($id) ? 0 : (int) $id;
}

/**
 * Crée une réservation de démonstration et renvoie son identifiant.
 *
 * @param int    $auteur   Locataire qui réserve (post_author).
 * @param int    $bien_id  Annonce concernée.
 * @param int    $hote_id  Propriétaire de l'annonce (méta pp_hote_id).
 * @param int    $delta    Décalage en jours vs aujourd'hui (négatif = passée).
 * @param string $statut   pp_statut (en-attente, acceptee...).
 * @param array  $details  creneau, invites, formule, prix, message.
 */
function poolparty_g4_demo_creer_resa($auteur, $bien_id, $hote_id, $delta, $statut, $details) {
    $ts    = current_time('timestamp') + ($delta * DAY_IN_SECONDS);
    $date  = date('d/m/Y', $ts);
    $total = number_format($details['prix'] * 1.15, 2, ',', ' ') . '€';

    $resa_id = wp_insert_post(array(
        'post_type'   => 'reservation',
        'post_status' => 'publish',
        'post_author' => $auteur,
        'post_title'  => sprintf('%s, %s', get_the_title($bien_id), $date),
    ), true);
    if (is_wp_error($resa_id) || !$resa_id) {
        return 0;
    }

    $metas = array(
        'pp_bien_id'  => $bien_id,
        'pp_hote_id'  => $hote_id,
        'pp_statut'   => $statut,
        'pp_date'     => $date,
        'pp_creneau'  => $details['creneau'],
        'pp_invites'  => $details['invites'],
        'pp_formule'  => $details['formule'],
        'pp_total'    => $total,
        'pp_echeance' => 'Paiement comptant',
        'pp_paiement' => 'Carte bancaire',
        'pp_garantie' => 'Non',
        'pp_message'  => isset($details['message']) ? $details['message'] : '',
        'pp_demo'     => 1,
    );
    foreach ($metas as $cle => $valeur) {
        update_post_meta($resa_id, $cle, $valeur);
    }
    return (int) $resa_id;
}

/**
 * Publie un avis (pp_avis) sur un bien, avec sa note, la réservation d'origine
 * et, en option, la réponse publique de l'hôte (pp_avis_reponse).
 */
function poolparty_g4_demo_creer_avis($bien_id, $auteur_id, $resa_id, $note, $texte, $reponse_id = 0, $reponse_texte = '') {
    $auteur = get_userdata($auteur_id);
    $avis_id = wp_insert_comment(array(
        'comment_post_ID'  => $bien_id,
        'comment_type'     => 'pp_avis',
        'comment_content'  => $texte,
        'user_id'          => $auteur_id,
        'comment_author'   => $auteur ? $auteur->display_name : 'Membre',
        'comment_approved' => 1,
        'comment_date'     => date('Y-m-d H:i:s', current_time('timestamp')),
    ));
    if (!$avis_id) {
        return;
    }
    add_comment_meta($avis_id, 'pp_note', (int) $note);
    add_comment_meta($avis_id, 'pp_resa_id', (int) $resa_id);
    add_comment_meta($avis_id, 'pp_demo', 1);

    if ($reponse_id && $reponse_texte) {
        $repondant = get_userdata($reponse_id);
        $rep_id = wp_insert_comment(array(
            'comment_post_ID'  => $bien_id,
            'comment_type'     => 'pp_avis_reponse',
            'comment_parent'   => $avis_id,
            'comment_content'  => $reponse_texte,
            'user_id'          => $reponse_id,
            'comment_author'   => $repondant ? $repondant->display_name : 'Hôte',
            'comment_approved' => 1,
            'comment_date'     => date('Y-m-d H:i:s', current_time('timestamp')),
        ));
        if ($rep_id) {
            add_comment_meta($rep_id, 'pp_demo', 1);
        }
    }
}

/**
 * Supprime toutes les données marquées pp_demo (réservations + avis), pour
 * pouvoir régénérer proprement.
 */
function poolparty_g4_demo_nettoyer() {
    $resas = get_posts(array(
        'post_type'      => 'reservation',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_key'       => 'pp_demo',
        'meta_value'     => 1,
    ));
    foreach ($resas as $rid) {
        wp_delete_post($rid, true);
    }

    $avis = get_comments(array(
        'meta_key'   => 'pp_demo',
        'meta_value' => 1,
        'fields'     => 'ids',
    ));
    foreach ($avis as $cid) {
        wp_delete_comment($cid, true);
    }
}

/**
 * Construit tout le jeu de démonstration. Idempotent (verrou par version).
 */
function poolparty_g4_seed_demo() {
    if (get_option('pp_demo_data_version') === PP_DEMO_DATA_VERSION) {
        return;
    }

    $membre_id = (int) get_option('pp_membre_demo_id');
    if (!$membre_id) {
        return; // le compte de démo n'est pas encore prêt, on retentera
    }

    // Tout le catalogue, quel que soit le propriétaire du moment : en cas de
    // rejeu, on repart d'une répartition neutre (tout au membre de démo)
    // avant de redonner deux annonces au client.
    $biens = get_posts(array(
        'post_type'      => 'bien',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'fields'         => 'ids',
    ));
    if (count($biens) < 5) {
        return; // biens pas encore rattachés, on retentera au prochain chargement
    }

    // Verrou posé seulement une fois les prérequis réunis.
    if (!add_option('pp_demo_claim_' . PP_DEMO_DATA_VERSION, 1, '', false)) {
        return;
    }

    $client_id = poolparty_g4_demo_client_id();
    if (!$client_id) {
        return;
    }

    poolparty_g4_demo_nettoyer();

    // Deux annonces passent au client : elles servent aux réservations que le
    // membre de démo fait EN TANT QUE LOCATAIRE (crédible : on ne réserve pas
    // chez soi). Le reste appartient au membre de démo (côté Hôte).
    $biens_client = array_slice($biens, 0, 2);
    $biens_membre = array_values(array_slice($biens, 2));
    foreach ($biens_client as $bien_id) {
        if ((int) get_post_field('post_author', $bien_id) !== $client_id) {
            wp_update_post(array('ID' => $bien_id, 'post_author' => $client_id));
        }
    }
    foreach ($biens_membre as $bien_id) {
        if ((int) get_post_field('post_author', $bien_id) !== $membre_id) {
            wp_update_post(array('ID' => $bien_id, 'post_author' => $membre_id));
        }
    }

    /* ---- Onglet Locataire (Sophie réserve chez Camille) ---- */
    // À venir, confirmée.
    poolparty_g4_demo_creer_resa($membre_id, $biens_client[0], $client_id, 9, 'acceptee', array(
        'creneau' => 'Journée 10h-19h', 'invites' => '4 adultes', 'formule' => 'Journée privative', 'prix' => 120,
    ));
    // Passée, confirmée, sans avis : apparaît « à évaluer ».
    poolparty_g4_demo_creer_resa($membre_id, $biens_client[1], $client_id, -20, 'acceptee', array(
        'creneau' => 'Après-midi 14h-18h', 'invites' => '2 adultes, 2 enfants', 'formule' => 'Demi-journée privative', 'prix' => 90,
    ));
    // Passée, confirmée, avec un avis déjà publié (et la réponse de l'hôte).
    $resa_l3 = poolparty_g4_demo_creer_resa($membre_id, $biens_client[0], $client_id, -45, 'acceptee', array(
        'creneau' => 'Matin 9h-13h', 'invites' => '2 adultes', 'formule' => 'Demi-journée privative', 'prix' => 90,
    ));

    /* ---- Onglet Hôte (Camille réserve chez Sophie) ---- */
    // En attente : boutons Accepter / Refuser à démontrer en direct.
    poolparty_g4_demo_creer_resa($client_id, $biens_membre[0], $membre_id, 5, 'en-attente', array(
        'creneau' => 'Après-midi 14h-18h', 'invites' => '6 adultes, 2 enfants', 'formule' => 'Demi-journée privative', 'prix' => 110,
        'message' => "Bonjour, nous fêtons un anniversaire en petit comité. La piscine est-elle chauffée cet après-midi là ? Merci d'avance.",
    ));
    // À venir, confirmée.
    poolparty_g4_demo_creer_resa($client_id, $biens_membre[1], $membre_id, 18, 'acceptee', array(
        'creneau' => 'Journée 10h-19h', 'invites' => '4 adultes, 3 enfants', 'formule' => 'Journée privative', 'prix' => 170,
    ));
    // Passée, confirmée, avec un avis reçu du client (et la réponse de Sophie).
    $resa_h3 = poolparty_g4_demo_creer_resa($client_id, $biens_membre[2], $membre_id, -30, 'acceptee', array(
        'creneau' => 'Matin 9h-13h', 'invites' => '3 adultes', 'formule' => 'Demi-journée privative', 'prix' => 90,
    ));

    /* ---- Avis ---- */
    // Avis publié par Sophie sur l'annonce de Camille + réponse de Camille.
    if ($resa_l3) {
        poolparty_g4_demo_creer_avis(
            $biens_client[0], $membre_id, $resa_l3, 5,
            "Un cadre magnifique et une eau parfaite. Accueil aux petits soins, on reviendra sans hésiter.",
            $client_id,
            "Merci beaucoup Sophie, vous êtes les bienvenus quand vous voulez !"
        );
    }
    // Avis reçu par Sophie de la part de Camille + réponse de Sophie.
    if ($resa_h3) {
        poolparty_g4_demo_creer_avis(
            $biens_membre[2], $client_id, $resa_h3, 5,
            "Espace très propre et conforme à l'annonce. Hôte réactive et arrangeante, je recommande.",
            $membre_id,
            "Merci Camille pour votre visite et votre message, à bientôt !"
        );
    }

    update_option('pp_demo_data_version', PP_DEMO_DATA_VERSION);
}
add_action('init', 'poolparty_g4_seed_demo', 40);
