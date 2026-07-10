<?php
/**
 * Gabarit de la page « Actualités » : journal grand public.
 * Liste dynamiquement les Articles WordPress (Posts) : le plus
 * récent est mis à la une, les suivants forment la grille. Chaque
 * carte mène à la page de l'article (single.php). Les articles sont
 * créés par inc/seed-articles.php et éditables dans wp-admin.
 * En-tête / pied fournis par header.php / footer.php ; styles dans
 * functions.php.
 */
get_header();

$journal = new WP_Query(array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 13,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
));
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane -->
        <div class="actus-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><span class="is-current" aria-current="page">Actualités</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="actus-hero">
            <p class="actus-hero__surtitre">Le journal</p>
            <h1>Les actualités Pool Party</h1>
            <p>Nouveautés de la plateforme, conseils pour profiter de l'eau, idées de journées à partager et coulisses de la communauté : suivez tout ce qui fait vivre la location d'espaces aquatiques en Île-de-France.</p>
        </section>

        <?php if ($journal->have_posts()) : ?>
            <?php
            $journal->the_post();
            // Premier article : mise à la une
            $cats  = get_the_category();
            $cat   = !empty($cats) ? $cats[0] : null;
            $image = get_post_meta(get_the_ID(), 'pp_image', true);
            $alt   = get_post_meta(get_the_ID(), 'pp_alt', true);
            ?>
            <!-- BLOC 3 : Article à la une -->
            <section class="actus-featured" aria-labelledby="actus-featured-titre">
                <h2 class="sr-only" id="actus-featured-titre">À la une</h2>
                <article class="actus-une">
                    <a class="actus-une__media" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                        <img src="<?php echo esc_url(pp_asset($image)); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy" decoding="async">
                    </a>
                    <div class="actus-une__body">
                        <div class="actus-card__entete">
                            <?php if ($cat) : ?><span class="tag <?php echo esc_attr(pp_article_tag_class($cat->slug)); ?>"><?php echo esc_html($cat->name); ?></span><?php endif; ?>
                            <time class="actus-card__date" datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"><?php echo esc_html(pp_date_fr(get_the_time('U'))); ?></time>
                        </div>
                        <h3 class="actus-une__titre"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p class="actus-une__resume"><?php echo esc_html(get_the_excerpt()); ?></p>
                        <a href="<?php the_permalink(); ?>" class="btn-link">Lire l'article</a>
                    </div>
                </article>
            </section>

            <!-- BLOC 4 : Grille d'articles -->
            <section class="actus-liste" aria-labelledby="actus-liste-titre">
                <h2 id="actus-liste-titre">Tous les articles</h2>
                <ul class="actus-grille">
                    <?php while ($journal->have_posts()) : $journal->the_post(); ?>
                        <?php
                        $cats  = get_the_category();
                        $cat   = !empty($cats) ? $cats[0] : null;
                        $image = get_post_meta(get_the_ID(), 'pp_image', true);
                        $alt   = get_post_meta(get_the_ID(), 'pp_alt', true);
                        ?>
                        <li>
                            <article class="actus-card">
                                <a class="actus-card__media" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
                                    <img src="<?php echo esc_url(pp_asset($image)); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy" decoding="async">
                                </a>
                                <div class="actus-card__body">
                                    <div class="actus-card__entete">
                                        <?php if ($cat) : ?><span class="tag <?php echo esc_attr(pp_article_tag_class($cat->slug)); ?>"><?php echo esc_html($cat->name); ?></span><?php endif; ?>
                                        <time class="actus-card__date" datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"><?php echo esc_html(pp_date_fr(get_the_time('U'))); ?></time>
                                    </div>
                                    <h3 class="actus-card__titre"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                    <p class="actus-card__resume"><?php echo esc_html(get_the_excerpt()); ?></p>
                                    <a href="<?php the_permalink(); ?>" class="btn-link">Lire l'article</a>
                                </div>
                            </article>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </section>
            <?php wp_reset_postdata(); ?>
        <?php else : ?>
            <section class="actus-liste">
                <p>Aucun article pour le moment. Revenez bientôt.</p>
            </section>
        <?php endif; ?>

        <!-- BLOC 5 : Newsletter (même composant que l'accueil, géré par main.js) -->
        <section class="newsletter" aria-labelledby="newsletter-titre">
            <div class="newsletter__box">
                <h2 id="newsletter-titre">Ne manquez aucune nouveauté</h2>
                <p class="newsletter__text">Recevez nos derniers articles, nos conseils baignade et nos offres privilégiées directement dans votre boîte mail.</p>
                <form class="newsletter__form">
                    <label class="sr-only" for="newsletter-email">Votre adresse email</label>
                    <input type="email" id="newsletter-email" name="email" placeholder="exemple@email.com" required>
                    <button type="submit" class="btn btn-secondary">S'inscrire</button>
                </form>
                <p class="newsletter__rgpd">En vous inscrivant, vous acceptez de recevoir nos actualités par e-mail. Désinscription possible à tout moment via le lien présent dans chaque message ; pour en savoir plus, consultez notre <a href="<?php echo esc_url(home_url('/mentions-legales/#donnees')); ?>">politique de confidentialité</a>.</p>
            </div>
        </section>
    </main>

<?php get_footer(); ?>
