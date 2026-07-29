<?php
/**
 * Custom Post Type « Bien » : coeur de la démonstration CMS.
 * Déclare le type de contenu Bien (les annonces), la taxonomie
 * Catégorie (piscine, jacuzzi, spa, sauna, hammam), les champs
 * personnalisés mappés sur data/biens.js, la méta-box d'édition dans
 * wp-admin, et des fonctions d'affichage (carte, hôte, libellés).
 */

if (!defined('ABSPATH')) {
    exit;
}

/* =============================================================
   1. DÉCLARATION DU TYPE DE CONTENU ET DE LA TAXONOMIE
   ============================================================= */

/**
 * Type de contenu « Bien ». URL des fiches : /bien/<slug>/,
 * page catalogue : /catalogue/ (archive-bien.php).
 */
function poolparty_g4_cpt_bien() {
    register_post_type('bien', array(
        'labels' => array(
            'name'               => 'Biens',
            'singular_name'      => 'Bien',
            'add_new'            => 'Ajouter un bien',
            'add_new_item'       => 'Ajouter un bien',
            'edit_item'          => 'Modifier le bien',
            'new_item'           => 'Nouveau bien',
            'view_item'          => 'Voir le bien',
            'search_items'       => 'Rechercher un bien',
            'not_found'          => 'Aucun bien trouvé',
            'all_items'          => 'Tous les biens',
            'menu_name'          => 'Biens',
        ),
        'public'       => true,
        'has_archive'  => 'catalogue',
        'menu_icon'    => 'dashicons-location-alt',
        'menu_position' => 5,
        'supports'     => array('title', 'editor', 'thumbnail'),
        'rewrite'      => array('slug' => 'bien'),
        'show_in_rest' => true,
    ));

    register_taxonomy('categorie_bien', 'bien', array(
        'labels' => array(
            'name'          => 'Catégories',
            'singular_name' => 'Catégorie',
            'menu_name'     => 'Catégories',
            'all_items'     => 'Toutes les catégories',
            'edit_item'     => 'Modifier la catégorie',
            'add_new_item'  => 'Ajouter une catégorie',
        ),
        'public'            => true,
        'hierarchical'      => true,
        'show_admin_column' => true,
        'rewrite'           => array('slug' => 'categorie'),
        'show_in_rest'      => true,
    ));
}
add_action('init', 'poolparty_g4_cpt_bien');

/**
 * Force l'éditeur classique (méta-boxes) pour les biens. Plus simple à
 * manipuler : l'image à la une, la catégorie et les champs personnalisés
 * sont tous visibles d'un coup à l'écran, sans tiroir latéral à ouvrir.
 */
function poolparty_g4_editeur_classique_bien($use_block_editor, $post_type) {
    if ($post_type === 'bien') {
        return false;
    }
    return $use_block_editor;
}
add_filter('use_block_editor_for_post_type', 'poolparty_g4_editeur_classique_bien', 10, 2);

/**
 * La base des permaliens de fiche est /bien/ (une fiche vit sur
 * /bien/{slug}/), mais l'archive du catalogue est sur /catalogue/.
 * L'adresse nue /bien/ n'affiche donc rien : on la redirige en 301 vers
 * le catalogue au lieu de laisser une page 404.
 */
function poolparty_g4_redirige_base_bien() {
    if (is_admin()) {
        return;
    }
    $chemin = trim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if ($chemin === 'bien') {
        $catalogue = get_post_type_archive_link('bien');
        if ($catalogue) {
            wp_safe_redirect($catalogue, 301);
            exit;
        }
    }
}
add_action('template_redirect', 'poolparty_g4_redirige_base_bien');

/**
 * Sur le catalogue et les pages catégorie, afficher tous les biens sur
 * une seule page (pas de pagination) pour que la carte OpenStreetMap
 * montre tous les marqueurs d'un coup.
 */
