<?php
/**
 * Avis de départ du catalogue (vrais commentaires WordPress).
 * -------------------------------------------------------------
 * Avant, les commentaires affichés sur les fiches produit étaient un décor :
 * un tableau de textes dans single-bien.php, invisible dans l'admin et non
 * modérable. Ce fichier importe ce jeu de départ en base, sous forme de
 * commentaires pp_avis rattachés à chaque bien : la fiche les lit comme les
 * vrais avis des membres, et tout se modère depuis l'écran Commentaires >
 * Avis (masquer un avis le retire aussitôt de la fiche).
 *
 * Chaque bien reçoit au plus « pp_nb_avis » avis (borne du compteur du CMS),
 * le marqueur %HOTE% est remplacé par le prénom réel de l'hôte de la fiche.
 * Marqués pp_catalogue = 1 : rejouable proprement en incrémentant la version.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PP_AVIS_CATALOGUE_VERSION', '1');

/**
 * Jeu de départ : le contenu de démonstration historique des fiches.
 */
function poolparty_g4_pool_avis_catalogue() {
    return array(
        array('nom' => 'Marie L.',   'depuis' => '9 mois', 'note' => 4, 'date' => 'Mai 2025',     'texte' => "Superbe après-midi passée dans la piscine de %HOTE%. Le cadre est magnifique, très calme et sans aucun vis-à-vis. L'eau était à une température parfaite. %HOTE% a été très accueillant tout en restant discret. Nous reviendrons !"),
        array('nom' => 'Thomas R.',  'depuis' => '1 mois', 'note' => 4, 'date' => 'Juillet 2026', 'texte' => "Tout était nickel : portillon séparé donc aucune gêne, transats confortables, douche extérieure très pratique. On a passé la journée à six adultes, l'espace était largement suffisant. Mention spéciale pour le barbecue, top pour le déjeuner."),
        array('nom' => 'Sophie B.',  'depuis' => '6 mois', 'note' => 3, 'date' => 'Juin 2026',    'texte' => "Très belle piscine, bien entretenue, et hôtes adorables. Le seul petit bémol c'est le bruit de la rue qu'on entend par moments, mais ça reste mineur. On a quand même passé un super moment, les enfants ont adoré."),
        array('nom' => 'Yoann D.',   'depuis' => '2 ans',  'note' => 4, 'date' => 'Juin 2026',    'texte' => "On voulait fêter notre anniversaire de mariage dans un endroit calme à dix minutes de Paris. C'était exactement ça. Bassin propre, jardin bien planté, accueil parfait. %HOTE% nous a même recommandé un restaurant à côté, on a adoré."),
        array('nom' => 'Camille P.', 'depuis' => '4 mois', 'note' => 5, 'date' => 'Août 2026',    'texte' => "Journée parfaite entre amis, la piscine est spacieuse et l'eau limpide. %HOTE% répond vite aux messages et donne toutes les infos avant l'arrivée. Rien à redire."),
        array('nom' => 'Nicolas F.', 'depuis' => '1 an',   'note' => 5, 'date' => 'Août 2026',    'texte' => "Endroit idéal pour couper de l'agitation parisienne sans partir loin. Le jardin est bien entretenu et très fleuri, on se croirait en vacances."),
        array('nom' => 'Aurélie M.', 'depuis' => '7 mois', 'note' => 4, 'date' => 'Juillet 2026', 'texte' => "Super moment en famille, les enfants ne voulaient plus sortir de l'eau. Un peu de mal à se garer le samedi, mais l'accueil compense largement."),
        array('nom' => 'Karim B.',   'depuis' => '2 ans',  'note' => 5, 'date' => 'Juillet 2026', 'texte' => "Le coin est très propre et le portillon indépendant assure une vraie intimité. Douche extérieure appréciable après la baignade. Je recommande sans hésiter."),
        array('nom' => 'Léa V.',     'depuis' => '5 mois', 'note' => 5, 'date' => 'Juillet 2026', 'texte' => "On a réservé pour un anniversaire, tout était impeccable. Transats confortables, coin ombragé bienvenu en pleine chaleur. Merci %HOTE% pour ta disponibilité."),
        array('nom' => 'Antoine G.', 'depuis' => '3 mois', 'note' => 4, 'date' => 'Juin 2026',    'texte' => "Belle piscine chauffée, cadre agréable et calme. Le seul bémol reste l'accès un peu étroit, mais une fois sur place on oublie tout."),
        array('nom' => 'Fatou D.',   'depuis' => '8 mois', 'note' => 5, 'date' => 'Juin 2026',    'texte' => "Cadre reposant, on a passé un après-midi au calme à lire au bord de l'eau. %HOTE% nous a laissé profiter en toute tranquillité."),
        array('nom' => 'Mathieu L.', 'depuis' => '1 an',   'note' => 5, 'date' => 'Juin 2026',    'texte' => "Parfait pour un déjeuner entre collègues, le barbecue est un vrai plus. Espace suffisant à sept adultes, on reviendra pour la rentrée."),
        array('nom' => 'Chloé R.',   'depuis' => '2 mois', 'note' => 3, 'date' => 'Mai 2026',     'texte' => "Piscine agréable et hôte sympathique. On entend un peu la circulation aux heures de pointe, mais rien de rédhibitoire pour une baignade."),
        array('nom' => 'Sarah K.',   'depuis' => '6 mois', 'note' => 5, 'date' => 'Mai 2026',     'texte' => "Un havre de paix à deux pas de Paris. Le jardin est superbe et très bien tenu. Nous avons adoré, l'adresse est notée pour l'été prochain."),
        array('nom' => 'Romain C.',  'depuis' => '10 mois','note' => 4, 'date' => 'Avril 2026',   'texte' => "Très bon accueil et piscine nickel. Un peu frais en avril malgré le chauffage, mais l'endroit est vraiment charmant et bien pensé."),
        array('nom' => 'Inès H.',    'depuis' => '3 mois', 'note' => 5, 'date' => 'Avril 2026',   'texte' => "Escapade parfaite pour se détendre. Le cadre verdoyant fait tout de suite baisser la pression. Communication au top avant et pendant le séjour."),
        array('nom' => 'Paul E.',    'depuis' => '1 an',   'note' => 5, 'date' => 'Mars 2026',    'texte' => "Séance de nage matinale magnifique dans un jardin calme. %HOTE% pense à tout, serviettes et boissons fraîches mises à disposition. Bravo."),
        array('nom' => 'Manon S.',   'depuis' => '5 mois', 'note' => 4, 'date' => 'Mars 2026',    'texte' => "Endroit agréable et propre, hôte aux petits soins. On aurait aimé un peu plus de rangements pour les affaires, mais rien de gênant."),
        array('nom' => 'Julie A.',   'depuis' => '9 mois', 'note' => 5, 'date' => 'Février 2026', 'texte' => "Bassin impeccable et ambiance très reposante, même en hiver le coin est agréable. %HOTE% est arrangeant sur les horaires, un vrai plaisir."),
    );
}

