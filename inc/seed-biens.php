<?php
/**
 * Import automatique des biens dans WordPress.
 * -------------------------------------------------------------
 * Reprend le jeu de données de data/biens.js (les 16 annonces) et
 * le charge dans la base : création des cinq catégories (taxonomie
 * categorie_bien) puis d'un post « Bien » par annonce, avec son
 * titre, sa description (contenu de l'éditeur), son image et tous
 * ses champs personnalisés (pp_*).
 *
 * L'import ne s'exécute qu'une seule fois (verrou via une option).
 * Chaque bien est aussi protégé par un contrôle d'existence sur son
 * slug, pour ne jamais créer de doublon. Pour relancer l'import
 * après avoir ajouté des biens ici, il suffit d'incrémenter
 * PP_BIENS_SEED_VERSION.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Numéro de version de l'import. À incrémenter pour rejouer le seed
// (les biens déjà présents ne sont pas recréés, seuls les nouveaux
// sont ajoutés).
define('PP_BIENS_SEED_VERSION', '1');

/**
 * Catégories à créer dans la taxonomie categorie_bien.
 * La clé (slug) correspond au champ id_categorie de chaque bien.
 */
function poolparty_g4_seed_categories() {
    return array(
        'piscine' => 'Piscine',
        'jacuzzi' => 'Jacuzzi',
        'spa'     => 'Spa',
        'sauna'   => 'Sauna',
        'hammam'  => 'Hammam',
    );
}

/**
 * Les 16 biens à importer. Mêmes données que data/biens.js, enrichies
 * d'une description (deux paragraphes : un résumé visible, puis un
 * détail dévoilé par « Lire la suite » sur la fiche).
 */
