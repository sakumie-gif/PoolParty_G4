<?php
/**
 * Gabarit d'un Article du journal (single Post).
 * Fil d'Ariane, image d'en-tête, rubrique + date, titre, corps de
 * l'article puis retour vers la page Actualités. Styles dans
 * css/actualites.css (chargé sur is_singular('post') via functions.php).
 */
get_header();

while (have_posts()) : the_post();
    $cats  = get_the_category();
    $cat   = !empty($cats) ? $cats[0] : null;
    $image = get_post_meta(get_the_ID(), 'pp_image', true);
    $alt   = get_post_meta(get_the_ID(), 'pp_alt', true);
?>

    <main id="contenu">
        <!-- Fil d'Ariane -->
        <div class="actus-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><a href="<?php echo esc_url(home_url('/actualites/')); ?>">Actualités</a></li>
                    <li><span class="is-current" aria-current="page"><?php the_title(); ?></span></li>
                </ol>
            </nav>
        </div>

        <article class="article">
            <!-- En-tête : rubrique, date, titre -->
            <header class="article-header">
                <div class="actus-card__entete">
                    <?php if ($cat) : ?><span class="tag <?php echo esc_attr(pp_article_tag_class($cat->slug)); ?>"><?php echo esc_html($cat->name); ?></span><?php endif; ?>
                    <time class="actus-card__date" datetime="<?php echo esc_attr(get_the_date('Y-m-d')); ?>"><?php echo esc_html(pp_date_fr(get_the_time('U'))); ?></time>
                </div>
                <h1 class="article-titre"><?php the_title(); ?></h1>
            </header>

            <!-- Image d'en-tête -->
            <?php if ($image) : ?>
                <div class="article-hero">
                    <img src="<?php echo esc_url(pp_asset($image)); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy" decoding="async">
                </div>
            <?php endif; ?>

            <!-- Corps de l'article -->
            <div class="article-body">
                <?php the_content(); ?>
            </div>

            <!-- Retour au journal -->
            <div class="article-back">
                <a href="<?php echo esc_url(home_url('/actualites/')); ?>" class="article-back__link">Retour aux actualités</a>
            </div>
        </article>
    </main>

<?php
endwhile;
get_footer();