function poolparty_g4_query_biens($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    if ($query->is_post_type_archive('bien') || $query->is_tax('categorie_bien')) {
        $query->set('posts_per_page', -1);

        // Barre de recherche (header/accueil) ET pilules du catalogue : les
        // sélections partent en paramètres d'URL, lus ici pour filtrer et
        // trier la requête principale (les résultats sont rendus côté serveur).
        //   quoi / recherche → mot-clé (titre ou description du bien)
        //   adresse          → ville du bien
        //   invites          → capacité d'accueil suffisante (barre de recherche)
        //   prix_min/prix_max→ fourchette de prix par heure (pilule Prix)
        //   distance         → rayon maximum en km (pilule Distance)
        //   personnes        → taille du groupe, plage « a-b » ou « 10+ » (pilule)
        //   tri              → ordre d'affichage (bloc « Trier par »)
        // La date reste indicative : cette démo n'a pas de planning de
        // disponibilité, on ne filtre donc pas dessus.
        $quoi = isset($_GET['quoi']) ? sanitize_text_field(wp_unslash($_GET['quoi'])) : '';
        if ($quoi === '' && isset($_GET['recherche'])) {
            $quoi = sanitize_text_field(wp_unslash($_GET['recherche']));
        }
        $adresse   = isset($_GET['adresse'])   ? sanitize_text_field(wp_unslash($_GET['adresse'])) : '';
        $invites   = isset($_GET['invites'])   ? intval($_GET['invites'])  : 0;
        $prix_min  = isset($_GET['prix_min'])  ? intval($_GET['prix_min']) : 0;
        $prix_max  = isset($_GET['prix_max'])  ? intval($_GET['prix_max']) : 0;
        $distance  = isset($_GET['distance'])  ? intval($_GET['distance']) : 0;
        $personnes = isset($_GET['personnes']) ? sanitize_text_field(wp_unslash($_GET['personnes'])) : '';
        $tri       = isset($_GET['tri'])       ? sanitize_text_field(wp_unslash($_GET['tri'])) : '';

        if ($quoi !== '') {
            // Le champ Quoi propose un type d'espace : si la valeur correspond
            // à une catégorie (Piscine, Spa...), on filtre par taxonomie ; la
            // recherche plein texte ne sert qu'au texte libre (elle ratait les
            // biens sans le mot dans le titre, et « Spa » matchait « espace »).
            $terme = get_term_by('slug', sanitize_title($quoi), 'categorie_bien');
            if ($terme) {
                $tax_query   = (array) $query->get('tax_query');
                $tax_query[] = array(
                    'taxonomy' => 'categorie_bien',
                    'field'    => 'term_id',
                    'terms'    => $terme->term_id,
                );
                $query->set('tax_query', $tax_query);
            } else {
                $query->set('s', $quoi);
            }
        }

        $meta_query = array('relation' => 'AND');

        if ($adresse !== '') {
            $meta_query[] = array(
                'key'     => 'pp_ville',
                'value'   => $adresse,
                'compare' => 'LIKE',
            );
        }
        if ($invites > 0) {
            $meta_query[] = array(
                'key'     => 'pp_capacite_max',
                'value'   => $invites,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            );
        }
        if ($prix_min > 0 || $prix_max > 0) {
            $bas  = $prix_min > 0 ? $prix_min : 0;
            $haut = $prix_max > 0 ? $prix_max : 1000000;
            $meta_query[] = array(
                'key'     => 'pp_prix_heure',
                'value'   => array($bas, $haut),
                'compare' => 'BETWEEN',
                'type'    => 'NUMERIC',
            );
        }
        if ($distance > 0) {
            $meta_query[] = array(
                'key'     => 'pp_distance_km',
                'value'   => $distance,
                'compare' => '<=',
                'type'    => 'NUMERIC',
            );
        }
        // Nombre de personne : la place convient si la taille du groupe
        // (g_bas..g_haut) recoupe la capacité du bien (capacite_min..max),
        // soit capacite_max >= g_bas ET capacite_min <= g_haut.
        $g_bas = 0;
        $g_haut = 0;
        if ($personnes === '10+') {
            $g_bas = 10;
            $g_haut = 1000000;
        } elseif (preg_match('/^(\d+)-(\d+)$/', $personnes, $m)) {
            $g_bas  = (int) $m[1];
            $g_haut = (int) $m[2];
        }
        if ($g_bas > 0) {
            $meta_query[] = array(
                'key'     => 'pp_capacite_max',
                'value'   => $g_bas,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            );
            $meta_query[] = array(
                'key'     => 'pp_capacite_min',
                'value'   => $g_haut,
                'compare' => '<=',
                'type'    => 'NUMERIC',
            );
        }

        // Tri : par avis (défaut), prix croissant/décroissant, distance ou
        // nouveautés. Les tris sur une méta passent par une clause nommée
        // pour rester compatibles avec les filtres méta ci-dessus.
        switch ($tri) {
            case 'prix-asc':
                $meta_query['tri_ordre'] = array('key' => 'pp_prix_heure', 'type' => 'NUMERIC', 'compare' => 'EXISTS');
                $query->set('orderby', array('tri_ordre' => 'ASC'));
                break;
            case 'prix-desc':
                $meta_query['tri_ordre'] = array('key' => 'pp_prix_heure', 'type' => 'NUMERIC', 'compare' => 'EXISTS');
                $query->set('orderby', array('tri_ordre' => 'DESC'));
                break;
            case 'distance':
                $meta_query['tri_ordre'] = array('key' => 'pp_distance_km', 'type' => 'NUMERIC', 'compare' => 'EXISTS');
                $query->set('orderby', array('tri_ordre' => 'ASC'));
                break;
            case 'nouveautes':
                $query->set('orderby', 'date');
                $query->set('order', 'DESC');
                break;
            default: // « Avis » (ou aucun tri choisi) : les mieux notés d'abord
                $meta_query['tri_ordre'] = array('key' => 'pp_note', 'type' => 'NUMERIC', 'compare' => 'EXISTS');
                $query->set('orderby', array('tri_ordre' => 'DESC'));
                break;
        }

        // Applique la requête méta seulement si elle porte au moins un
        // critère en plus de la relation.
        if (count($meta_query) > 1) {
            $query->set('meta_query', $meta_query);
        }
    }
}
add_action('pre_get_posts', 'poolparty_g4_query_biens');

