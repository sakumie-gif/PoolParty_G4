<?php
/**
 * Messagerie interne (démonstration).
 * -------------------------------------------------------------
 * La messagerie permet, une fois connecté, d'échanger avec l'hôte
 * d'un espace sans passer par l'e-mail. Comme le reste de l'espace
 * connecté du projet (favoris, réservations), la connexion est
 * simulée et les conversations vivent dans le navigateur
 * (localStorage). Ce fichier fournit UNIQUEMENT le jeu de
 * conversations de démonstration injecté au premier affichage :
 * quelques fils locataire ↔ hôte pré-remplis, rattachés à de vrais
 * biens du catalogue, pour que la boîte de réception ne soit pas
 * vide lors de la démonstration.
 *
 * L'architecture cible (tables Conversation et Message) est
 * présentée dans schema-bdd.html pour la soutenance.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Construit les conversations de démonstration à partir de vrais
 * biens et de leurs hôtes (table de référence poolparty_g4_hotes()).
 * Retourne un tableau prêt à être passé à main.js via ppData.
 *
 * Chaque conversation : identifiant, hôte (prénom + photo), bien
 * rattaché (titre + lien), horodatage de dernière activité (ms, pour
 * le tri) et liste de messages { de: 'hote'|'moi', texte, label }.
 */
function poolparty_g4_messagerie_seed() {
    // On récupère quelques biens récents ; on ne garde que ceux dont
    // l'hôte est renseigné (le fil doit avoir un interlocuteur réel).
    $biens = get_posts(array(
        'post_type'      => 'bien',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'fields'         => 'ids',
    ));

    // Trames de messages selon la position dans la liste, pour varier
    // le contenu sans inventer de fausses données personnelles.
    $trames = array(
        array(
            array('de' => 'hote', 'texte' => "Bonjour et bienvenue ! Merci pour votre intérêt pour mon espace. N'hésitez pas si vous avez la moindre question avant de réserver.", 'delta' => 172800),
            array('de' => 'moi',  'texte' => "Bonjour, merci ! L'eau est-elle chauffée en ce moment, et à quelle température ?", 'delta' => 169200),
            array('de' => 'hote', 'texte' => "Oui, l'eau est chauffée autour de 28°C toute la saison. Vous serez au chaud même en soirée.", 'delta' => 165600),
        ),
        array(
            array('de' => 'moi',  'texte' => "Bonjour, est-il possible de venir à 6 personnes plutôt que 4 ? On serait deux de plus.", 'delta' => 86400),
            array('de' => 'hote', 'texte' => "Bonjour ! Oui, aucun souci pour 6 personnes, l'espace est prévu pour. Je vous confirme dès que votre demande arrive.", 'delta' => 82800),
        ),
        array(
            array('de' => 'hote', 'texte' => "Bonjour, je vois que votre réservation approche. Voici le code du portail : 1974. À très vite !", 'delta' => 10800),
        ),
    );

    $conversations = array();
    $maintenant = current_time('timestamp'); // horodatage local du site
    $index = 0;

    foreach ($biens as $bien_id) {
        if ($index >= count($trames)) {
            break;
        }
        $id_hote = get_post_meta($bien_id, 'pp_id_hote', true);
        $hote = $id_hote ? poolparty_g4_get_hote($id_hote) : null;
        if (!$hote) {
            continue; // pas d'hôte renseigné : on passe au bien suivant
        }

        $trame = $trames[$index];
        $messages = array();
        $derniere = 0;
        foreach ($trame as $m) {
            $ts = $maintenant - (int) $m['delta'];
            $derniere = $ts;
            $messages[] = array(
                'de'    => $m['de'],
                'texte' => $m['texte'],
                'label' => poolparty_g4_messagerie_label($ts, $maintenant),
            );
        }

        $conversations[] = array(
            'id'        => 'conv-' . $bien_id,
            'hote'      => $hote['prenom'],
            'photo'     => pp_asset($hote['photo']),
            'bienId'    => $bien_id,
            'bienTitre' => get_the_title($bien_id),
            'bienLien'  => get_permalink($bien_id),
            'maj'       => $derniere * 1000, // ms, pour trier côté JS
            'messages'  => $messages,
        );
        $index++;
    }

    return $conversations;
}

/**
 * Libellé court d'horodatage pour un message (« Aujourd'hui 10:12 »,
 * « Hier 18:30 », sinon « 7 juil. 11:05 »). Indépendant de la locale.
 */
function poolparty_g4_messagerie_label($ts, $maintenant) {
    $heure = date('H:i', $ts);
    $jour  = date('Y-m-d', $ts);
    if ($jour === date('Y-m-d', $maintenant)) {
        return "Aujourd'hui " . $heure;
    }
    if ($jour === date('Y-m-d', $maintenant - 86400)) {
        return 'Hier ' . $heure;
    }
    $mois = array(
        1 => 'janv.', 2 => 'févr.', 3 => 'mars', 4 => 'avr.',
        5 => 'mai', 6 => 'juin', 7 => 'juil.', 8 => 'août',
        9 => 'sept.', 10 => 'oct.', 11 => 'nov.', 12 => 'déc.',
    );
    return intval(date('j', $ts)) . ' ' . $mois[intval(date('n', $ts))] . ' ' . $heure;
}
