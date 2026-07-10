<?php
/**
 * Gabarit de la page « Page introuvable » (contenu repris de 404.html).
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 */
get_header();
?>

    <main id="contenu">

        <!-- Écran d'erreur : code géant, vague décorative, message
             et portes de sortie vers les pages clés du site. -->
        <section class="error-page">
            <p class="error-page__code" aria-hidden="true">404</p>

            <svg class="error-page__wave" viewBox="0 0 220 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M2 14c18-16 36-16 54 0s36 16 54 0 36-16 54 0 36 16 54 0" stroke="currentColor" stroke-width="3" stroke-linecap="round"/>
            </svg>

            <h1>Cette page a pris l'eau</h1>
            <p class="error-page__text">L'adresse demandée n'existe pas ou plus : l'annonce a peut-être été retirée, ou une faute de frappe s'est glissée dans l'URL. Le reste du site vous attend au sec.</p>

            <h2 class="sr-only">Pour continuer votre visite</h2>
            <div class="error-page__actions">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">Retour à l'accueil</a>
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="btn btn-secondary">Explorer les espaces</a>
            </div>

            <p class="error-page__aide">Besoin d'un coup de main ? Consultez la <a href="<?php echo esc_url(home_url('/faq/')); ?>">FAQ</a> ou <a href="<?php echo esc_url(home_url('/contact/')); ?>">contactez-nous</a>.</p>
        </section>

    </main>

<?php get_footer(); ?>