/* =============================================================
   2. CHAMPS PERSONNALISÉS (méta) mappés sur data/biens.js
   ============================================================= */

/**
 * Liste des champs personnalisés d'un bien, dans l'ordre d'affichage
 * de la méta-box. type = text | number | textarea | select.
 */
function poolparty_g4_champs_bien() {
    return array(
        'ville'        => array('label' => 'Ville', 'type' => 'text'),
        'pays'         => array('label' => 'Pays', 'type' => 'text'),
        'distance_km'  => array('label' => 'Distance (km)', 'type' => 'number'),
        'prix_heure'   => array('label' => 'Prix par heure (euros)', 'type' => 'number'),
        'prix_ancien'  => array('label' => 'Ancien prix barré (euros, vide si aucun)', 'type' => 'number'),
        'capacite_min' => array('label' => 'Capacité min (personnes)', 'type' => 'number'),
        'capacite_max' => array('label' => 'Capacité max (personnes)', 'type' => 'number'),
        'note'         => array('label' => 'Note moyenne (sur 5)', 'type' => 'text'),
        'nb_avis'      => array('label' => 'Nombre d\'avis', 'type' => 'number'),
        'lat'          => array('label' => 'Latitude', 'type' => 'text'),
        'lon'          => array('label' => 'Longitude', 'type' => 'text'),
        'tag'          => array('label' => 'Mise en avant', 'type' => 'select', 'options' => array(
            ''          => 'Aucune',
            'top-vente' => 'Top vente',
            'proche'    => 'Le plus proche',
            'nouveau'   => 'Nouveau',
            'promo'     => 'Promo',
            'succes'    => 'Victime de son succès',
        )),
        'image'        => array('label' => 'Image (chemin dans le thème, ex: assets/images/piscines/annonce-torcy.jpg)', 'type' => 'text'),
        'alt'          => array('label' => 'Texte alternatif de l\'image', 'type' => 'text'),
        'id_hote'      => array('label' => 'Hôte', 'type' => 'select', 'options' => 'hotes'),
        'favori_id'    => array('label' => 'Identifiant favori (optionnel)', 'type' => 'text'),
    );
}

/**
 * Enregistre chaque champ comme méta de post (exposée à l'API REST).
 */
function poolparty_g4_enregistrer_meta_bien() {
    foreach (poolparty_g4_champs_bien() as $cle => $def) {
        register_post_meta('bien', 'pp_' . $cle, array(
            'type'         => $def['type'] === 'number' ? 'number' : 'string',
            'single'       => true,
            'show_in_rest' => true,
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => function () {
                return current_user_can('edit_posts');
            },
        ));
    }

    // Galerie du bien : liste d'identifiants de médias (photos de la
    // médiathèque) séparés par des virgules, gérée par sa propre méta-box.
    register_post_meta('bien', 'pp_galerie', array(
        'type'         => 'string',
        'single'       => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function () {
            return current_user_can('edit_posts');
        },
    ));
}
add_action('init', 'poolparty_g4_enregistrer_meta_bien');

/* =============================================================
   3. MÉTA-BOX D'ÉDITION DANS WP-ADMIN
   ============================================================= */

