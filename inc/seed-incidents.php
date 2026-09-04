<?php
/**
 * Signalements de démonstration pour la console d'administration.
 * -------------------------------------------------------------
 * La section « Incidents » de la page /administration/ reste vide tant
 * qu'aucun membre n'a déposé de signalement. Ce seed crée deux dossiers
 * rattachés aux réservations du jeu de démonstration :
 *  - un signalement déposé par le locataire, encore ouvert ;
 *  - un signalement déposé par l'hôte, en cours de traitement, avec sa
 *    note interne, pour montrer les deux états et la pop-up de suivi.
 *
 * Le déclarant est toujours le membre contrepartie (Camille Laurent) :
 * le compte de démonstration garde ainsi ses boutons « Signaler un
 * problème » disponibles pour la démonstration en direct.
 *
 * Marqués pp_demo = 1 : le seed nettoie ses propres dossiers avant de les
 * recréer. Incrémenter la version pour rejouer.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PP_INCIDENTS_DEMO_VERSION', '1');

/**
 * Réservations de démonstration confirmées et déjà passées, pour un
 * locataire donné, de la plus ancienne à la plus récente.
 */
function poolparty_g4_incidents_demo_resas($locataire_id) {
    $resas = get_posts(array(
        'post_type'      => 'reservation',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'author'         => (int) $locataire_id,
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'meta_query'     => array(
            'relation' => 'AND',
            array('key' => 'pp_demo', 'value' => 1),
            array('key' => 'pp_statut', 'value' => 'acceptee'),
        ),
    ));

    $passees    = array();
    $aujourdhui = current_time('timestamp');
    foreach ($resas as $resa) {
        $date = DateTime::createFromFormat('d/m/Y', (string) get_post_meta($resa->ID, 'pp_date', true));
        if ($date && $date->getTimestamp() < $aujourdhui) {
            $passees[] = $resa;
        }
    }
    return $passees;
}

/**
 * Crée un signalement rattaché à une réservation et renvoie son identifiant.
 *
 * @param WP_Post $resa   Réservation concernée (confirmée).
 * @param string  $role   Rôle du déclarant : locataire ou hote.
 * @param string  $motif  Clé de motif (voir poolparty_g4_incident_motifs).
 * @param string  $texte  Description du membre.
 * @param string  $statut Statut de traitement.
 * @param string  $note   Note interne de l'équipe (facultative).
 * @param int     $depuis Ancienneté du dépôt, en jours.
 */
function poolparty_g4_incidents_demo_creer($resa, $role, $motif, $texte, $statut, $note = '', $depuis = 5) {
    $locataire_id = (int) $resa->post_author;
    $hote_id      = (int) get_post_meta($resa->ID, 'pp_hote_id', true);
    $declarant_id = $role === 'hote' ? $hote_id : $locataire_id;
    $autre_id     = $role === 'hote' ? $locataire_id : $hote_id;
    if (!$declarant_id || !$autre_id) {
        return 0;
    }

    $bien_id   = (int) get_post_meta($resa->ID, 'pp_bien_id', true);
    $declarant = get_userdata($declarant_id);
    $depot     = current_time('timestamp') - ($depuis * DAY_IN_SECONDS);

    $incident_id = wp_insert_post(array(
        'post_type'    => 'pp_incident',
        'post_status'  => 'publish',
        'post_author'  => $declarant_id,
        'post_title'   => sprintf(
            '%s, %s, %s',
            $bien_id ? get_the_title($bien_id) : $resa->post_title,
            get_post_meta($resa->ID, 'pp_date', true) ?: 'sans date',
            $declarant ? $declarant->display_name : 'membre'
        ),
        'post_content' => $texte,
        'post_date'    => date('Y-m-d H:i:s', $depot),
    ), true);
    if (is_wp_error($incident_id) || !$incident_id) {
        return 0;
    }

    $metas = array(
        'pp_resa_id'        => (int) $resa->ID,
        'pp_bien_id'        => $bien_id,
        'pp_declarant_id'   => $declarant_id,
        'pp_autre_id'       => $autre_id,
        'pp_role_declarant' => $role,
        'pp_motif'          => $motif,
        'pp_statut'         => $statut,
        'pp_demo'           => 1,
    );
    if ($note !== '') {
        $metas['pp_note_interne'] = $note;
    }
    foreach ($metas as $cle => $valeur) {
        update_post_meta($incident_id, $cle, $valeur);
    }
    return (int) $incident_id;
}

/** Supprime les signalements de démonstration déjà en base. */
function poolparty_g4_incidents_demo_nettoyer() {
    $incidents = get_posts(array(
        'post_type'      => 'pp_incident',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'meta_key'       => 'pp_demo',
        'meta_value'     => 1,
    ));
    foreach ($incidents as $incident_id) {
        wp_delete_post($incident_id, true);
    }
}

/** Construit le jeu de signalements. Idempotent (verrou par version). */
function poolparty_g4_seed_incidents_demo() {
    if (get_option('pp_incidents_demo_version') === PP_INCIDENTS_DEMO_VERSION) {
        return;
    }

    $membre_id = (int) get_option('pp_membre_demo_id');
    if (!$membre_id) {
        return; // compte de démonstration pas encore prêt, on retentera
    }

    $client = get_user_by('login', PP_DEMO_CLIENT_LOGIN);
    if (!$client) {
        return; // le jeu de démonstration n'est pas encore posé
    }

    // Réservations passées de chaque côté de la bascule : le client chez le
    // membre de démonstration, puis le membre de démonstration chez le client.
    $chez_membre = poolparty_g4_incidents_demo_resas((int) $client->ID);
    $chez_client = poolparty_g4_incidents_demo_resas($membre_id);
    if (!$chez_membre || !$chez_client) {
        return; // réservations pas encore créées, on retentera
    }

    // Verrou posé seulement une fois les prérequis réunis.
    if (!add_option('pp_incidents_claim_' . PP_INCIDENTS_DEMO_VERSION, 1, '', false)) {
        return;
    }

    poolparty_g4_incidents_demo_nettoyer();

    // Signalement du locataire, encore ouvert.
    poolparty_g4_incidents_demo_creer(
        $chez_membre[0],
        'locataire',
        'non-conforme',
        "L'annonce indiquait une piscine chauffée et une douche extérieure. À notre arrivée l'eau était à 19 degrés et la douche ne fonctionnait pas. Nous avons écourté l'après-midi avec les enfants. Nous souhaitons savoir ce qu'il est possible de faire sur le montant réglé.",
        'ouvert',
        '',
        3
    );

    // Signalement de l'hôte, en cours de traitement, avec la note de l'équipe.
    poolparty_g4_incidents_demo_creer(
        end($chez_client),
        'hote',
        'degradation',
        "Deux transats ont été cassés pendant la venue et le local technique a été ouvert alors que le règlement de l'annonce l'interdit. Le groupe était plus nombreux que le nombre d'invités annoncé. Je demande la prise en charge du remplacement des transats.",
        'en-cours',
        "Photos reçues et cohérentes avec la description. Locataire contacté par la messagerie interne, réponse attendue sous 48 heures. Devis des transats à demander avant de proposer un geste commercial.",
        9
    );

    update_option('pp_incidents_demo_version', PP_INCIDENTS_DEMO_VERSION);
}
add_action('init', 'poolparty_g4_seed_incidents_demo', 45);
