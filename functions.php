<?php
/**
 * PoolParty_G4 : fonctions du thème.
 * Charge les feuilles de styles du site statique (css/), le script
 * principal (js/main.js) et déclare les réglages de base du thème.
 * Le CSS et le JS sont réutilisés tels quels, rien n'est recodé ici.
 */

// Sécurité : pas d'accès direct au fichier en dehors de WordPress
if (!defined('ABSPATH')) {
    exit;
}

// Rôles utilisateurs métier (Locataire, Hôte) cumulables avec le compte,
// en plus de l'Administrateur natif. Créés une fois en base (verrou).
require get_template_directory() . '/inc/roles.php';

// Custom Post Type « Bien » (annonces) : déclaration, taxonomie,
// champs personnalisés, méta-box d'édition et fonctions d'affichage.
require get_template_directory() . '/inc/cpt-bien.php';

// Import automatique des 16 biens (data/biens.js) dans la base :
// catégories, posts « Bien », images et descriptions. Ne s'exécute
// qu'une fois (verrou par option).
require get_template_directory() . '/inc/seed-biens.php';

// Création automatique des Pages WordPress (à propos, faq, contact,
// pages légales...) avec leurs slugs, une seule fois (verrou par option).
require get_template_directory() . '/inc/seed-pages.php';

// Import automatique des Articles du journal (Posts) et de leurs
// catégories, une seule fois (verrou par option).
require get_template_directory() . '/inc/seed-articles.php';

// Messagerie interne (page Messages) : conversations de démonstration
// locataire ↔ hôte injectées au premier affichage (le circuit lui-même
// vit dans main.js / localStorage, comme les favoris et réservations).
require get_template_directory() . '/inc/messagerie.php';

// Réservations de démonstration (page Mes réservations) : quelques
// demandes pré-remplies injectées au premier affichage connecté (le
// circuit lui-même vit dans main.js / localStorage, comme la messagerie).
require get_template_directory() . '/inc/reservations-demo.php';

// Personnalisateur : textes éditables de l'accueil (Apparence >
// Personnaliser), pour que le contenu de front-page.php soit modifiable
// depuis le back-office sans toucher au code.
require get_template_directory() . '/inc/customizer.php';

/**
 * Raccourci vers un fichier du thème (images, icônes, polices...).
 * Exemple : pp_asset('assets/images/logo/logo-full.png')
 */
function pp_asset($chemin) {
    return get_template_directory_uri() . '/' . ltrim($chemin, '/');
}

/**
 * Version d'un fichier du thème basée sur sa date de modification.
 * Sert de numéro de version pour wp_enqueue_* : à chaque édition d'un
 * CSS ou du JS, l'URL change (?ver=timestamp), ce qui force le
 * navigateur à recharger le fichier au lieu de servir l'ancien cache.
 */
function pp_version($chemin_relatif) {
    $fichier = get_template_directory() . '/' . ltrim($chemin_relatif, '/');
    return file_exists($fichier) ? filemtime($fichier) : wp_get_theme()->get('Version');
}

/**
 * Classe de badge (composant .tag) d'un article selon le slug de sa
 * catégorie. Reprend les couleurs de la charte utilisées sur la
 * grille des actualités.
 */
function pp_article_tag_class($slug_categorie) {
    $map = array(
        'nouveautes' => 'tag--nouveau',
        'conseils'   => 'tag--succes',
        'idees'      => 'tag--promo',
        'communaute' => 'tag--top-vente',
    );
    return isset($map[$slug_categorie]) ? $map[$slug_categorie] : 'tag--nouveau';
}

/**
 * Date en toutes lettres françaises, indépendante de la locale
 * WordPress (évite un affichage anglais des mois). Attend un
 * timestamp Unix (ex. get_the_time('U')).
 */
function pp_date_fr($timestamp) {
    $mois = array(
        1 => 'janvier', 2 => 'février', 3 => 'mars', 4 => 'avril',
        5 => 'mai', 6 => 'juin', 7 => 'juillet', 8 => 'août',
        9 => 'septembre', 10 => 'octobre', 11 => 'novembre', 12 => 'décembre',
    );
    return intval(date('j', $timestamp)) . ' ' . $mois[intval(date('n', $timestamp))] . ' ' . date('Y', $timestamp);
}