function poolparty_g4_metabox_bien() {
    add_meta_box(
        'pp_galerie_bien',
        'Galerie du bien (photos)',
        'poolparty_g4_afficher_metabox_galerie',
        'bien',
        'normal',
        'high'
    );
    add_meta_box(
        'pp_details_bien',
        'Détails du bien',
        'poolparty_g4_afficher_metabox_bien',
        'bien',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'poolparty_g4_metabox_bien');

/**
 * Charge le sélecteur de médias de WordPress et le script de la galerie
 * uniquement sur l'écran d'édition d'un bien.
 */
function poolparty_g4_admin_galerie_assets($hook) {
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }
    $ecran = get_current_screen();
    if (!$ecran || $ecran->post_type !== 'bien') {
        return;
    }
    wp_enqueue_media();
    wp_enqueue_script(
        'pp-admin-galerie',
        get_template_directory_uri() . '/js/admin-galerie.js',
        array('jquery', 'jquery-ui-sortable'),
        pp_version('js/admin-galerie.js'),
        true
    );
}
add_action('admin_enqueue_scripts', 'poolparty_g4_admin_galerie_assets');

/**
 * Méta-box « Galerie du bien » : choix de plusieurs photos dans la
 * médiathèque, réordonnables. Enregistre leurs identifiants dans pp_galerie.
 */
function poolparty_g4_afficher_metabox_galerie($post) {
    wp_nonce_field('pp_enregistrer_galerie', 'pp_galerie_nonce');
    $ids = array_filter(array_map('absint', explode(',', (string) get_post_meta($post->ID, 'pp_galerie', true))));
    echo '<style>.pp-galerie-liste{display:flex;flex-wrap:wrap;gap:10px;margin:12px 0;padding:0;list-style:none}.pp-galerie-liste li{position:relative;width:110px;height:82px;cursor:move}.pp-galerie-liste img{width:100%;height:100%;object-fit:cover;border-radius:4px;display:block}.pp-galerie-suppr{position:absolute;top:-8px;right:-8px;width:22px;height:22px;line-height:20px;padding:0;border:0;border-radius:50%;background:#b32d2e;color:#fff;font-size:16px;cursor:pointer}</style>';
    echo '<div class="pp-galerie-admin" data-pp-galerie>';
    echo '<input type="hidden" class="pp-galerie-ids" name="pp_galerie" value="' . esc_attr(implode(',', $ids)) . '">';
    echo '<ul class="pp-galerie-liste">';
    foreach ($ids as $id) {
        $apercu = wp_get_attachment_image_url($id, 'thumbnail');
        if (!$apercu) {
            continue;
        }
        echo '<li data-id="' . esc_attr($id) . '"><img src="' . esc_url($apercu) . '" alt=""><button type="button" class="pp-galerie-suppr" aria-label="Retirer cette photo">&times;</button></li>';
    }
    echo '</ul>';
    echo '<button type="button" class="button button-secondary pp-galerie-ajouter">Ajouter ou modifier les photos</button>';
    echo '<p class="description">Ces photos remplacent la galerie de la fiche du bien. Glissez-les pour changer l\'ordre : la première est la grande photo. Laissez vide pour garder la galerie par défaut du thème.</p>';
    echo '</div>';
}

function poolparty_g4_afficher_metabox_bien($post) {
    wp_nonce_field('pp_enregistrer_bien', 'pp_bien_nonce');
    echo '<style>.pp-champs{display:grid;grid-template-columns:1fr 1fr;gap:12px}.pp-champ label{display:block;font-weight:600;margin-bottom:4px}.pp-champ input,.pp-champ select,.pp-champ textarea{width:100%}</style>';
    echo '<div class="pp-champs">';
    foreach (poolparty_g4_champs_bien() as $cle => $def) {
        $valeur = get_post_meta($post->ID, 'pp_' . $cle, true);
        echo '<p class="pp-champ">';
        echo '<label for="pp_' . esc_attr($cle) . '">' . esc_html($def['label']) . '</label>';
        if ($def['type'] === 'select') {
            $options = $def['options'] === 'hotes' ? poolparty_g4_options_hotes() : $def['options'];
            echo '<select id="pp_' . esc_attr($cle) . '" name="pp_' . esc_attr($cle) . '">';
            foreach ($options as $val => $libelle) {
                echo '<option value="' . esc_attr($val) . '"' . selected($valeur, $val, false) . '>' . esc_html($libelle) . '</option>';
            }
            echo '</select>';
        } else {
            $type = $def['type'] === 'number' ? 'number' : 'text';
            $step = $def['type'] === 'number' ? ' step="any"' : '';
            echo '<input type="' . esc_attr($type) . '"' . $step . ' id="pp_' . esc_attr($cle) . '" name="pp_' . esc_attr($cle) . '" value="' . esc_attr($valeur) . '">';
        }
        echo '</p>';
    }
    echo '</div>';
}

