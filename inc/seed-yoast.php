<?php
/**
 * Import automatique des métadonnées Yoast SEO.
 * -------------------------------------------------------------
 * Les réglages Yoast vivent en base de données, pas dans les
 * fichiers du thème : ils ne suivent donc pas le déploiement. Ce
 * fichier rejoue la partie « contenu » de cette configuration sur
 * n'importe quelle installation : mot-clé et méta-description des
 * 16 biens, 15 pages et 7 articles, puis mise en « ne pas indexer »
 * des pages de service (espace membre, tunnel de réservation).
 *
 * Les contenus sont retrouvés par leur slug, les identifiants
 * numériques différant d'une installation à l'autre. Une valeur
 * déjà saisie n'est jamais écrasée : une méta-description tapée à
 * la main dans l'administration reste intacte.
 *
 * L'import ne s'exécute qu'une fois (verrou par option). Pour le
 * rejouer après avoir complété les données, incrémenter
 * PP_YOAST_SEED_VERSION.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PP_YOAST_SEED_VERSION', '3');

/**
 * Mot-clé principal et méta-description de chaque contenu, par slug.
 * Zone verte Yoast visée : 120 à 156 caractères.
 */
function poolparty_g4_yoast_metas() {
    return array(

        // Les 16 biens du catalogue.
        'piscine-torcy-1' => array(
            'kw'   => 'piscine intérieure chauffée',
            'desc' => 'Louez une piscine intérieure chauffée à 28°C près de Torcy : baignade toute l\'année à l\'abri, vestiaire et douche chaude. Réservez votre créneau détente.',
        ),
        'piscine-chelles' => array(
            'kw'   => 'piscine en pierre',
            'desc' => 'Louez un bassin en pierre plein de charme près de Chelles : cour close et intime, transats et coin ombragé. Réservez votre parenthèse au calme.',
        ),
        'jacuzzi-croissy' => array(
            'kw'   => 'jacuzzi terrasse bois',
            'desc' => 'Louez un jacuzzi sur terrasse bois à Croissy : bulles chaudes, éclairage tamisé et coin salon. Réservez votre moment cocooning entre amis.',
        ),
        'spa-pantin' => array(
            'kw'   => 'spa privatif',
            'desc' => 'Louez un spa privatif baigné de lumière à Pantin : jets massants jusqu\'à 8 personnes, idéal anniversaire ou détente en famille. Réservez votre séance.',
        ),
        'piscine-champs' => array(
            'kw'   => 'piscine avec vue',
            'desc' => 'Louez une piscine avec vue sur la vallée à Champs-sur-Marne : cadre paysager sans vis-à-vis et terrasse ensoleillée. Réservez votre bol d\'air près de Paris.',
        ),
        'piscine-torcy-2' => array(
            'kw'   => 'piscine ombragée',
            'desc' => 'Louez une grande piscine ombragée près de Torcy : jardin arboré, barbecue et grande table pour les grandes tablées. Tarif réduit en ce moment.',
        ),
        'jacuzzi-bussy' => array(
            'kw'   => 'jacuzzi encastré',
            'desc' => 'Louez un jacuzzi encastré face à un mur en pierre à Bussy-Saint-Georges : espace intime et soigné, serviettes fournies. Réservez entre amis.',
        ),
        'piscine-saint-thibault' => array(
            'kw'   => 'piscine de villa',
            'desc' => 'Louez la piscine d\'une villa contemporaine à Saint-Thibault : bassin spacieux jusqu\'à 10 personnes et grand jardin. Réservez votre événement.',
        ),
        'piscine-pontault' => array(
            'kw'   => 'piscine jardin arboré',
            'desc' => 'Louez un bassin turquoise dans un jardin arboré à Pontault-Combault : transats et douche extérieure. Idéal en famille au vert. Réservez votre journée.',
        ),
        'piscine-joinville' => array(
            'kw'   => 'piscine grand groupe',
            'desc' => 'Louez un bassin dans un patio verdoyant à Joinville-le-Pont : effet oasis en ville, jusqu\'à 12 personnes. Parfait pour vos fêtes et retrouvailles.',
        ),
        'piscine-paris' => array(
            'kw'   => 'couloir de nage',
            'desc' => 'Louez un couloir de nage élégant en plein Paris : nage et détente, superbe au crépuscule, jusqu\'à 5 personnes. Réservez votre pause au bord de l\'eau.',
        ),
        'piscine-chessy' => array(
            'kw'   => 'piscine intérieure',
            'desc' => 'Louez une piscine intérieure végétalisée à Chessy : ambiance jardin d\'hiver tropical, agréable toute l\'année. Réservez votre évasion dépaysante.',
        ),
        'sauna-vincennes' => array(
            'kw'   => 'sauna finlandais',
            'desc' => 'Louez un sauna finlandais authentique près du bois de Vincennes : chaleur sèche, 2 à 4 personnes, douche et repos. Réservez votre rituel bien-être.',
        ),
        'hammam-montreuil' => array(
            'kw'   => 'hammam traditionnel',
            'desc' => 'Louez un hammam traditionnel en zellige à Montreuil : vapeur chaude et ambiance orientale, 2 à 6 personnes. Réservez votre détente hors du temps.',
        ),
        'spa-nogent' => array(
            'kw'   => 'spa de nage',
            'desc' => 'Louez un spa de nage à jets massants à Nogent-sur-Marne : nage à contre-courant et relaxation, 4 à 6 personnes. Réservez votre séance.',
        ),
        'jacuzzi-meaux' => array(
            'kw'   => 'jacuzzi rooftop',
            'desc' => 'Louez un jacuzzi panoramique en rooftop à Meaux : vue imprenable, bulles chaudes pour 2 à 5 personnes, coin lounge et transats. Réservez votre soirée d\'exception.',
        ),

        // Les pages de contenu.
        'a-propos' => array(
            'kw'   => 'location de piscines entre particuliers',
            'desc' => 'Découvrez PoolParty, la location de piscines, jacuzzis et spas entre particuliers. Notre mission : rendre la baignade accessible près de chez vous.',
        ),
        'faq' => array(
            'kw'   => 'questions fréquentes',
            'desc' => 'Toutes les réponses à vos questions sur la location de piscines entre particuliers : réservation, paiement, annulation et accès aux biens PoolParty.',
        ),
        'contact' => array(
            'kw'   => 'contacter PoolParty',
            'desc' => 'Une question sur une location de piscine ou votre réservation ? Contactez l\'équipe PoolParty : nous vous répondons rapidement pour vous accompagner.',
        ),
        'presse' => array(
            'kw'   => '',
            'desc' => 'Espace presse de Pool Party : communiqués, chiffres clés, retombées média et kit presse à télécharger pour parler de la location d\'espaces aquatiques.',
        ),
        'partenaires' => array(
            'kw'   => '',
            'desc' => 'Découvrez les partenaires de Pool Party et rejoignez le réseau : services et marques qui accompagnent la location de piscines entre particuliers.',
        ),
        'devenir-partenaire' => array(
            'kw'   => 'proposer votre piscine',
            'desc' => 'Vous avez une piscine, un jacuzzi ou un spa ? Devenez hôte PoolParty, proposez votre espace à la location et générez des revenus en toute simplicité.',
        ),
        'moyen-de-paiement' => array(
            'kw'   => '',
            'desc' => 'Carte bancaire, paiement en plusieurs fois, virement : tous les moyens de paiement acceptés sur Pool Party pour réserver votre espace simplement.',
        ),
        'paiement-securise' => array(
            'kw'   => '',
            'desc' => 'Paiement 100% sécurisé sur Pool Party : transactions chiffrées, données protégées et débit seulement après accord de l\'hôte. Réservez en confiance.',
        ),
        'accessibilite' => array(
            'kw'   => '',
            'desc' => 'Déclaration d\'accessibilité de Pool Party : nos engagements pour un site utilisable par tous, conforme au RGAA, et comment nous signaler un souci.',
        ),
        'mentions-legales' => array(
            'kw'   => '',
            'desc' => 'Mentions légales de Pool Party : éditeur du site, hébergeur, propriété intellectuelle et informations réglementaires de la plateforme de location entre particuliers.',
        ),
        'cgu' => array(
            'kw'   => '',
            'desc' => 'Conditions générales d\'utilisation de Pool Party : règles d\'usage de la plateforme, droits et obligations des hôtes et des locataires.',
        ),
        'cgv' => array(
            'kw'   => '',
            'desc' => 'Conditions générales de vente de Pool Party : réservation, tarifs, paiement, annulation et dépôt de garantie pour la location entre particuliers.',
        ),
        'proposer' => array(
            'kw'   => '',
            'desc' => 'Proposez votre piscine, jacuzzi, spa, sauna ou hammam à la location sur Pool Party et générez des revenus complémentaires. Inscription simple et gratuite pour les hôtes.',
        ),
        'assurance' => array(
            'kw'   => '',
            'desc' => 'Chaque réservation Pool Party est couverte par une assurance incluse. Découvrez les garanties qui protègent hôtes et locataires à chaque baignade.',
        ),
        'actualites' => array(
            'kw'   => '',
            'desc' => 'Toute l\'actualité de Pool Party : nouveaux espaces à louer, conseils baignade et événements autour de la location de piscines et spas entre particuliers.',
        ),
        'plan-du-site' => array(
            'kw'   => 'plan du site',
            'desc' => 'Toutes les pages de Pool Party réunies : rubriques du site, catégories d\'espaces, annonces de piscines à louer et articles du journal.',
        ),

        // Les articles du blog.
        'reservation-instantanee-ete-2026' => array(
            'kw'   => '',
            'desc' => 'Réservez votre piscine en un clic dès l\'été 2026 : Pool Party lance la réservation instantanée, sans attendre la validation de l\'hôte. Découvrez comment ça marche.',
        ),
        'cinq-conseils-apres-midi-baignade' => array(
            'kw'   => '',
            'desc' => 'Cinq conseils simples pour réussir votre après-midi baignade entre amis : choix du lieu, timing, en-cas et bonne ambiance au bord de l\'eau avec Pool Party.',
        ),
        'pool-party-seine-et-marne' => array(
            'kw'   => '',
            'desc' => 'Pool Party arrive en Seine-et-Marne avec 40 nouvelles piscines, spas et jacuzzis à louer entre particuliers. Trouvez un espace de baignade près de chez vous.',
        ),
        'team-building-au-bord-de-l-eau' => array(
            'kw'   => '',
            'desc' => 'Team building original au bord de l\'eau : idées d\'activités, conseils d\'organisation et espaces à privatiser pour souder vos équipes avec Pool Party.',
        ),
        'anniversaire-au-bord-de-la-piscine' => array(
            'kw'   => '',
            'desc' => 'Organisez un anniversaire réussi au bord de la piscine : location d\'espace, déco, animations et sécurité. Le guide complet Pool Party pour une fête inoubliable.',
        ),
        'hotes-photographier-votre-piscine' => array(
            'kw'   => '',
            'desc' => 'Hôtes Pool Party : nos conseils photo pour mettre en valeur votre piscine ou spa, attirer plus de locataires et remplir votre calendrier. Lumière, angles et cadrage.',
        ),
        'aquagym-longueurs-farniente' => array(
            'kw'   => '',
            'desc' => 'Aquagym, longueurs ou farniente au soleil : à chacun sa façon de profiter de l\'eau. Trouvez la piscine idéale pour votre baignade entre particuliers sur Pool Party.',
        ),
    );
}

