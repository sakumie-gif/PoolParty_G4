<?php
/**
 * Gabarit de la page « Mes favoris » (contenu repris de favoris.html).
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 */
get_header();
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane + titre + compteur (rempli par main.js) -->
        <section class="favoris-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li class="is-current" aria-current="page">Mes favoris</li>
                </ol>
            </nav>

            <h1>Mes favoris</h1>
            <p class="favoris-intro__sub" id="favoris-compte" aria-live="polite"></p>
        </section>

        <!-- BLOC 2 : État visiteur. Les favoris sont liés au compte :
             on invite à se connecter avant de les afficher (les boutons
             ouvrent les pop-ups de connexion et d'inscription). -->
        <section class="favoris-etat favoris-etat--connexion" id="favoris-connexion" aria-labelledby="favoris-connexion-titre" hidden>
            <svg class="favoris-etat__coeur" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.51 4.04 3 5.5l7 7Z"/></svg>
            <h2 id="favoris-connexion-titre">Connectez-vous pour retrouver vos favoris</h2>
            <p>Vos coups de coeur sont rattachés à votre compte Pool Party. Connectez-vous pour les retrouver ici à chacune de vos visites.</p>
            <div class="favoris-etat__actions">
                <button type="button" class="btn btn-primary js-open-login">Connexion</button>
                <a href="<?php echo esc_url(home_url('/inscription/')); ?>" class="btn btn-secondary">Inscription</a>
            </div>
        </section>

        <!-- BLOC 3 : État liste vide, une fois connecté -->
        <section class="favoris-etat favoris-etat--vide" id="favoris-vide" aria-labelledby="favoris-vide-titre" hidden>
            <svg class="favoris-etat__coeur" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.51 4.04 3 5.5l7 7Z"/></svg>
            <h2 id="favoris-vide-titre">Rien dans vos favoris pour le moment</h2>
            <p>Touchez le coeur d'une annonce pour la garder sous la main : elle vous attendra ici, prête à être réservée.</p>
            <div class="favoris-etat__actions">
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="btn btn-primary">Explorer les espaces</a>
            </div>
        </section>

        <!-- BLOC 4 : Grille des annonces enregistrées (cartes construites
             par main.js depuis le localStorage, composant card-product) -->
        <section class="favoris-liste" aria-label="Vos annonces enregistrées">
            <div class="favoris-grid" id="favoris-grid" hidden></div>
        </section>
    </main>

<?php get_footer(); ?>
