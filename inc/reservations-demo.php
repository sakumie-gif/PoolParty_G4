<?php
/**
 * Réservations de démonstration.
 * -------------------------------------------------------------
 * Comme le reste de l'espace connecté du projet (favoris, messagerie),
 * la connexion est simulée et les réservations vivent dans le navigateur
 * (localStorage). Ce fichier fournit UNIQUEMENT le jeu de réservations
 * de démonstration injecté au premier affichage connecté : quelques
 * demandes rattachées à de vrais biens du catalogue et à leurs hôtes,
 * pour que la page Mes réservations ne soit pas vide en démonstration.
 *
 * L'architecture cible (table Réservation) est présentée dans
 * schema-bdd.html pour la soutenance.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Construit les réservations de démonstration à partir de vrais biens
 * et de leurs hôtes. Retourne un tableau prêt à passer à main.js via
 * ppData ; chaque entrée a la même forme qu'une demande enregistrée par
 * le tunnel « Confirmer et payer » (titre, image, lien, hôte, date au
 * format JJ/MM/AAAA, créneau, invités, formule, total, statut).
 */
function poolparty_g4_reservations_demo() {
    $biens = get_posts(array(
        'post_type'      => 'bien',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
    ));

    // Trames : décalage en jours par rapport à aujourd'hui (négatif =
    // réservation passée), créneau, invités, formule et prix HT (les
    // frais de service de 15% s'ajoutent, comme dans le tunnel).
    $trames = array(
        array('delta' => 6,   'creneau' => 'Après-midi 14h-18h', 'invites' => '2 adultes, 2 enfants', 'formule' => 'Demi-journée privative', 'prix' => 90),
        array('delta' => 24,  'creneau' => 'Journée 10h-19h',    'invites' => '4 adultes',            'formule' => 'Journée privative',      'prix' => 170),
        array('delta' => -34, 'creneau' => 'Matin 9h-13h',       'invites' => '2 adultes, 1 enfant',  'formule' => 'Demi-journée privative', 'prix' => 90),
    );

    $reservations = array();
    $maintenant = current_time('timestamp'); // horodatage local du site
    $index = 0;

    foreach ($biens as $bien_id) {
        if ($index >= count($trames)) {
            break;
        }
        $id_hote = poolparty_g4_meta($bien_id, 'id_hote');
        $hote = $id_hote ? poolparty_g4_get_hote($id_hote) : null;
        if (!$hote || empty($hote['prenom'])) {
            continue; // pas d'hôte renseigné : on passe au bien suivant
        }

        $t = $trames[$index];
        $ts = $maintenant + ($t['delta'] * DAY_IN_SECONDS);
        $total = $t['prix'] * 1.15; // frais de service 15%

        $reservations[] = array(
            'id'      => 'demo-' . $bien_id,
            'titre'   => get_the_title($bien_id),
            'image'   => poolparty_g4_image_url($bien_id),
            'alt'     => poolparty_g4_meta($bien_id, 'alt'),
            'lien'    => get_permalink($bien_id),
            'hote'    => $hote['prenom'],
            'date'    => date('d/m/Y', $ts),
            'creneau' => $t['creneau'],
            'invites' => $t['invites'],
            'formule' => $t['formule'],
            'total'   => number_format($total, 2, ',', ' ') . '€',
            'statut'  => $t['delta'] < 0 ? 'terminee' : 'en-attente',
        );
        $index++;
    }

    return $reservations;
}