/**
 * Mot-clé et méta-description des catégories de biens. Yoast range
 * les métas de taxonomie dans une seule option, pas dans des champs
 * personnalisés, d'où le traitement séparé plus bas.
 */
function poolparty_g4_yoast_categories() {
    return array(
        'piscine' => array(
            'kw'   => 'louer une piscine',
            'desc' => 'Louez une piscine entre particuliers : bassins enterrés, chauffés ou avec vue, à l’heure ou à la demi-journée, partout en Île-de-France.',
        ),
        'jacuzzi' => array(
            'kw'   => 'louer un jacuzzi',
            'desc' => 'Réservez un jacuzzi privatif pour quelques heures : bulles chaudes, rooftop ou jardin, le bien-être entre particuliers avec Pool Party.',
        ),
        'spa' => array(
            'kw'   => 'louer un spa',
            'desc' => 'Offrez-vous un spa privatif à la location : détente complète, équipements soignés et réservation simple sur Pool Party.',
        ),
        'sauna' => array(
            'kw'   => 'louer un sauna',
            'desc' => 'Louez un sauna authentique près de chez vous : chaleur sèche, séance privative et réservation à l’heure sur Pool Party.',
        ),
        'hammam' => array(
            'kw'   => 'louer un hammam',
            'desc' => 'Réservez un hammam privatif : vapeur douce et moment de détente entre particuliers, aux horaires qui vous arrangent.',
        ),
        'piscine-hors-sol' => array(
            'kw'   => 'piscine hors-sol',
            'desc' => 'Louez une piscine hors-sol entre particuliers : la baignade simple et conviviale, à petit prix près de chez vous.',
        ),
    );
}