/**
 * Réglages de base du thème.
 */
function poolparty_g4_setup() {
    // WordPress gère la balise title (exigence SEO du cahier des charges)
    add_theme_support('title-tag');

    // Images à la une, utilisées par le futur CPT Bien
    add_theme_support('post-thumbnails');

    // Balises HTML5 propres sur les éléments générés par WordPress
    add_theme_support('html5', array('search-form', 'gallery', 'caption', 'style', 'script'));

    // Emplacements de menus gérables depuis Apparence > Menus.
    // Tant qu'aucun menu n'est assigné, les gabarits affichent
    // les liens du site statique en repli.
    register_nav_menus(array(
        'principal'      => 'Menu principal (panneau déroulant du header)',
        'footer_service' => 'Footer : colonne Service client',
        'footer_savoir'  => 'Footer : colonne En savoir plus',
        'footer_legal'   => 'Footer : colonne Mentions légales',
    ));
}
add_action('after_setup_theme', 'poolparty_g4_setup');

/**
 * Feuilles de styles : socle commun dans l'ordre du site statique
 * (fonts, reset, variables, global) puis la feuille propre à la page.
 * Les URL relatives internes aux CSS (../assets/...) restent valides
 * car l'arborescence css/, js/, assets/ est copiée telle quelle.
 */
function poolparty_g4_styles() {
    $uri = get_template_directory_uri();

    wp_enqueue_style('pp-fonts', $uri . '/css/fonts.css', array(), pp_version('css/fonts.css'));
    wp_enqueue_style('pp-reset', $uri . '/css/reset.css', array('pp-fonts'), pp_version('css/reset.css'));
    wp_enqueue_style('pp-variables', $uri . '/css/variables.css', array('pp-reset'), pp_version('css/variables.css'));
    wp_enqueue_style('pp-global', $uri . '/css/global.css', array('pp-variables'), pp_version('css/global.css'));

    // Feuille spécifique à la page affichée ; la liste s'étoffera
    // au fil de la conversion des gabarits (étapes 2 à 5)
    if (is_front_page()) {
        wp_enqueue_style('pp-accueil', $uri . '/css/accueil.css', array('pp-global'), pp_version('css/accueil.css'));
    }

    // Catalogue des biens et pages catégorie : styles catégorie + carte OSM
    if (is_post_type_archive('bien') || is_tax('categorie_bien')) {
        wp_enqueue_style('pp-leaflet', $uri . '/css/leaflet.css', array('pp-global'), pp_version('css/leaflet.css'));
        wp_enqueue_style('pp-categorie', $uri . '/css/categorie.css', array('pp-global'), pp_version('css/categorie.css'));
    }

    // Fiche d'un bien : styles produit
    if (is_singular('bien')) {
        wp_enqueue_style('pp-produit', $uri . '/css/produit.css', array('pp-global'), pp_version('css/produit.css'));
    }

    // Pages de contenu : chaque page charge sa ou ses feuilles dédiées.
    // Clé = slug de la page, valeur = liste de feuilles CSS (sans .css).
    $pages_css = array(
        'a-propos'           => array('a-propos'),
        'faq'                => array('faq'),
        'contact'            => array('contact'),
        'presse'             => array('presse'),
        'actualites'         => array('actualites'),
        'partenaires'        => array('partenaires'),
        'devenir-partenaire' => array('partenaire'),
        'moyen-de-paiement'  => array('paiement', 'moyen-paiement'),
        'paiement-securise'  => array('paiement'),
        'assurance'          => array('assurance'),
        'accessibilite'      => array('accessibilite'),
        'mentions-legales'   => array('legal'),
        'cgu'                => array('legal'),
        'cgv'                => array('legal'),
        'inscription'        => array('inscription'),
        'favoris'            => array('favoris'),
        'mes-reservations'   => array('mes-reservations'),
        'messages'           => array('messages'),
        'proposer'           => array('proposer'),
        'reservation'        => array('reservation'),
    );
    foreach ($pages_css as $slug => $feuilles) {
        if (is_page($slug)) {
            foreach ($feuilles as $f) {
                wp_enqueue_style('pp-' . $f, $uri . '/css/' . $f . '.css', array('pp-global'), pp_version('css/' . $f . '.css'));
            }
        }
    }

    // Page d'un article du journal (single.php) : styles des actualités
    if (is_singular('post')) {
        wp_enqueue_style('pp-actualites', $uri . '/css/actualites.css', array('pp-global'), pp_version('css/actualites.css'));
    }

    // Page 404 : styles légaux (comme le site statique)
    if (is_404()) {
        wp_enqueue_style('pp-legal', $uri . '/css/legal.css', array('pp-global'), pp_version('css/legal.css'));
    }
}
add_action('wp_enqueue_scripts', 'poolparty_g4_styles');

