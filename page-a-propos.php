<?php
/**
 * Gabarit de la page « À propos » (contenu repris de a-propos.html).
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 */
get_header();
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane -->
        <div class="apropos-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><span class="is-current" aria-current="page">À propos</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="apropos-hero">
            <h1>Pool Party, née d'une idée toute simple</h1>
            <p>Des milliers de piscines dorment derrière les clôtures d'Île-de-France pendant que d'autres rêvent d'un plongeon. Nous avons décidé de les réunir.</p>
        </section>

        <!-- BLOC 3 : Notre histoire -->
        <section class="apropos-histoire" aria-labelledby="histoire-titre">
            <div class="apropos-histoire__content">
                <h2 id="histoire-titre">Notre histoire</h2>
                <p>Tout commence un été, dans un jardin de Seine-et-Marne. Francine, notre fondatrice, observe sa piscine : entretenue à l'année, utilisée quelques week-ends à peine. Au même moment, ses voisins enchaînent les allers-retours vers des bases de loisirs bondées.</p>
                <p>Le constat est là : d'un côté des bassins sous-exploités qui coûtent cher à leurs propriétaires, de l'autre des familles, des amis et des sportifs en quête d'un coin d'eau tranquille. Pool Party naît en 2025 pour faire se rencontrer ces deux mondes.</p>
                <p>Aujourd'hui, la plateforme référence des piscines, jacuzzis, spas, saunas et hammams chez des particuliers de toute l'Île-de-France. Chaque réservation aide un hôte à rentabiliser son espace et offre à un locataire un moment privilégié, loin de la foule.</p>
            </div>
            <img class="apropos-histoire__img" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-croissy.jpg'); ?>" alt="Piscine privée entourée de verdure chez un hôte Pool Party">
        </section>

        <!-- BLOC 4 : Chiffres clés -->
        <section class="apropos-chiffres" aria-labelledby="chiffres-titre">
            <h2 class="sr-only" id="chiffres-titre">Pool Party en chiffres</h2>
            <ul class="apropos-chiffres__liste">
                <li class="apropos-chiffre">
                    <span class="apropos-chiffre__valeur">350</span>
                    <span class="apropos-chiffre__label">espaces référencés en Île-de-France</span>
                </li>
                <li class="apropos-chiffre">
                    <span class="apropos-chiffre__valeur">18 000</span>
                    <span class="apropos-chiffre__label">baignades réservées depuis le lancement</span>
                </li>
                <li class="apropos-chiffre">
                    <span class="apropos-chiffre__valeur">4,8 / 5</span>
                    <span class="apropos-chiffre__label">note moyenne laissée par nos locataires</span>
                </li>
                <li class="apropos-chiffre">
                    <span class="apropos-chiffre__valeur">24 h</span>
                    <span class="apropos-chiffre__label">pour verser leurs revenus aux hôtes</span>
                </li>
            </ul>
        </section>

        <!-- BLOC 5 : Nos valeurs -->
        <section class="apropos-valeurs" aria-labelledby="valeurs-titre">
            <h2 id="valeurs-titre">Ce qui nous guide</h2>
            <p class="apropos-valeurs__sub">Trois convictions portent chacune de nos décisions, de la sélection des annonces au service client.</p>

            <div class="apropos-valeurs__grid">
                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                    <h3 class="card-protection__title">La confiance</h3>
                    <p class="card-protection__text">Identités vérifiées, avis authentiques, assurance incluse sur chaque réservation. Hôtes comme locataires profitent l'esprit léger.</p>
                </article>

                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2.69 17.66 8.35a8 8 0 1 1-11.31 0z"/></svg>
                    <h3 class="card-protection__title">La simplicité</h3>
                    <p class="card-protection__text">Chercher, réserver, plonger. Trois étapes suffisent, sans paperasse ni frais cachés : le prix affiché est le prix payé.</p>
                </article>

                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                    <h3 class="card-protection__title">La proximité</h3>
                    <p class="card-protection__text">Des espaces à moins de trente minutes de chez vous et une équipe basée en France, joignable du lundi au samedi.</p>
                </article>
            </div>
        </section>

        <!-- BLOC 6 : Le mot de la fondatrice -->
        <section class="apropos-fondatrice" aria-labelledby="fondatrice-titre">
            <h2 class="sr-only" id="fondatrice-titre">Le mot de la fondatrice</h2>
            <figure class="apropos-fondatrice__carte">
                <blockquote class="apropos-fondatrice__citation">
                    <p>"Une piscine qui dort, c'est un été qui se perd. Mon rêve avec Pool Party : que chaque bassin d'Île-de-France profite à ceux qui l'entourent, et que chaque hôte soit fier d'ouvrir son jardin."</p>
                </blockquote>
                <figcaption class="apropos-fondatrice__auteur">
                    <span class="card-avis__avatar" aria-hidden="true">F</span>
                    <div>
                        <p class="apropos-fondatrice__nom">Francine</p>
                        <p class="apropos-fondatrice__role">Fondatrice de Pool Party</p>
                    </div>
                </figcaption>
            </figure>
        </section>

        <!-- BLOC 7 : CTA final -->
        <section class="apropos-cta" aria-labelledby="apropos-cta-titre">
            <h2 id="apropos-cta-titre">Envie de faire partie de l'aventure ?</h2>
            <p>Trouvez votre prochain coin de baignade ou rentabilisez le vôtre dès aujourd'hui.</p>
            <div class="apropos-cta__actions">
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="btn btn-tertiary">Explorer les espaces</a>
                <a href="<?php echo esc_url(home_url('/proposer/')); ?>" class="btn btn-secondary">Proposer mon espace</a>
            </div>
        </section>
    </main>

<?php get_footer(); ?>
