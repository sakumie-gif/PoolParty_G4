<?php
/**
 * Disponibilités de démonstration.
 * -------------------------------------------------------------
 * Les biens du jeu de départ n'ont ni jours d'accueil ni dates bloquées :
 * le calendrier de la fiche reste ouvert tous les jours et la recherche
 * par date n'écarte rien. Ce seed renseigne les jours d'accueil et deux
 * dates bloquées sur une partie des biens, et confirme une réservation
 * à venir sur l'un d'eux pour montrer un créneau déjà pris.
 *
 * Ne touche jamais un bien dont l'hôte a déjà renseigné ses jours.
 * Ne s'exécute qu'une fois (verrou par option), incrémenter la version
 * pour rejouer.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PP_DISPO_DEMO_VERSION', '1');

function poolparty_g4_seed_dispo() {
    if (get_option('pp_dispo_demo_version') === PP_DISPO_DEMO_VERSION) {
        return;
    }
    if (get_option('pp_demo_data_version') !== PP_DEMO_DATA_VERSION) {
        return; // les réservations de démonstration ne sont pas encore posées
    }
    if (!add_option('pp_dispo_demo_claim_' . PP_DISPO_DEMO_VERSION, 1, '', false)) {
        return;
    }

    $maintenant = current_time('timestamp');
    $dans = function ($jours) use ($maintenant) {
        return date('d/m/Y', $maintenant + $jours * DAY_IN_SECONDS);
    };

    $plannings = array(
        'piscine-torcy-1'  => array('jours' => 'Mercredi,Vendredi,Samedi,Dimanche', 'indispos' => $dans(6) . ',' . $dans(13)),
        'piscine-chelles'  => array('jours' => 'Samedi,Dimanche',                    'indispos' => ''),
        'jacuzzi-croissy'  => array('jours' => 'Jeudi,Vendredi,Samedi,Dimanche',     'indispos' => $dans(9)),
        'spa-pantin'       => array('jours' => 'Lundi,Mardi,Mercredi,Jeudi,Vendredi', 'indispos' => ''),
        'piscine-champs'   => array('jours' => 'Mercredi,Samedi,Dimanche',           'indispos' => $dans(4)),
        'piscine-paris'    => array('jours' => 'Samedi,Dimanche',                    'indispos' => ''),
        'sauna-vincennes'  => array('jours' => 'Vendredi,Samedi,Dimanche',           'indispos' => ''),
        'hammam-montreuil' => array('jours' => 'Mardi,Jeudi,Samedi',                 'indispos' => $dans(8)),
    );

    foreach ($plannings as $slug => $planning) {
        $bien = get_page_by_path($slug, OBJECT, 'bien');
        if (!$bien || get_post_meta($bien->ID, 'pp_jours', true) !== '') {
            continue;
        }
        update_post_meta($bien->ID, 'pp_jours', $planning['jours']);
        if ($planning['indispos'] !== '' && get_post_meta($bien->ID, 'pp_indispos', true) === '') {
            update_post_meta($bien->ID, 'pp_indispos', $planning['indispos']);
        }
    }

    // Réservation confirmée le prochain samedi (au moins deux jours devant)
    // sur la piscine intérieure chauffée : l'après-midi apparaît « réservé ».
    $bien = get_page_by_path('piscine-torcy-1', OBJECT, 'bien');
    $membre_id = (int) get_option('pp_membre_demo_id');
    $client_id = function_exists('poolparty_g4_demo_client_id') ? (int) poolparty_g4_demo_client_id() : 0;
    if ($bien && $membre_id && $client_id && function_exists('poolparty_g4_demo_creer_resa')) {
        $hote_id   = (int) $bien->post_author;
        $auteur_id = ($hote_id === $membre_id) ? $client_id : $membre_id;
        $delta     = 2;
        while ((int) date('w', $maintenant + $delta * DAY_IN_SECONDS) !== 6) {
            $delta++;
        }
        poolparty_g4_demo_creer_resa($auteur_id, $bien->ID, $hote_id, $delta, 'acceptee', array(
            'creneau' => 'Après-midi 14h-18h',
            'invites' => '2 adultes, 1 enfant',
            'formule' => 'Demi-journée privative',
            'prix'    => 4 * (float) get_post_meta($bien->ID, 'pp_prix_heure', true),
        ));
    }

    update_option('pp_dispo_demo_version', PP_DISPO_DEMO_VERSION);
}
add_action('init', 'poolparty_g4_seed_dispo', 45);
