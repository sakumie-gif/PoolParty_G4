<?php
/**
 * Gabarit de secours : affiché quand aucun gabarit plus précis
 * ne s'applique (boucle WordPress minimale). Les vrais gabarits
 * arrivent aux étapes suivantes : front-page.php, archive-bien.php,
 * single-bien.php, page.php...
 */

get_header();
?>

    <main id="contenu">
        <section class="index-fallback">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class(); ?>>
                        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <?php the_excerpt(); ?>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <p>Aucun contenu à afficher pour le moment.</p>
            <?php endif; ?>
        </section>
    </main>

<?php get_footer(); ?>