function poolparty_g4_seed_biens() {
    return array(
        array(
            'slug' => 'piscine-torcy-1', 'categorie' => 'piscine', 'id_hote' => 'h-julien',
            'titre' => 'Piscine intérieure chauffée', 'ville' => 'Torcy', 'pays' => 'France',
            'distance_km' => 4, 'prix_heure' => 23, 'prix_ancien' => '',
            'capacite_min' => 4, 'capacite_max' => 5, 'note' => '4.7', 'nb_avis' => 5,
            'lat' => '48.850', 'lon' => '2.653',
            'image' => 'assets/images/piscines/annonce-torcy.jpg',
            'alt' => 'Piscine intérieure chauffée avec fresque murale à Torcy',
            'tag' => 'top-vente', 'favori_id' => '',
            'description' => "Une piscine intérieure chauffée toute l'année, à l'abri des regards et de la météo. Le bassin est habillé d'une fresque murale qui donne à l'espace une ambiance chaleureuse, parfaite pour une baignade en petit comité.\n\nL'eau est maintenue autour de 28 degrés, idéale pour les enfants comme pour les longueurs tranquilles. Un vestiaire et une douche chaude sont accessibles sur place. Un cadre couvert qui reste agréable même quand le soleil se fait attendre.",
        ),
        array(
            'slug' => 'piscine-chelles', 'categorie' => 'piscine', 'id_hote' => 'h-sarah',
            'titre' => 'Bassin en pierre de caractère', 'ville' => 'Chelles', 'pays' => 'France',
            'distance_km' => 3, 'prix_heure' => 23, 'prix_ancien' => '',
            'capacite_min' => 6, 'capacite_max' => 7, 'note' => '4.9', 'nb_avis' => 12,
            'lat' => '48.883', 'lon' => '2.590',
            'image' => 'assets/images/piscines/annonce-chelles.jpg',
            'alt' => "Bassin en pierre dans la cour d'une maison de caractère à Chelles",
            'tag' => 'proche', 'favori_id' => '',
            'description' => "Un bassin en pierre niché dans la cour d'une maison de caractère, à quelques minutes seulement du centre. Le charme de l'ancien rencontre le confort d'une eau limpide et bien entretenue.\n\nLa cour close offre une vraie intimité pour recevoir famille ou amis. Transats, coin ombragé et accès indépendant font de cet espace une parenthèse au calme, sans vis-à-vis.",
        ),
        array(
            'slug' => 'jacuzzi-croissy', 'categorie' => 'jacuzzi', 'id_hote' => 'h-yoann',
            'titre' => 'Jacuzzi sur terrasse bois', 'ville' => 'Croissy-Beaubourg', 'pays' => 'France',
            'distance_km' => 5, 'prix_heure' => 22, 'prix_ancien' => '',
            'capacite_min' => 7, 'capacite_max' => 7, 'note' => '4.7', 'nb_avis' => 9,
            'lat' => '48.827', 'lon' => '2.672',
            'image' => 'assets/images/piscines/annonce-croissy.jpg',
            'alt' => 'Jacuzzi gonflable sur une terrasse en bois à Croissy-Beaubourg',
            'tag' => '', 'favori_id' => '',
            'description' => "Un jacuzzi installé sur une belle terrasse en bois, pensé pour les soirées détente entre amis. Les bulles chaudes et l'éclairage tamisé créent une atmosphère cocooning dès la tombée du jour.\n\nLa terrasse accueille aussi un coin salon pour prolonger le moment. Serviettes et enceinte disponibles sur demande. Idéal pour se relaxer après une longue semaine.",
        ),
        array(
            'slug' => 'spa-pantin', 'categorie' => 'spa', 'id_hote' => 'h-paula',
            'titre' => 'Spa privatif baigné de lumière', 'ville' => 'Pantin', 'pays' => 'France',
            'distance_km' => 5, 'prix_heure' => 23, 'prix_ancien' => '',
            'capacite_min' => 8, 'capacite_max' => 8, 'note' => '4.7', 'nb_avis' => 5,
            'lat' => '48.894', 'lon' => '2.409',
            'image' => 'assets/images/cta/sauna.jpg',
            'alt' => 'Spa privatif en bois clair baigné de lumière à Pantin',
            'tag' => '', 'favori_id' => 'spa-pantin',
            'description' => "Un spa privatif tout en bois clair, baigné de lumière naturelle grâce à ses larges ouvertures. L'espace respire le calme et invite à la relaxation.\n\nJusqu'à huit personnes peuvent profiter du bassin et de ses jets massants. Un lieu élégant pour un anniversaire, un moment en famille ou simplement pour souffler.",
        ),
        array(
            'slug' => 'piscine-champs', 'categorie' => 'piscine', 'id_hote' => 'h-julien',
            'titre' => 'Piscine avec vue sur la vallée', 'ville' => 'Champs-sur-Marne', 'pays' => 'France',
            'distance_km' => 11, 'prix_heure' => 20, 'prix_ancien' => '',
            'capacite_min' => 5, 'capacite_max' => 6, 'note' => '4.6', 'nb_avis' => 3,
            'lat' => '48.853', 'lon' => '2.603',
            'image' => 'assets/images/piscines/annonce-champs.jpg',
            'alt' => 'Piscine avec vue dégagée sur la vallée à Champs-sur-Marne',
            'tag' => 'top-vente', 'favori_id' => '',
            'description' => "Une piscine avec une vue dégagée sur la vallée, où l'on nage face à l'horizon. Le cadre paysager, planté d'arbres et de graminées, isole totalement du voisinage.\n\nLa terrasse ensoleillée accueille transats et parasol pour les journées d'été. Un accès facile et un stationnement à proximité complètent le tableau. Un vrai bol d'air à deux pas de Paris.",
        ),
        array(
            'slug' => 'piscine-torcy-2', 'categorie' => 'piscine', 'id_hote' => 'h-sarah',
            'titre' => 'Piscine ombragée par les arbres', 'ville' => 'Torcy', 'pays' => 'France',
            'distance_km' => 7, 'prix_heure' => 21, 'prix_ancien' => 25,
            'capacite_min' => 7, 'capacite_max' => 8, 'note' => '4.7', 'nb_avis' => 5,
            'lat' => '48.858', 'lon' => '2.665',
            'image' => 'assets/images/categories/categorie-3.jpg',
            'alt' => 'Piscine ombragée par de grands arbres à Torcy',
            'tag' => 'promo', 'favori_id' => '',
            'description' => "Une piscine généreuse, ombragée par de grands arbres qui rafraîchissent naturellement les après-midis d'été. L'espace convient parfaitement aux grandes tablées.\n\nLe jardin arboré offre plusieurs coins pour se poser à l'ombre ou au soleil. Un barbecue et une grande table de jardin sont à disposition. En ce moment proposée à tarif réduit.",
        ),
        array(
            'slug' => 'jacuzzi-bussy', 'categorie' => 'jacuzzi', 'id_hote' => 'h-yoann',
            'titre' => 'Jacuzzi encastré devant mur en pierre', 'ville' => 'Bussy-Saint-Georges', 'pays' => 'France',
            'distance_km' => 8, 'prix_heure' => 24, 'prix_ancien' => '',
            'capacite_min' => 4, 'capacite_max' => 4, 'note' => '4.7', 'nb_avis' => 5,
            'lat' => '48.842', 'lon' => '2.712',
            'image' => 'assets/images/piscines/annonce-noisy.jpg',
            'alt' => 'Jacuzzi encastré dans une terrasse en bois devant un mur en pierre à Bussy-Saint-Georges',
            'tag' => '', 'favori_id' => 'jacuzzi-bussy',
            'description' => "Un jacuzzi encastré dans une terrasse en bois, adossé à un élégant mur en pierre. Le décor soigné donne à l'espace un vrai cachet.\n\nParfait pour un moment à deux ou entre amis proches, l'endroit est intime et facile d'accès. Éclairage d'ambiance le soir et serviettes fournies.",
        ),
        array(
            'slug' => 'piscine-saint-thibault', 'categorie' => 'piscine', 'id_hote' => 'h-paula',
            'titre' => 'Piscine de villa contemporaine', 'ville' => 'Saint-Thibault-des-Vignes', 'pays' => 'France',
            'distance_km' => 19, 'prix_heure' => 21, 'prix_ancien' => '',
            'capacite_min' => 10, 'capacite_max' => 10, 'note' => '4.7', 'nb_avis' => 5,
            'lat' => '48.870', 'lon' => '2.680',
            'image' => 'assets/images/piscines/annonce-pantin.jpg',
            'alt' => "Piscine d'une villa contemporaine entourée de verdure à Saint-Thibault-des-Vignes",
            'tag' => '', 'favori_id' => 'piscine-saint-thibault',
            'description' => "La piscine d'une villa contemporaine, entourée de verdure et d'une architecture soignée. L'espace, spacieux, se prête aux grandes réunions.\n\nJusqu'à dix personnes peuvent profiter du bassin et du vaste jardin. Mobilier de jardin, coin repas et parasols sont sur place. Un cadre haut de gamme pour un événement mémorable.",
        ),
        array(
            'slug' => 'piscine-pontault', 'categorie' => 'piscine', 'id_hote' => 'h-julien',
            'titre' => 'Bassin turquoise en jardin arboré', 'ville' => 'Pontault-Combault', 'pays' => 'France',
            'distance_km' => 16, 'prix_heure' => 23, 'prix_ancien' => '',
            'capacite_min' => 6, 'capacite_max' => 6, 'note' => '4.7', 'nb_avis' => 5,
            'lat' => '48.786', 'lon' => '2.605',
            'image' => 'assets/images/categories/categorie-2.jpg',
            'alt' => "Bassin turquoise au coeur d'un jardin arboré à Pontault-Combault",
            'tag' => 'nouveau', 'favori_id' => '',
            'description' => "Un bassin aux reflets turquoise, posé au cœur d'un jardin arboré et fleuri. La végétation crée une bulle de fraîcheur et d'intimité.\n\nNouvellement disponible, cet espace propose transats, douche extérieure et un grand coin détente. Idéal pour une journée en famille au vert.",
        ),
        array(
            'slug' => 'piscine-joinville', 'categorie' => 'piscine', 'id_hote' => 'h-tony',
            'titre' => 'Bassin dans un patio verdoyant', 'ville' => 'Joinville-le-Pont', 'pays' => 'France',
            'distance_km' => 11, 'prix_heure' => 23, 'prix_ancien' => '',
            'capacite_min' => 12, 'capacite_max' => 12, 'note' => '4.7', 'nb_avis' => 5,
            'lat' => '48.821', 'lon' => '2.473',
            'image' => 'assets/images/piscines/annonce-lagny.jpg',
            'alt' => "Bassin dans un patio verdoyant vu depuis l'étage à Joinville-le-Pont",
            'tag' => '', 'favori_id' => '',
            'description' => "Un bassin niché dans un patio verdoyant, visible depuis l'étage pour un effet oasis en pleine ville. L'espace accueille facilement les grands groupes.\n\nAvec une capacité de douze personnes, il convient aux fêtes et grandes retrouvailles. Le patio protégé garantit calme et discrétion tout au long de la journée.",
        ),
        array(
            'slug' => 'piscine-paris', 'categorie' => 'piscine', 'id_hote' => 'h-sarah',
            'titre' => 'Couloir de nage au crépuscule', 'ville' => 'Paris', 'pays' => 'France',
            'distance_km' => 15, 'prix_heure' => 24, 'prix_ancien' => '',
            'capacite_min' => 5, 'capacite_max' => 5, 'note' => '4.7', 'nb_avis' => 5,
            'lat' => '48.860', 'lon' => '2.412',
            'image' => 'assets/images/categories/categorie-4.jpg',
            'alt' => 'Couloir de nage au crépuscule à Paris',
            'tag' => '', 'favori_id' => '',
            'description' => "Un couloir de nage élégant, particulièrement beau au crépuscule quand l'éclairage se reflète sur l'eau. Pensé pour nager et se détendre en plein Paris.\n\nL'espace, intimiste, accueille jusqu'à cinq personnes. Un lieu rare et raffiné, idéal pour une pause sportive ou une soirée au bord de l'eau.",
        ),
        array(
            'slug' => 'piscine-chessy', 'categorie' => 'piscine', 'id_hote' => 'h-paula',
            'titre' => 'Piscine intérieure végétalisée', 'ville' => 'Chessy', 'pays' => 'France',
            'distance_km' => 15, 'prix_heure' => 23, 'prix_ancien' => '',
            'capacite_min' => 8, 'capacite_max' => 8, 'note' => '4.7', 'nb_avis' => 5,
            'lat' => '48.885', 'lon' => '2.765',
            'image' => 'assets/images/categories/categorie-5.jpg',
            'alt' => 'Piscine intérieure entourée de végétation luxuriante à Chessy',
            'tag' => '', 'favori_id' => '',
            'description' => "Une piscine intérieure entourée d'une végétation luxuriante, comme un jardin d'hiver tropical. L'ambiance y est chaude et dépaysante en toute saison.\n\nÀ l'abri de la météo, l'espace reste agréable été comme hiver. Jusqu'à huit personnes peuvent profiter du bassin et du coin détente végétalisé.",
        ),
        array(
            'slug' => 'sauna-vincennes', 'categorie' => 'sauna', 'id_hote' => 'h-tony',
            'titre' => 'Sauna finlandais en bois clair', 'ville' => 'Vincennes', 'pays' => 'France',
            'distance_km' => 9, 'prix_heure' => 19, 'prix_ancien' => '',
            'capacite_min' => 2, 'capacite_max' => 4, 'note' => '4.8', 'nb_avis' => 7,
            'lat' => '48.847', 'lon' => '2.437',
            'image' => 'assets/images/evenements/detente.jpg',
            'alt' => 'Sauna finlandais en bois clair avec banquettes à Vincennes',
            'tag' => 'nouveau', 'favori_id' => '',
            'description' => "Un authentique sauna finlandais en bois clair, avec ses banquettes chaleureuses et sa chaleur sèche enveloppante. Un vrai rituel bien-être à deux pas du bois de Vincennes.\n\nRécemment ajouté au catalogue, il accueille de deux à quatre personnes pour une séance revitalisante. Une douche et un espace de repos sont attenants.",
        ),
        array(
            'slug' => 'hammam-montreuil', 'categorie' => 'hammam', 'id_hote' => 'h-tony',
            'titre' => 'Hammam traditionnel en zellige', 'ville' => 'Montreuil', 'pays' => 'France',
            'distance_km' => 8, 'prix_heure' => 21, 'prix_ancien' => '',
            'capacite_min' => 2, 'capacite_max' => 6, 'note' => '4.6', 'nb_avis' => 4,
            'lat' => '48.864', 'lon' => '2.443',
            'image' => 'assets/images/evenements/famille.jpg',
            'alt' => 'Hammam traditionnel habillé de zellige à Montreuil',
            'tag' => '', 'favori_id' => '',
            'description' => "Un hammam traditionnel habillé de zellige, où la vapeur chaude et les carreaux colorés recréent l'atmosphère des bains orientaux. Un moment de détente hors du temps.\n\nL'espace accueille de deux à six personnes pour une séance en toute intimité. Peignoirs et thé à la menthe proposés sur demande.",
        ),
        array(
            'slug' => 'spa-nogent', 'categorie' => 'spa', 'id_hote' => 'h-paula',
            'titre' => 'Spa de nage avec jets massants', 'ville' => 'Nogent-sur-Marne', 'pays' => 'France',
            'distance_km' => 12, 'prix_heure' => 26, 'prix_ancien' => 30,
            'capacite_min' => 4, 'capacite_max' => 6, 'note' => '4.8', 'nb_avis' => 8,
            'lat' => '48.837', 'lon' => '2.482',
            'image' => 'assets/images/evenements/anniversaire.jpg',
            'alt' => 'Spa de nage avec jets massants et éclairage tamisé à Nogent-sur-Marne',
            'tag' => 'promo', 'favori_id' => '',
            'description' => "Un spa de nage équipé de puissants jets massants, permettant à la fois de nager à contre-courant et de se relaxer. L'éclairage tamisé installe une ambiance apaisante.\n\nProposé en ce moment à tarif réduit, il accueille de quatre à six personnes. Un équipement complet pour allier sport doux et récupération.",
        ),
        array(
            'slug' => 'jacuzzi-meaux', 'categorie' => 'jacuzzi', 'id_hote' => 'h-yoann',
            'titre' => 'Jacuzzi panoramique en rooftop', 'ville' => 'Meaux', 'pays' => 'France',
            'distance_km' => 24, 'prix_heure' => 25, 'prix_ancien' => '',
            'capacite_min' => 2, 'capacite_max' => 5, 'note' => '4.9', 'nb_avis' => 11,
            'lat' => '48.960', 'lon' => '2.878',
            'image' => 'assets/images/evenements/pool-party.jpg',
            'alt' => 'Jacuzzi panoramique installé sur un rooftop à Meaux',
            'tag' => 'top-vente', 'favori_id' => '',
            'description' => "Un jacuzzi panoramique installé en rooftop, avec une vue imprenable, magique au coucher du soleil. L'un de nos espaces les plus demandés.\n\nDe deux à cinq personnes peuvent profiter des bulles chaudes face au paysage. Transats et coin lounge complètent ce cadre spectaculaire pour une soirée d'exception.",
        ),
    );
}