function poolparty_g4_enregistrer_bien($post_id) {
    if (!isset($_POST['pp_bien_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pp_bien_nonce'])), 'pp_enregistrer_bien')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    foreach (poolparty_g4_champs_bien() as $cle => $def) {
        if (isset($_POST['pp_' . $cle])) {
            update_post_meta($post_id, 'pp_' . $cle, sanitize_text_field(wp_unslash($_POST['pp_' . $cle])));
        }
    }
}
add_action('save_post_bien', 'poolparty_g4_enregistrer_bien');

/**
 * Enregistre les identifiants de photos de la galerie (méta-box dédiée).
 */
function poolparty_g4_enregistrer_galerie($post_id) {
    if (!isset($_POST['pp_galerie_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pp_galerie_nonce'])), 'pp_enregistrer_galerie')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['pp_galerie'])) {
        $ids = array_filter(array_map('absint', explode(',', sanitize_text_field(wp_unslash($_POST['pp_galerie'])))));
        update_post_meta($post_id, 'pp_galerie', implode(',', $ids));
    }
}
add_action('save_post_bien', 'poolparty_g4_enregistrer_galerie');

/**
 * Filet de sécurité du tri : le tri par défaut (et prix/distance) repose
 * sur une clause EXISTS, un bien publié sans ces métas disparaîtrait du
 * catalogue en silence. On complète donc à zéro toute méta de tri absente,
 * quel que soit le chemin d'enregistrement (méta-box, REST, wp-cli).
 */
function poolparty_g4_completer_metas_tri($post_id) {
    foreach (array('pp_note', 'pp_prix_heure', 'pp_distance_km') as $meta) {
        if (get_post_meta($post_id, $meta, true) === '') {
            update_post_meta($post_id, $meta, '0');
        }
    }
}
add_action('save_post_bien', 'poolparty_g4_completer_metas_tri', 20);

function poolparty_g4_completer_metas_tri_rest($post) {
    poolparty_g4_completer_metas_tri($post->ID);
}
add_action('rest_after_insert_bien', 'poolparty_g4_completer_metas_tri_rest');

/* =============================================================
   3bis. COLONNE IMAGE DANS LA LISTE DES BIENS (wp-admin)
   Affiche la photo de chaque bien dans le tableau d'administration
   sans avoir à définir une image à la une : reprend l'image à la une
   si elle existe, sinon l'image du thème (même logique que le site).
   Les 16 biens ont donc leur vignette d'office.
   ============================================================= */

function poolparty_g4_colonnes_bien($colonnes) {
    $nouvelles = array();
    foreach ($colonnes as $cle => $libelle) {
        if ($cle === 'title') {
            $nouvelles['pp_image'] = 'Image';
        }
        $nouvelles[$cle] = $libelle;
    }
    return $nouvelles;
}
add_filter('manage_bien_posts_columns', 'poolparty_g4_colonnes_bien');

function poolparty_g4_colonne_image_bien($colonne, $post_id) {
    if ($colonne !== 'pp_image') {
        return;
    }
    $html = poolparty_g4_image_bien($post_id);
    if ($html) {
        echo '<span style="display:inline-block;width:80px;height:56px;border-radius:4px;overflow:hidden;background:#eee">'
           . str_replace('<img ', '<img style="width:100%;height:100%;object-fit:cover" ', $html)
           . '</span>';
    } else {
        echo '—';
    }
}
add_action('manage_bien_posts_custom_column', 'poolparty_g4_colonne_image_bien', 10, 2);

/* =============================================================
   4. HÔTES (table de référence, mappée sur data/biens.js)
   Comme une clé étrangère : chaque bien stocke un id_hote, résolu
   ici vers le prénom, la photo et le statut superhôte.
   ============================================================= */