/**
 * Pages sans intérêt pour un moteur de recherche : espace membre,
 * tunnel de réservation, console d'administration, et la page
 * d'exemple créée par WordPress à l'installation.
 */
function poolparty_g4_yoast_pages_noindex() {
    return array(
        'mon-compte',
        'mes-reservations',
        'mes-annonces',
        'messages',
        'favoris',
        'demandes',
        'administration',
        'reservation',
        'inscription',
        'sample-page',
    );
}

/**
 * Retrouve un contenu publié par son slug, tous types confondus.
 */
function poolparty_g4_post_par_slug($slug) {
    $trouves = get_posts(array(
        'name'             => $slug,
        'post_type'        => array('bien', 'page', 'post'),
        'post_status'      => 'publish',
        'posts_per_page'   => 1,
        'suppress_filters' => false,
    ));

    return $trouves ? $trouves[0] : null;
}

/**
 * Écrit une méta Yoast si elle est encore vide, pour ne jamais
 * écraser une saisie faite dans l'administration.
 */
function poolparty_g4_yoast_meta_si_vide($post_id, $cle, $valeur) {
    if ($valeur === '') {
        return false;
    }
    if (get_post_meta($post_id, $cle, true) !== '') {
        return false;
    }

    return (bool) update_post_meta($post_id, $cle, $valeur);
}