/**
 * Exécute l'import : crée les catégories manquantes puis les biens
 * manquants. Idempotent (ne recrée jamais un bien déjà présent).
 */
function poolparty_g4_importer_biens() {
    // Verrou : ne rien faire si cette version de l'import a déjà tourné.
    if (get_option('pp_biens_seed_version') === PP_BIENS_SEED_VERSION) {
        return;
    }

    // 1. Catégories (termes de la taxonomie categorie_bien)
    foreach (poolparty_g4_seed_categories() as $slug => $nom) {
        if (!term_exists($slug, 'categorie_bien')) {
            wp_insert_term($nom, 'categorie_bien', array('slug' => $slug));
        }
    }

    // 2. Biens (posts du type « bien »)
    // Champs personnalisés attendus par la méta-box (préfixe pp_).
    $champs_meta = array(
        'ville', 'pays', 'distance_km', 'prix_heure', 'prix_ancien',
        'capacite_min', 'capacite_max', 'note', 'nb_avis', 'lat', 'lon',
        'tag', 'image', 'alt', 'id_hote', 'favori_id',
    );

    foreach (poolparty_g4_seed_biens() as $bien) {
        // Anti-doublon : si un bien porte déjà ce slug, on passe.
        if (get_page_by_path($bien['slug'], OBJECT, 'bien')) {
            continue;
        }

        $post_id = wp_insert_post(array(
            'post_type'    => 'bien',
            'post_status'  => 'publish',
            'post_title'   => $bien['titre'],
            'post_name'    => $bien['slug'],
            'post_content' => $bien['description'],
        ));

        if (!$post_id || is_wp_error($post_id)) {
            continue;
        }

        // Champs personnalisés
        foreach ($champs_meta as $cle) {
            if (isset($bien[$cle]) && $bien[$cle] !== '') {
                update_post_meta($post_id, 'pp_' . $cle, $bien[$cle]);
            }
        }

        // Description : déclenché sur le hook init (hors admin), très tôt
        // dans le chargement, wp_insert_post peut ne PAS conserver
        // post_content. On garantit donc son écriture en base une fois le
        // post créé, directement via $wpdb, puis on vide le cache du post.
        $cree = get_post($post_id);
        if ($cree && trim($cree->post_content) === '' && !empty($bien['description'])) {
            global $wpdb;
            $wpdb->update(
                $wpdb->posts,
                array('post_content' => $bien['description']),
                array('ID' => $post_id)
            );
            clean_post_cache($post_id);
        }

        // Catégorie (clé étrangère id_categorie -> terme de la taxonomie)
        wp_set_object_terms($post_id, $bien['categorie'], 'categorie_bien', false);
    }

    update_option('pp_biens_seed_version', PP_BIENS_SEED_VERSION);
}
// Priorité 20 : après l'enregistrement du CPT et de la taxonomie (init/10).
add_action('init', 'poolparty_g4_importer_biens', 20);