function poolparty_g4_hotes() {
    return array(
        'h-julien' => array('prenom' => 'Julien', 'membre_depuis' => 2021, 'superhote' => true,  'photo' => 'assets/images/temoignages/temoin-julien.jpg', 'bio' => "Passionné de baignades entre amis, Julien loue sa piscine chauffée depuis trois ans."),
        'h-paula'  => array('prenom' => 'Paula',  'membre_depuis' => 2022, 'superhote' => true,  'photo' => 'assets/images/temoignages/hote-paula.jpg',    'bio' => "Paula ouvre son jardin et son spa aux familles du quartier le week-end."),
        'h-yoann'  => array('prenom' => 'Yoann',  'membre_depuis' => 2023, 'superhote' => false, 'photo' => 'assets/images/temoignages/hote-yoann.jpg',    'bio' => "Yoann propose son jacuzzi pour les soirées détente en petit comité."),
        'h-tony'   => array('prenom' => 'Tony',   'membre_depuis' => 2020, 'superhote' => true,  'photo' => 'assets/images/temoignages/hote-tony.jpg',     'bio' => "Tony gère un sauna et un hammam soigneusement entretenus toute l'année."),
        'h-sarah'  => array('prenom' => 'Sarah',  'membre_depuis' => 2024, 'superhote' => false, 'photo' => 'assets/images/temoignages/hote-fraise.jpg',   'bio' => "Sarah accueille avec le sourire dans sa piscine intérieure au coeur de la ville."),
    );
}

function poolparty_g4_options_hotes() {
    $options = array('' => 'Aucun');
    foreach (poolparty_g4_hotes() as $id => $hote) {
        $options[$id] = $hote['prenom'];
    }
    return $options;
}

function poolparty_g4_get_hote($id) {
    $hotes = poolparty_g4_hotes();
    return isset($hotes[$id]) ? $hotes[$id] : null;
}

/* =============================================================
   5. FONCTIONS D'AFFICHAGE
   ============================================================= */

/** Récupère une méta de bien (sans le préfixe pp_). */
function poolparty_g4_meta($post_id, $cle) {
    return get_post_meta($post_id, 'pp_' . $cle, true);
}

/** Libellé lisible d'un tag de mise en avant. */
function poolparty_g4_libelle_tag($tag) {
    $labels = array(
        'top-vente' => 'Top vente',
        'proche'    => 'Le plus proche',
        'nouveau'   => 'Nouveau',
        'promo'     => 'Promo',
        'succes'    => 'Victime de son succès',
    );
    return isset($labels[$tag]) ? $labels[$tag] : '';
}

/** Formate la capacité : « 7 personnes » ou « 4-5 personnes ». */
function poolparty_g4_capacite($min, $max) {
    $min = (int) $min;
    $max = (int) $max;
    if (!$max || $max === $min) {
        return $min . ' personnes';
    }
    return $min . '-' . $max . ' personnes';
}

/**
 * Répartition des notes d'un bien par nombre d'étoiles, cohérente avec
 * sa note moyenne et son nombre d'avis (tous deux venant du CMS). Pour
 * une note comprise entre 4 et 5, les avis se partagent entre 5 et 4
 * étoiles de sorte que la moyenne retombe au plus près de la note ; même
 * principe pour les notes plus basses. Évite d'inventer de faux avis :
 * la somme des compteurs égale toujours le nombre d'avis réel.
 * Retourne un tableau associatif 5 => n5, 4 => n4, ... 1 => n1.
 */
function poolparty_g4_repartition_avis($note, $nb_avis) {
    $n    = max(0, (int) $nb_avis);
    $note = (float) str_replace(',', '.', (string) $note);
    $c    = array(5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0);
    if ($n <= 0) {
        return $c;
    }
    if ($note >= 4) {
        $c[5] = min($n, max(0, (int) round($n * ($note - 4))));
        $c[4] = $n - $c[5];
    } elseif ($note >= 3) {
        $c[4] = min($n, max(0, (int) round($n * ($note - 3))));
        $c[3] = $n - $c[4];
    } elseif ($note >= 2) {
        $c[3] = min($n, max(0, (int) round($n * ($note - 2))));
        $c[2] = $n - $c[3];
    } else {
        $c[2] = min($n, max(0, (int) round($n * ($note - 1))));
        $c[1] = $n - $c[2];
    }
    return $c;
}

/** Nom de la catégorie d'un bien (premier terme de la taxonomie). */
function poolparty_g4_categorie_nom($post_id) {
    $termes = get_the_terms($post_id, 'categorie_bien');
    if (is_array($termes) && !empty($termes)) {
        return $termes[0]->name;
    }
    return 'Bien';
}