/**
 * Métas des catégories de biens. Yoast les stocke toutes ensemble
 * dans l'option wpseo_taxonomy_meta, indexées par identifiant de
 * terme ; une valeur déjà saisie est conservée.
 */
function poolparty_g4_importer_yoast_categories() {
    $stock = get_option('wpseo_taxonomy_meta');
    if (!is_array($stock)) {
        $stock = array();
    }
    if (!isset($stock['categorie_bien']) || !is_array($stock['categorie_bien'])) {
        $stock['categorie_bien'] = array();
    }

    $modifie = false;
    foreach (poolparty_g4_yoast_categories() as $slug => $meta) {
        $terme = get_term_by('slug', $slug, 'categorie_bien');
        if (!$terme || is_wp_error($terme)) {
            continue;
        }
        $actuel = isset($stock['categorie_bien'][$terme->term_id]) && is_array($stock['categorie_bien'][$terme->term_id])
            ? $stock['categorie_bien'][$terme->term_id]
            : array();

        if (empty($actuel['wpseo_desc'])) {
            $actuel['wpseo_desc'] = $meta['desc'];
            $modifie = true;
        }
        if (empty($actuel['wpseo_focuskw'])) {
            $actuel['wpseo_focuskw'] = $meta['kw'];
            $modifie = true;
        }
        $stock['categorie_bien'][$terme->term_id] = $actuel;
    }

    if ($modifie) {
        update_option('wpseo_taxonomy_meta', $stock);
    }
}

/**
 * Applique les métas et les exclusions d'indexation.
 */
function poolparty_g4_importer_yoast() {
    if (get_option('pp_yoast_seed_version') === PP_YOAST_SEED_VERSION) {
        return;
    }

    foreach (poolparty_g4_yoast_metas() as $slug => $meta) {
        $post = poolparty_g4_post_par_slug($slug);
        if (!$post) {
            continue;
        }
        poolparty_g4_yoast_meta_si_vide($post->ID, '_yoast_wpseo_metadesc', $meta['desc']);
        poolparty_g4_yoast_meta_si_vide($post->ID, '_yoast_wpseo_focuskw', $meta['kw']);
    }

    foreach (poolparty_g4_yoast_pages_noindex() as $slug) {
        $post = poolparty_g4_post_par_slug($slug);
        if (!$post) {
            continue;
        }
        poolparty_g4_yoast_meta_si_vide($post->ID, '_yoast_wpseo_meta-robots-noindex', '1');
    }

    poolparty_g4_importer_yoast_categories();

    update_option('pp_yoast_seed_version', PP_YOAST_SEED_VERSION);
}
add_action('init', 'poolparty_g4_importer_yoast', 45);