/**
 * Importe le jeu de départ pour chaque bien publié. Idempotent (verrou de
 * version) ; nettoie les avis d'une version précédente avant de recréer.
 */
function poolparty_g4_seed_avis_catalogue() {
    if (get_option('pp_avis_catalogue_version') === PP_AVIS_CATALOGUE_VERSION) {
        return;
    }

    $biens = get_posts(array(
        'post_type'      => 'bien',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'ID',
        'order'          => 'ASC',
        'fields'         => 'ids',
    ));
    if (empty($biens)) {
        return; // import des biens pas encore joué, on retentera
    }

    // Prise de verrou atomique : une seule requête lance l'import.
    if (!add_option('pp_avis_catalogue_claim_' . PP_AVIS_CATALOGUE_VERSION, 1, '', false)) {
        return;
    }

    // L'import crée plusieurs centaines de commentaires d'un coup : on
    // s'accorde une marge de temps d'exécution.
    if (function_exists('set_time_limit')) {
        @set_time_limit(120);
    }

    // Nettoie les avis d'une version précédente.
    $anciens = get_comments(array(
        'meta_key'   => 'pp_catalogue',
        'meta_value' => 1,
        'fields'     => 'ids',
    ));
    foreach ($anciens as $cid) {
        wp_delete_comment($cid, true);
    }

    $pool       = poolparty_g4_pool_avis_catalogue();
    $maintenant = current_time('timestamp');

    foreach ($biens as $bien_id) {
        $nb = min((int) poolparty_g4_meta($bien_id, 'nb_avis'), count($pool));
        $hote   = poolparty_g4_get_hote(poolparty_g4_meta($bien_id, 'id_hote'));
        $prenom = ($hote && !empty($hote['prenom'])) ? $hote['prenom'] : 'votre hôte';

        for ($i = 0; $i < $nb; $i++) {
            $avis = $pool[$i];
            $cid  = wp_insert_comment(array(
                'comment_post_ID'  => $bien_id,
                'comment_type'     => 'pp_avis',
                'comment_author'   => $avis['nom'],
                'comment_content'  => str_replace('%HOTE%', $prenom, $avis['texte']),
                'comment_approved' => 1,
                'user_id'          => 0,
                // Dates décroissantes le long du jeu pour préserver l'ordre
                // d'affichage historique (tri par date, plus récent d'abord).
                'comment_date'     => date('Y-m-d H:i:s', $maintenant - (($i + 1) * DAY_IN_SECONDS)),
            ));
            if ($cid) {
                add_comment_meta($cid, 'pp_note', (int) $avis['note']);
                add_comment_meta($cid, 'pp_depuis', $avis['depuis']);
                add_comment_meta($cid, 'pp_date_label', $avis['date']);
                add_comment_meta($cid, 'pp_catalogue', 1);
            }
        }
    }

    update_option('pp_avis_catalogue_version', PP_AVIS_CATALOGUE_VERSION);
}
add_action('init', 'poolparty_g4_seed_avis_catalogue', 35);