/**
 * Identifiants d'autres biens à proposer sur une fiche (« espaces
 * similaires à proximité »). Priorité aux biens de la même catégorie,
 * complétés au besoin par d'autres biens ; le bien courant est toujours
 * exclu. Triés par proximité (distance_km croissante). Remplace les
 * cartes en dur qui pointaient toutes vers le catalogue.
 */
function poolparty_g4_biens_similaires($post_id, $limite = 5) {
    $post_id = (int) $post_id;
    $termes  = get_the_terms($post_id, 'categorie_bien');
    $ids     = array();

    $base = array(
        'post_type'      => 'bien',
        'post_status'    => 'publish',
        'posts_per_page' => $limite,
        'post__not_in'   => array($post_id),
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'pp_distance_km',
        'order'          => 'ASC',
        'fields'         => 'ids',
    );

    // 1) Biens de la même catégorie, les plus proches d'abord
    if (is_array($termes) && !empty($termes)) {
        $meme = $base;
        $meme['tax_query'] = array(array(
            'taxonomy' => 'categorie_bien',
            'field'    => 'term_id',
            'terms'    => wp_list_pluck($termes, 'term_id'),
        ));
        $ids = get_posts($meme);
    }

    // 2) Complément avec d'autres biens si la catégorie n'en fournit pas assez
    if (count($ids) < $limite) {
        $reste = $base;
        $reste['posts_per_page'] = $limite - count($ids);
        $reste['post__not_in']   = array_merge(array($post_id), $ids);
        $ids = array_merge($ids, get_posts($reste));
    }

    return $ids;
}

/**
 * Affiche une carte annonce (composant card-product), identique au
 * markup du site statique, à partir d'un bien de la boucle. La carte
 * porte les attributs data-lat/data-lon/data-titre lus par la carte
 * OpenStreetMap, et data-favori-id pour la fonction Favoris.
 */
function poolparty_g4_carte_bien($post_id) {
    $note        = poolparty_g4_meta($post_id, 'note');
    $nb_avis     = poolparty_g4_meta($post_id, 'nb_avis');
    $ville       = poolparty_g4_meta($post_id, 'ville');
    $pays        = poolparty_g4_meta($post_id, 'pays');
    $distance    = poolparty_g4_meta($post_id, 'distance_km');
    $prix        = poolparty_g4_meta($post_id, 'prix_heure');
    $cap_min     = poolparty_g4_meta($post_id, 'capacite_min');
    $cap_max     = poolparty_g4_meta($post_id, 'capacite_max');
    $lat         = poolparty_g4_meta($post_id, 'lat');
    $lon         = poolparty_g4_meta($post_id, 'lon');
    $tag         = poolparty_g4_meta($post_id, 'tag');
    $favori_id   = poolparty_g4_meta($post_id, 'favori_id');
    $categorie   = poolparty_g4_categorie_nom($post_id);
    $note_fr     = str_replace('.', ',', $note);
    $data_titre  = $categorie . ' à ' . $ville . ', ' . $prix . ' € par heure';

    $attrs = ' data-lat="' . esc_attr($lat) . '" data-lon="' . esc_attr($lon) . '" data-titre="' . esc_attr($data_titre) . '"';
    if ($favori_id) {
        $attrs .= ' data-favori-id="' . esc_attr($favori_id) . '"';
    }
    ?>
    <article class="card-product"<?php echo $attrs; ?>>
        <div class="card-product__media">
            <?php echo poolparty_g4_image_bien($post_id, 'card-product__image'); ?>
            <button type="button" class="card-product__fav" aria-label="Ajouter aux favoris" aria-pressed="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.51 4.04 3 5.5l7 7Z"/></svg>
            </button>
        </div>
        <?php if ($tag) : ?>
            <span class="tag tag--<?php echo esc_attr($tag); ?> card-product__tag"><?php echo esc_html(poolparty_g4_libelle_tag($tag)); ?></span>
        <?php endif; ?>
        <div class="card-product__body">
            <div class="card-product__head">
                <h3 class="card-product__title"><a href="<?php the_permalink($post_id); ?>"><?php echo esc_html($categorie); ?></a></h3>
                <span class="rating rating--small"><?php echo esc_html($note_fr); ?> <span class="rating__count">(<?php echo esc_html($nb_avis); ?> avis)</span></span>
            </div>
            <p class="card-product__meta"><span><?php echo esc_html($distance); ?>km</span><span><?php echo esc_html($ville); ?></span><?php if ($pays) : ?><span><?php echo esc_html($pays); ?></span><?php endif; ?></p>
            <p class="card-product__meta"><span><?php echo esc_html(poolparty_g4_capacite($cap_min, $cap_max)); ?></span></p>
            <p class="card-product__price"><?php echo esc_html($prix); ?> €/h</p>
        </div>
    </article>
    <?php
}

