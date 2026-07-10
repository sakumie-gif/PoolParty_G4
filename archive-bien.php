<?php
/**
 * Page catalogue : archive du type de contenu « Bien ».
 * Reprend la mise en page de categorie.html (fil d'Ariane, bandeau,
 * barre de filtres, carte OpenStreetMap) ; la grille de résultats est
 * générée par la BOUCLE WordPress à partir des biens du CMS. C'est la
 * démonstration principale : les annonces viennent de la base, plus du
 * HTML écrit à la main.
 */

get_header();

// Sur une page catégorie (taxonomie), on affiche le nom de la catégorie ;
// sur le catalogue complet, un libellé générique. get_queried_object()
// renvoie le type de contenu sur l'archive, d'où la distinction.
if (is_tax('categorie_bien')) {
    $titre_page = single_term_title('', false);
} elseif (!empty($_GET['quoi']) || !empty($_GET['recherche']) || !empty($_GET['adresse']) || !empty($_GET['invites'])) {
    // Recherche lancée depuis la barre du header ou de l'accueil.
    $titre_page = 'Résultats de votre recherche';
} else {
    $titre_page = 'Tous les résultats';
}
?>

    <main id="contenu">
        <!-- Fil d'Ariane -->
        <section class="categorie-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li class="breadcrumb__etape"><a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>">Tous types d'espaces</a></li>
                    <li class="breadcrumb__points" aria-hidden="true">&#8230;</li>
                    <li class="breadcrumb__etape breadcrumb__region" hidden>Ville</li>
                    <li class="breadcrumb__etape is-current" aria-current="page"><?php echo esc_html($titre_page); ?></li>
                </ol>
            </nav>
        </section>

        <!-- Bandeau réassurance -->
        <section class="bandeau-resa" aria-labelledby="bandeau-titre">
            <h1 id="bandeau-titre">Réservez votre parenthèse en quelques clics</h1>
            <ul class="bandeau-resa__liste">
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                    Découvrez les hôtes près de chez vous
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                    Choisissez l'espace qui vous ressemble
                </li>
                <li>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                    Réservez en confiance, assurance incluse
                </li>
            </ul>
        </section>

        <?php
        // État courant des filtres, lu dans l'URL pour réafficher les choix
        // (le filtrage lui-même se fait dans poolparty_g4_query_biens()).
        $f_prix_min  = isset($_GET['prix_min'])  ? max(5, min(80, intval($_GET['prix_min']))) : 15;
        $f_prix_max  = isset($_GET['prix_max'])  ? max(5, min(80, intval($_GET['prix_max']))) : 40;
        $f_distance  = isset($_GET['distance'])  ? intval($_GET['distance']) : 0;
        $f_personnes = isset($_GET['personnes']) ? sanitize_text_field(wp_unslash($_GET['personnes'])) : '';
        $f_tri       = isset($_GET['tri'])       ? sanitize_text_field(wp_unslash($_GET['tri'])) : '';
        $tri_labels  = array(
            ''           => 'Avis',
            'avis'       => 'Avis',
            'prix-asc'   => 'Prix croissant',
            'prix-desc'  => 'Prix décroissant',
            'distance'   => 'Distance',
            'nouveautes' => 'Nouveautés',
        );
        $tri_label_courant = isset($tri_labels[$f_tri]) ? $tri_labels[$f_tri] : 'Avis';
        ?>

        <!-- Barre de filtres en pilules + interrupteur carte -->
        <div class="filtres-barre">
            <div class="filtres-pilules">

                <div class="pilule pilule--md">
                    <button type="button" class="menu-item" aria-expanded="false" aria-controls="pilule-categories">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><rect x="4" y="4" width="7" height="7" rx="1"/><rect x="13" y="4" width="7" height="7" rx="1"/><rect x="4" y="13" width="7" height="7" rx="1"/><rect x="13" y="13" width="7" height="7" rx="1"/></svg>
                        Catégories
                    </button>
                    <ul class="pilule__liste" id="pilule-categories" hidden>
                        <?php
                        // Sur une page catégorie, la pilule active est la catégorie
                        // courante ; sur le catalogue complet, c'est « Tous les espaces ».
                        $terme_courant = is_tax('categorie_bien') ? get_queried_object_id() : 0;
                        ?>
                        <li><a class="dropdown-item<?php echo $terme_courant ? '' : ' is-active'; ?>" href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>">Tous les espaces</a></li>
                        <?php
                        $termes = get_terms(array('taxonomy' => 'categorie_bien', 'hide_empty' => true));
                        if (!is_wp_error($termes)) {
                            foreach ($termes as $terme) {
                                $actif = ((int) $terme->term_id === (int) $terme_courant) ? ' is-active' : '';
                                echo '<li><a class="dropdown-item' . $actif . '" href="' . esc_url(get_term_link($terme)) . '">' . esc_html($terme->name) . 's</a></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>

                <div class="pilule pilule--md">
                    <button type="button" class="menu-item" aria-expanded="false" aria-controls="pilule-prix">Prix</button>
                    <div class="pilule__liste pilule__liste--prix filtres__prix" id="pilule-prix" hidden>
                        <p class="filtres__titre">Prix par heure</p>
                        <p class="range-double__value" aria-live="polite"><?php echo esc_html($f_prix_min); ?> € - <?php echo esc_html($f_prix_max); ?> €</p>
                        <div class="range-double">
                            <span class="range-double__fill"></span>
                            <input type="range" class="range-double__input" name="prix-min" data-borne="debut" min="5" max="80" step="5" value="<?php echo esc_attr($f_prix_min); ?>" aria-label="Prix minimum par heure">
                            <input type="range" class="range-double__input" name="prix-max" data-borne="fin" min="5" max="80" step="5" value="<?php echo esc_attr($f_prix_max); ?>" aria-label="Prix maximum par heure">
                        </div>
                        <button type="button" class="btn btn-primary btn-small filtres__prix-appliquer">Appliquer</button>
                    </div>
                </div>

                <div class="pilule pilule--lg">
                    <button type="button" class="menu-item" aria-expanded="false" aria-controls="pilule-distance">Distance</button>
                    <ul class="pilule__liste" id="pilule-distance" hidden>
                        <li><a class="dropdown-item<?php echo $f_distance === 5 ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('distance', 5)); ?>">Moins de 5 km</a></li>
                        <li><a class="dropdown-item<?php echo $f_distance === 10 ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('distance', 10)); ?>">Moins de 10 km</a></li>
                        <li><a class="dropdown-item<?php echo $f_distance === 20 ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('distance', 20)); ?>">Moins de 20 km</a></li>
                        <li><a class="dropdown-item<?php echo $f_distance === 0 ? ' is-active' : ''; ?>" href="<?php echo esc_url(remove_query_arg('distance')); ?>">Toute l'Île-de-France</a></li>
                    </ul>
                </div>

                <div class="pilule pilule--lg">
                    <button type="button" class="menu-item" aria-expanded="false" aria-controls="pilule-personnes">Nombre de personne</button>
                    <ul class="pilule__liste" id="pilule-personnes" hidden>
                        <li><a class="dropdown-item<?php echo $f_personnes === '' ? ' is-active' : ''; ?>" href="<?php echo esc_url(remove_query_arg('personnes')); ?>">Peu importe</a></li>
                        <li><a class="dropdown-item<?php echo $f_personnes === '1-2' ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('personnes', '1-2')); ?>">1 à 2 personnes</a></li>
                        <li><a class="dropdown-item<?php echo $f_personnes === '3-5' ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('personnes', '3-5')); ?>">3 à 5 personnes</a></li>
                        <li><a class="dropdown-item<?php echo $f_personnes === '6-9' ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('personnes', '6-9')); ?>">6 à 9 personnes</a></li>
                        <li><a class="dropdown-item<?php echo $f_personnes === '10+' ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('personnes', '10+')); ?>">10 personnes et plus</a></li>
                    </ul>
                </div>

            </div>

            <!-- Interrupteur d'affichage de la carte -->
            <label class="toggle carte-toggle">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                <span class="carte-toggle__texte">Afficher la carte</span>
                <input type="checkbox" id="carte-checkbox">
                <span class="toggle__track"></span>
            </label>
        </div>

        <!-- Compteur de résultats + tri -->
        <div class="resultats-info">
            <p class="resultats-info__compte">
                <strong><?php echo esc_html($wp_query->found_posts); ?> résultats</strong>
            </p>
            <div class="tri">
                <button type="button" class="tri__bouton" aria-expanded="false" aria-controls="tri-liste">Trier par&nbsp;: <strong id="tri-valeur"><?php echo esc_html($tri_label_courant); ?></strong></button>
                <ul class="tri__liste" id="tri-liste" hidden>
                    <li><a class="dropdown-item<?php echo ($f_tri === '' || $f_tri === 'avis') ? ' is-active' : ''; ?>" href="<?php echo esc_url(remove_query_arg('tri')); ?>">Avis</a></li>
                    <li><a class="dropdown-item<?php echo $f_tri === 'prix-asc' ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('tri', 'prix-asc')); ?>">Prix croissant</a></li>
                    <li><a class="dropdown-item<?php echo $f_tri === 'prix-desc' ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('tri', 'prix-desc')); ?>">Prix décroissant</a></li>
                    <li><a class="dropdown-item<?php echo $f_tri === 'distance' ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('tri', 'distance')); ?>">Distance</a></li>
                    <li><a class="dropdown-item<?php echo $f_tri === 'nouveautes' ? ' is-active' : ''; ?>" href="<?php echo esc_url(add_query_arg('tri', 'nouveautes')); ?>">Nouveautés</a></li>
                </ul>
            </div>
        </div>

        <!-- Grille de résultats (boucle WordPress) + carte -->
        <div class="categorie-layout" id="categorie-layout">
            <section class="resultats" aria-labelledby="resultats-titre">

                <header class="resultats__head">
                    <h2 id="resultats-titre"><?php echo esc_html($titre_page); ?></h2>
                    <p class="resultats__sub">Explorez les pépites cachées à quelques pas de chez vous</p>
                </header>

                <div class="resultats-grid">
                    <?php if (have_posts()) : ?>
                        <?php while (have_posts()) : the_post(); ?>
                            <?php poolparty_g4_carte_bien(get_the_ID()); ?>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <p>Aucun bien disponible pour le moment.</p>
                    <?php endif; ?>
                </div>

            </section>

            <!-- Panneau carte : Leaflet + OpenStreetMap, marqueurs créés en JS
                 à partir des data-lat / data-lon des cartes ci-dessus. -->
            <aside class="carte-panel" id="carte-panel" hidden>
                <div class="carte-osm" id="carte-osm" aria-label="Carte des espaces disponibles en Île-de-France"></div>
            </aside>
        </div>
    </main>

<?php get_footer(); ?>