/**
 * Scripts : main.js chargé en fin de page, comme sur le site statique.
 */
function poolparty_g4_scripts() {
    $uri = get_template_directory_uri();
    $deps = array();

    // Catalogue et pages catégorie : la carte OpenStreetMap a besoin de
    // Leaflet, chargé avant main.js (main.js l'utilise pour les marqueurs).
    if (is_post_type_archive('bien') || is_tax('categorie_bien')) {
        wp_enqueue_script('pp-leaflet', $uri . '/js/leaflet.js', array(), pp_version('js/leaflet.js'), true);
        $deps[] = 'pp-leaflet';
    }

    wp_enqueue_script('pp-main', $uri . '/js/main.js', $deps, pp_version('js/main.js'), true);

    // URL WordPress passées à main.js (js réutilisé du site statique).
    // Le carrousel de catégories de l'accueil et le tunnel de réservation
    // ciblaient des fichiers .html : on leur fournit les vrais permaliens
    // (catalogue des biens, page réservation) pour éviter les 404.
    wp_localize_script('pp-main', 'ppData', array(
        'catalogueUrl'    => get_post_type_archive_link('bien'),
        'reservationUrl'  => home_url('/reservation/'),
        'favorisUrl'      => home_url('/favoris/'),
        'reservationsUrl' => home_url('/mes-reservations/'),
        'messagesUrl'     => home_url('/messages/'),
        // Conversations de démonstration de la messagerie interne,
        // amorcées dans le localStorage au premier affichage connecté.
        // On ne monte le jeu de données (qui interroge la base) que sur
        // la page Messages, seule à le consommer.
        'messagerie'      => is_page('messages') ? poolparty_g4_messagerie_seed() : array(),
        // Réservations de démonstration, amorcées dans le localStorage au
        // premier affichage connecté. Comme la messagerie, on ne monte le
        // jeu de données (qui interroge la base) que sur la page qui le
        // consomme, Mes réservations.
        'reservationsDemo' => is_page('mes-reservations') ? poolparty_g4_reservations_demo() : array(),
    ));
}
add_action('wp_enqueue_scripts', 'poolparty_g4_scripts');

/**
 * Retire les classes menu-item, menu-item-type-*, menu-item-N... que
 * WordPress pose sur chaque <li> de wp_nav_menu. Elles entrent en
 * collision avec le composant .menu-item du design system (pastille
 * blanche à bordure du Figma) : sans ce filtre, les liens du footer
 * s'affichent en blanc sur fond blanc, donc invisibles. On conserve
 * les classes utiles (current-menu-item, classes saisies dans l'admin).
 */
function poolparty_g4_menu_item_classes($classes) {
    return array_filter($classes, function ($classe) {
        return strpos($classe, 'menu-item') !== 0;
    });
}
add_filter('nav_menu_css_class', 'poolparty_g4_menu_item_classes', 10, 1);

/**
 * Walker de menu sur mesure : rend chaque élément comme un simple
 * lien <a>, sans ul ni li, pour coller au markup du menu déroulant
 * statique (.main-menu__section > a). Les classes CSS saisies dans
 * Apparence > Menus (champ Classes CSS) sont reportées sur le lien,
 * ce qui permet par exemple d'appliquer main-menu__strong.
 */