/**
 * Image d'un bien : l'image à la une (média WordPress) si elle existe,
 * sinon l'image référencée par le champ « image » dans les assets du
 * thème. Permet de garder les visuels du site statique sans importer
 * tous les fichiers dans la médiathèque, tout en laissant la
 * possibilité de définir une image à la une depuis wp-admin.
 */
function poolparty_g4_image_bien($post_id, $classe = '') {
    $alt = poolparty_g4_meta($post_id, 'alt');
    if (has_post_thumbnail($post_id)) {
        return get_the_post_thumbnail($post_id, 'large', array('alt' => $alt, 'class' => $classe));
    }
    $image = poolparty_g4_meta($post_id, 'image');
    if (!$image) {
        return '';
    }
    $classe_attr = $classe ? ' class="' . esc_attr($classe) . '"' : '';
    return '<img src="' . esc_url(pp_asset($image)) . '" alt="' . esc_attr($alt) . '" loading="lazy" decoding="async"' . $classe_attr . '>';
}

/**
 * URL de l'image d'un bien (image à la une si définie, sinon le champ
 * « image » dans les assets du thème). Utile pour la photo principale
 * de la galerie sur la fiche, où l'on a besoin de l'URL seule.
 */
function poolparty_g4_image_url($post_id) {
    if (has_post_thumbnail($post_id)) {
        $src = get_the_post_thumbnail_url($post_id, 'large');
        if ($src) {
            return $src;
        }
    }
    $image = poolparty_g4_meta($post_id, 'image');
    return $image ? pp_asset($image) : '';
}

/**
 * Photos de la galerie d'un bien, sous forme de tableau d'éléments
 * array('url', 'w', 'h', 'alt'). Si le champ « Galerie du bien » contient
 * des médias, ce sont eux (dans l'ordre choisi). Sinon on retombe sur le
 * rendu historique du thème (image principale + photos d'illustration),
 * pour ne rien casser sur les biens sans galerie personnalisée.
 */
function poolparty_g4_galerie_bien($post_id) {
    $photos = array();
    $ids = array_filter(array_map('absint', explode(',', (string) get_post_meta($post_id, 'pp_galerie', true))));
    foreach ($ids as $id) {
        $src = wp_get_attachment_image_src($id, 'large');
        if (!$src) {
            continue;
        }
        $photos[] = array(
            'url' => $src[0],
            'w'   => $src[1] ? $src[1] : 800,
            'h'   => $src[2] ? $src[2] : 533,
            'alt' => trim((string) get_post_meta($id, '_wp_attachment_image_alt', true)),
        );
    }
    if (!empty($photos)) {
        return $photos;
    }

    // Repli : rendu d'origine (photo principale + 4 photos d'illustration).
    $base = get_template_directory_uri() . '/assets/images/piscines/';
    $principale = poolparty_g4_image_url($post_id);
    if (!$principale) {
        $principale = $base . 'annonce-pantin.jpg';
    }
    // Dimensions réelles de la photo principale (varie selon le bien)
    $pw = 800;
    $ph = 533;
    $chemin = str_replace(get_template_directory_uri(), get_template_directory(), $principale);
    if ($chemin !== $principale && file_exists($chemin)) {
        $dims = @getimagesize($chemin);
        if ($dims) {
            $pw = $dims[0];
            $ph = $dims[1];
        }
    }
    return array(
        array('url' => $principale,               'w' => $pw,  'h' => $ph, 'alt' => trim((string) poolparty_g4_meta($post_id, 'alt'))),
        array('url' => $base . 'annonce-champs.jpg',  'w' => 800,  'h' => 533, 'alt' => 'La vue dégagée depuis le bord du bassin'),
        array('url' => $base . 'annonce-croissy.jpg', 'w' => 800,  'h' => 533, 'alt' => 'Le coin détente avec transats sur la terrasse en bois'),
        array('url' => $base . 'annonce-lagny.jpg',   'w' => 1024, 'h' => 728, 'alt' => 'Le jardin planté de palmiers et de graminées vu de la maison'),
        array('url' => $base . 'annonce-chelles.jpg', 'w' => 800,  'h' => 597, 'alt' => "L'allée gravillonnée qui mène au portillon d'accès de la piscine"),
    );
}
