<?php
/**
 * Gabarit de la page « Plan du site ». Les listes sont construites à
 * partir du contenu réel (pages, catégories de biens, annonces,
 * articles) : la page reste juste sans intervention quand un contenu
 * est ajouté ou retiré. Les pages de l'espace personnel n'y figurent
 * pas, elles n'ont de sens qu'une fois connecté.
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 */
get_header();

$pages_publiques = array(
    'a-propos'           => 'À propos',
    'contact'            => 'Contact',
    'faq'                => 'FAQ',
    'actualites'         => 'Actualités',
    'presse'             => 'Presse',
    'partenaires'        => 'Partenaires',
    'devenir-partenaire' => 'Devenir partenaire',
    'proposer'           => 'Proposer votre espace',
);

$pages_pratiques = array(
    'moyen-de-paiement' => 'Moyens de paiement',
    'paiement-securise' => 'Paiement sécurisé',
    'assurance'         => 'Assurances',
    'accessibilite'     => 'Accessibilité',
);

$pages_legales = array(
    'mentions-legales' => 'Mentions légales',
    'cgu'              => "Conditions générales d'utilisation",
    'cgv'              => 'Conditions générales de vente',
);

/**
 * Liste de liens vers des pages désignées par leur slug. Une page
 * absente ou dépubliée est simplement ignorée.
 */
function pp_plan_liste_pages($pages) {
    echo '<ul class="legal-liste">';
    foreach ($pages as $slug => $titre) {
        $page = get_page_by_path($slug, OBJECT, 'page');
        if (!$page || $page->post_status !== 'publish') {
            continue;
        }
        echo '<li><a href="' . esc_url(get_permalink($page)) . '">' . esc_html($titre) . '</a></li>';
    }
    echo '</ul>';
}

$categories = get_terms(array(
    'taxonomy'   => 'categorie_bien',
    'hide_empty' => true,
));

$biens = get_posts(array(
    'post_type'      => 'bien',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
));

$articles = get_posts(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
));
?>

    <main id="contenu">

        <!-- BLOC 1 : Fil d'Ariane -->
        <div class="legal-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><span class="is-current" aria-current="page">Plan du site</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="legal-hero">
            <h1>Plan du site</h1>
            <p>Toutes les pages de Pool Party réunies au même endroit : les rubriques du site, les catégories d'espaces, les annonces en ligne et les articles du journal.</p>
        </section>

        <!-- BLOC 3 : Rubriques -->
        <div class="legal-layout legal-content">

            <section aria-labelledby="plan-decouvrir">
                <h2 id="plan-decouvrir">Découvrir Pool Party</h2>
                <?php
                echo '<ul class="legal-liste"><li><a href="' . esc_url(home_url('/')) . '">Accueil</a></li>';
                $catalogue = get_post_type_archive_link('bien');
                if ($catalogue) {
                    echo '<li><a href="' . esc_url($catalogue) . '">Catalogue des espaces</a></li>';
                }
                echo '</ul>';
                pp_plan_liste_pages($pages_publiques);
                ?>
            </section>

            <?php if (!is_wp_error($categories) && $categories) : ?>
            <section aria-labelledby="plan-categories">
                <h2 id="plan-categories">Types d'espaces</h2>
                <ul class="legal-liste">
                    <?php foreach ($categories as $categorie) : ?>
                        <li><a href="<?php echo esc_url(get_term_link($categorie)); ?>"><?php echo esc_html($categorie->name); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <?php endif; ?>

            <?php if ($biens) : ?>
            <section aria-labelledby="plan-biens">
                <h2 id="plan-biens">Espaces à louer</h2>
                <ul class="legal-liste">
                    <?php foreach ($biens as $bien) : ?>
                        <li><a href="<?php echo esc_url(get_permalink($bien)); ?>"><?php echo esc_html(get_the_title($bien)); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <?php endif; ?>

            <?php if ($articles) : ?>
            <section aria-labelledby="plan-journal">
                <h2 id="plan-journal">Le journal</h2>
                <ul class="legal-liste">
                    <?php foreach ($articles as $article) : ?>
                        <li><a href="<?php echo esc_url(get_permalink($article)); ?>"><?php echo esc_html(get_the_title($article)); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </section>
            <?php endif; ?>

            <section aria-labelledby="plan-pratique">
                <h2 id="plan-pratique">Informations pratiques</h2>
                <?php pp_plan_liste_pages($pages_pratiques); ?>
            </section>

            <section aria-labelledby="plan-legal">
                <h2 id="plan-legal">Mentions légales</h2>
                <?php pp_plan_liste_pages($pages_legales); ?>
            </section>

        </div>

    </main>

<?php
get_footer();