class PoolParty_G4_Walker_Liens extends Walker_Nav_Menu {
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        // On ne garde que les classes saisies dans l'admin,
        // pas les classes techniques menu-item-*/current-* de WordPress
        $classes = array_filter((array) $item->classes, function ($classe) {
            return $classe && strpos($classe, 'menu-item') === false && strpos($classe, 'current') === false;
        });
        // État « page active » : WordPress marque l'item courant via
        // $item->current. On le retraduit en classe .is-current + attribut
        // aria-current du design system (le filtre ci-dessus ayant retiré
        // les classes current-menu-item natives). Sans cela, l'item actif du
        // menu principal n'était jamais mis en évidence dans le header.
        $aria = '';
        if (!empty($item->current)) {
            $classes[] = 'is-current';
            $aria = ' aria-current="page"';
        }
        $attribut = $classes ? ' class="' . esc_attr(implode(' ', $classes)) . '"' : '';
        $output .= '<a href="' . esc_url($item->url) . '"' . $attribut . $aria . '>' . esc_html($item->title) . '</a>';
    }
    public function end_el(&$output, $item, $depth = 0, $args = null) {}
    public function start_lvl(&$output, $depth = 0, $args = null) {}
    public function end_lvl(&$output, $depth = 0, $args = null) {}
}

/**
 * Compatibilité avec les liens .html codés en dur dans js/main.js
 * (carrousel de catégories, tunnel de réservation, bandeau cookies).
 * Toute URL en <slug>.html qui aboutit à une 404 est redirigée en 301
 * vers le permalien /<slug>/, en conservant la chaîne de requête.
 * main.js reste ainsi réutilisé tel quel, sans modification.
 */
function poolparty_g4_redirection_html() {
    if (!is_404()) {
        return;
    }
    $chemin = wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($chemin && preg_match('#([a-z0-9-]+)\.html$#', $chemin, $m)) {
        // index.html renvoie vers l'accueil, les autres vers leur slug
        $cible = ($m[1] === 'index') ? home_url('/') : home_url('/' . $m[1] . '/');
        // Chaîne de requête reconstruite proprement (clés/valeurs
        // ré-encodées) avant d'être rattachée : on ne recopie jamais
        // le QUERY_STRING brut dans une redirection 301 mise en cache.
        if (!empty($_SERVER['QUERY_STRING'])) {
            $params = array();
            wp_parse_str($_SERVER['QUERY_STRING'], $params);
            if (!empty($params)) {
                $cible = add_query_arg($params, $cible);
            }
        }
        wp_safe_redirect(esc_url_raw($cible), 301);
        exit;
    }
}
add_action('template_redirect', 'poolparty_g4_redirection_html');

/**
 * Classe body du tunnel « Proposer votre espace » (mise en page plein
 * écran héritée de proposer.html).
 */
function poolparty_g4_body_class($classes) {
    if (is_page('proposer')) {
        $classes[] = 'proposer-body';
    }
    return $classes;
}
add_filter('body_class', 'poolparty_g4_body_class');

/**
 * Pages privées (espace personnel, non indexées comme sur le statique) :
 * on demande aux moteurs de ne pas les indexer.
 */
function poolparty_g4_noindex($robots) {
    if (is_page(array('favoris', 'mes-reservations', 'messages', 'inscription', 'proposer', 'reservation'))) {
        $robots['noindex']  = true;
        $robots['nofollow'] = true;
    }
    return $robots;
}
add_filter('wp_robots', 'poolparty_g4_noindex');

/**
 * Favicon, identique au site statique.
 */
function poolparty_g4_favicon() {
    echo '<link rel="icon" type="image/png" href="' . esc_url(pp_asset('assets/images/logo/favicon.png')) . '">' . "\n";
}
add_action('wp_head', 'poolparty_g4_favicon');

/**
 * Google Analytics 4 (gtag.js). Non chargé sur l'environnement Local
 * (.local) pour que les tests ne polluent pas les statistiques de la prod.
 */
function poolparty_g4_google_analytics() {
    $hote = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    if (substr($hote, -6) === '.local') {
        return;
    }
    $id = 'G-6YNBKZHM0Y';
    ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($id); ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?php echo esc_js($id); ?>');
    </script>
    <?php
}
add_action('wp_head', 'poolparty_g4_google_analytics');
