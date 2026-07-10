<?php
/**
 * Page d'accueil : les 11 blocs validés de index.html (hero,
 * carrousel de catégories, coups de coeur, simplicité, témoignages,
 * CTA hôte, réassurance, événements, newsletter), markup repris tel
 * quel. Les cartes annonce restent en dur comme sur le statique ;
 * elles seront recâblées sur le CPT Bien à l'étape 4 si besoin.
 * Les liens produit pointent sur /produit/ en attendant le CPT.
 */

get_header();
?>

    <main id="contenu">
        <!-- BLOC 1 : Hero + barre de recherche -->
        <section class="hero">
            <picture class="hero-image">
                <source srcset="<?php echo esc_url(pp_asset('assets/images/hero/hero-original.webp')); ?>" type="image/webp">
                <img src="<?php echo esc_url(pp_asset('assets/images/hero/hero-original.jpg')); ?>" width="1920" height="468" fetchpriority="high" decoding="async" alt="Femme se détendant dans une piscine à débordement entourée de végétation tropicale">
            </picture>

            <div class="hero-content">
                <h1 class="hero-title"><?php echo esc_html(pp_accueil_texte('hero_title')); ?></h1>
                <p class="hero-subtitle"><?php echo esc_html(pp_accueil_texte('hero_subtitle')); ?></p>
            </div>

            <form class="hero-search" action="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" role="search" aria-label="Rechercher un espace">
                <div class="search-field">
                    <label for="hero-quoi">Quoi</label>
                    <input type="text" id="hero-quoi" name="quoi" placeholder="Choisissez votre bien">
                </div>
                <span class="search-divider" aria-hidden="true"></span>
                <div class="search-field">
                    <label for="hero-adresse">Adresse</label>
                    <input type="text" id="hero-adresse" name="adresse" placeholder="Où cherchez-vous ?">
                </div>
                <span class="search-divider" aria-hidden="true"></span>
                <div class="search-field search-field--small">
                    <label for="hero-date">Date</label>
                    <input type="text" id="hero-date" name="date" placeholder="Quand ?">
                </div>
                <span class="search-divider" aria-hidden="true"></span>
                <div class="search-field search-field--small">
                    <label for="hero-invites">Invités</label>
                    <input type="text" id="hero-invites" name="invites" placeholder="Combien ?">
                </div>
                <button type="submit" class="search-submit" aria-label="Lancer la recherche">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                        <line x1="16.5" y1="16.5" x2="21" y2="21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>
        </section>

        <!-- BLOC 2 : Carrousel catégories -->
        <section class="categories" aria-label="Découvrir les espaces par catégorie">
            <h2 class="sr-only">Découvrir les espaces</h2>

            <div class="categories-carousel">
                <div class="categories-track">
                    <button type="button" class="categories-slide pos-m2" data-categorie="pres-de-chez-vous" aria-label="Voir la catégorie Près de chez vous" aria-pressed="false">
                        <img src="<?php echo esc_url(pp_asset('assets/images/categories/categorie-1.jpg')); ?>" alt="" loading="lazy" decoding="async" width="500" height="800">
                    </button>
                    <button type="button" class="categories-slide pos-m1" data-categorie="coup-de-coeur" aria-label="Voir la catégorie Coup de coeur" aria-pressed="false">
                        <img src="<?php echo esc_url(pp_asset('assets/images/categories/categorie-2.jpg')); ?>" alt="" loading="lazy" decoding="async" width="600" height="800">
                    </button>
                    <button type="button" class="categories-slide pos-0" data-categorie="nouveautes" aria-label="Voir la catégorie Nouveautés" aria-pressed="true">
                        <img src="<?php echo esc_url(pp_asset('assets/images/categories/categorie-3.jpg')); ?>" alt="" loading="lazy" decoding="async" width="533" height="800">
                    </button>
                    <button type="button" class="categories-slide pos-p1" data-categorie="spas-jacuzzis" aria-label="Voir la catégorie Spas et Jacuzzis" aria-pressed="false">
                        <img src="<?php echo esc_url(pp_asset('assets/images/categories/categorie-4.jpg')); ?>" alt="" loading="lazy" decoding="async" width="533" height="800">
                    </button>
                    <button type="button" class="categories-slide pos-p2" data-categorie="insolite" aria-label="Voir la catégorie Insolite" aria-pressed="false">
                        <img src="<?php echo esc_url(pp_asset('assets/images/categories/categorie-5.jpg')); ?>" alt="" loading="lazy" decoding="async" width="466" height="800">
                    </button>
                </div>
                <button type="button" class="carousel-control carousel-control--prev categories-control categories-control--prev" data-carousel-prev aria-label="Catégorie précédente"></button>
                <button type="button" class="carousel-control carousel-control--next categories-control categories-control--next" data-carousel-next aria-label="Catégorie suivante"></button>
            </div>

            <div class="categories-tabs" aria-label="Catégories">
                <button type="button" class="categorie-tab" data-categorie="pres-de-chez-vous" aria-pressed="false">
                    <span class="progress"><span class="progress__bar"></span></span>
                    <span class="categorie-tab__label">Près de chez vous</span>
                </button>
                <button type="button" class="categorie-tab" data-categorie="coup-de-coeur" aria-pressed="false">
                    <span class="progress"><span class="progress__bar"></span></span>
                    <span class="categorie-tab__label">Coup de coeur</span>
                </button>
                <button type="button" class="categorie-tab is-active" data-categorie="nouveautes" aria-pressed="true">
                    <span class="progress"><span class="progress__bar"></span></span>
                    <span class="categorie-tab__label">Nouveautés</span>
                </button>
                <button type="button" class="categorie-tab" data-categorie="spas-jacuzzis" aria-pressed="false">
                    <span class="progress"><span class="progress__bar"></span></span>
                    <span class="categorie-tab__label">Spas &amp; Jacuzzis</span>
                </button>
                <button type="button" class="categorie-tab" data-categorie="insolite" aria-pressed="false">
                    <span class="progress"><span class="progress__bar"></span></span>
                    <span class="categorie-tab__label">Insolite</span>
                </button>
            </div>
        </section>

        <!-- BLOC 3 : Coups de coeur du moment, cartes annonces -->
        <section class="coup-de-coeur" aria-labelledby="coup-de-coeur-titre">
            <header class="coup-de-coeur__head">
                <h2 id="coup-de-coeur-titre"><?php echo esc_html(pp_accueil_texte('coup_titre')); ?></h2>
                <p class="coup-de-coeur__sub"><?php echo esc_html(pp_accueil_texte('coup_sub')); ?></p>
            </header>

            <div class="products-track" tabindex="0" aria-label="Annonces coups de coeur">
                <?php
                // Coups de coeur : les biens mis en avant (champ « tag »),
                // rendus avec le composant carte du catalogue pour que chaque
                // carte pointe vers la vraie fiche du bien (et non /produit/,
                // route inexistante en WordPress qui renvoyait une 404).
                $coups_de_coeur = new WP_Query(array(
                    'post_type'      => 'bien',
                    'posts_per_page' => 7,
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                    'no_found_rows'  => true,
                    'meta_query'     => array(
                        array('key' => 'pp_tag', 'value' => '', 'compare' => '!='),
                    ),
                ));
                if ($coups_de_coeur->have_posts()) :
                    while ($coups_de_coeur->have_posts()) : $coups_de_coeur->the_post();
                        poolparty_g4_carte_bien(get_the_ID());
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
            </div>

            <div class="coup-de-coeur__footer">
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="btn btn-tertiary coup-de-coeur__link">Explorez tous les espaces</a>
            </div>
        </section>

        <!-- BLOC 4 : La simplicité avant tout, 3 étapes -->
        <section class="simplicite" aria-labelledby="simplicite-titre">
            <h2 id="simplicite-titre">La simplicité avant tout</h2>
            <p class="section-sub">Trois étapes pour plonger dans le bonheur</p>

            <div class="simplicite-track" tabindex="0" aria-label="Les trois étapes du parcours">

                <article class="card-parcours">
                    <div class="card-parcours__badge">
                        <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <circle cx="14.2" cy="14.2" r="13.4" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M23.9 23.9 30.5 30.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <p class="card-parcours__step">1. Explorez</p>
                    <p class="card-parcours__text">Parcourez des centaines d'espaces uniques filtrés selon vos envies : température, services, équipements.</p>
                </article>

                <article class="card-parcours">
                    <div class="card-parcours__badge">
                        <svg viewBox="0 0 33 30" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M1 9.4C1 6.2 3.6 3.6 6.8 3.6h19.1c3.2 0 5.8 2.6 5.8 5.8v14.4c0 3.2-2.6 5.7-5.8 5.7H6.8C3.6 29.5 1 27 1 23.8V9.4Z" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M8.7 6.5V0.75M16.3 6.5V0.75M24 6.5V0.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="m11.5 17.5 2.6 3.1c.08.1.23.1.31 0L22.3 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <p class="card-parcours__step">2. Réservez</p>
                    <p class="card-parcours__text">Choisissez vos créneaux et validez votre demande. Le paiement est 100% sécurisé et géré par nous.</p>
                </article>

                <article class="card-parcours">
                    <div class="card-parcours__badge">
                        <svg viewBox="0 0 27 34" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M20.2 30.8c-.2.9-1 1.6-1.9 1.6H8.1c-.9 0-1.7-.7-1.9-1.6L2.7 9.4h21.1l-3.6 21.4Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                            <path d="M25.7 9.4H0.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="m13.2 9.4.9-8.5c0-.1.1-.2.2-.2h4.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M22.8 16.2c-7.7-3.8-10.7 4.7-19.2 0" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <p class="card-parcours__step">3. Profitez</p>
                    <p class="card-parcours__text">Rendez-vous sur place à l'heure convenue. Votre hôte vous accueille pour un moment inoubliable.</p>
                </article>

            </div>
        </section>

        <!-- BLOC 5 : Témoignages locataires -->
        <section class="temoignages-locataires" aria-labelledby="temoignages-titre">
            <h2 id="temoignages-titre">Ils ont adoré leur expérience</h2>

            <div class="temoignages-track" tabindex="0" aria-label="Témoignages de locataires">

                <article class="card-temoignage">
                    <blockquote class="card-temoignage__quote">"Un après-midi magnifique pour l'anniversaire de ma fille. Le cadre était somptueux et l'hôte d'une gentillesse rare."</blockquote>
                    <footer class="card-temoignage__author">
                        <img class="card-temoignage__photo" src="<?php echo esc_url(pp_asset('assets/images/temoignages/temoin-lucie.jpg')); ?>" alt="Portrait de Lucie G." loading="lazy" decoding="async" width="132" height="200">
                        <div>
                            <p class="card-temoignage__name">Lucie G.</p>
                            <p class="card-temoignage__meta"><span>Paris</span><span>Locataire</span></p>
                        </div>
                    </footer>
                </article>

                <article class="card-temoignage">
                    <blockquote class="card-temoignage__quote">"On cherchait un endroit calme pour souffler en amoureux un dimanche. La piscine était impeccable et le jardin parfait."</blockquote>
                    <footer class="card-temoignage__author">
                        <img class="card-temoignage__photo" src="<?php echo esc_url(pp_asset('assets/images/temoignages/temoin-thomas.jpg')); ?>" alt="Portrait de Thomas R." loading="lazy" decoding="async" width="133" height="200">
                        <div>
                            <p class="card-temoignage__name">Thomas R.</p>
                            <p class="card-temoignage__meta"><span>Versailles</span><span>Locataire</span></p>
                        </div>
                    </footer>
                </article>

                <article class="card-temoignage">
                    <blockquote class="card-temoignage__quote">"Mes enfants ont passé une journée incroyable. Pouvoir profiter d'une piscine privée à vingt minutes de la maison, sans entretien, c'est un vrai luxe."</blockquote>
                    <footer class="card-temoignage__author">
                        <img class="card-temoignage__photo" src="<?php echo esc_url(pp_asset('assets/images/temoignages/temoin-sandrine.jpg')); ?>" alt="Portrait de Sandrine M." loading="lazy" decoding="async" width="159" height="200">
                        <div>
                            <p class="card-temoignage__name">Sandrine M.</p>
                            <p class="card-temoignage__meta"><span>Melun</span><span>Locataire</span></p>
                        </div>
                    </footer>
                </article>

                <article class="card-temoignage">
                    <blockquote class="card-temoignage__quote">"Réservé pour les 30 ans d'un copain. Tout était prêt à notre arrivée, le jacuzzi marchait nickel et l'hôte nous a laissés tranquilles."</blockquote>
                    <footer class="card-temoignage__author">
                        <img class="card-temoignage__photo" src="<?php echo esc_url(pp_asset('assets/images/temoignages/temoin-julien.jpg')); ?>" alt="Portrait de Julien S." loading="lazy" decoding="async" width="200" height="200">
                        <div>
                            <p class="card-temoignage__name">Julien S.</p>
                            <p class="card-temoignage__meta"><span>Paris</span><span>Locataire</span></p>
                        </div>
                    </footer>
                </article>

            </div>
        </section>

        <!-- BLOC 6 : Témoignages propriétaires, "Ils vous ouvrent leurs portes" -->
        <section class="temoignages-proprietaires" aria-labelledby="hotes-titre">
            <div class="hotes-content">
                <h2 id="hotes-titre">Ils vous ouvrent leurs portes</h2>
                <p class="hotes-sub">Des hôtes franciliens qui rentabilisent leur bien sans contrainte</p>

                <ol class="hotes-steps">
                    <li>
                        <span class="hotes-steps__num" aria-hidden="true">1</span>
                        <div>
                            <p class="hotes-steps__title">Inscrivez votre espace</p>
                            <p class="hotes-steps__text">Quelques photos, vos tarifs et vos disponibilités suffisent</p>
                        </div>
                    </li>
                    <li>
                        <span class="hotes-steps__num" aria-hidden="true">2</span>
                        <div>
                            <p class="hotes-steps__title">Gérez vos réservations</p>
                            <p class="hotes-steps__text">Acceptez les demandes qui vous conviennent</p>
                        </div>
                    </li>
                    <li>
                        <span class="hotes-steps__num" aria-hidden="true">3</span>
                        <div>
                            <p class="hotes-steps__title">Rentabilisez vos biens</p>
                            <p class="hotes-steps__text">Recevez vos paiements 24h après chaque location</p>
                        </div>
                    </li>
                </ol>
            </div>

            <div class="hotes-cards">
                <article class="card-hote">
                    <img class="card-hote__photo" src="<?php echo esc_url(pp_asset('assets/images/temoignages/hote-paula.jpg')); ?>" alt="Portrait de Paula G." loading="lazy" decoding="async" width="300" height="300">
                    <h3 class="card-hote__name">Paula G.</h3>
                    <p class="card-hote__role">Hôte à Poissy</p>
                    <span class="rating">8,7</span>
                    <blockquote class="card-hote__quote">"Je rentabilise ma piscine sans contrainte ; je valide chaque demande."</blockquote>
                </article>

                <article class="card-hote card-hote--extra">
                    <img class="card-hote__photo" src="<?php echo esc_url(pp_asset('assets/images/temoignages/hote-yoann.jpg')); ?>" alt="Portrait de Yoann D." loading="lazy" decoding="async" width="241" height="300">
                    <h3 class="card-hote__name">Yoann D.</h3>
                    <p class="card-hote__role">Hôte à Bussy</p>
                    <span class="rating">8,7</span>
                    <blockquote class="card-hote__quote">"Ma piscine ne servait que deux mois par an. Plus maintenant."</blockquote>
                </article>

                <article class="card-hote card-hote--extra">
                    <img class="card-hote__photo" src="<?php echo esc_url(pp_asset('assets/images/temoignages/hote-tony.jpg')); ?>" alt="Portrait de Tony S." loading="lazy" decoding="async" width="300" height="300">
                    <h3 class="card-hote__name">Tony S.</h3>
                    <p class="card-hote__role">Hôte à Chelles</p>
                    <span class="rating">8,7</span>
                    <blockquote class="card-hote__quote">"Je choisis qui je reçois et j'adapte mes créneaux."</blockquote>
                </article>

                <article class="card-hote">
                    <img class="card-hote__photo" src="<?php echo esc_url(pp_asset('assets/images/temoignages/hote-fraise.jpg')); ?>" alt="Portrait de Fraise T." loading="lazy" decoding="async" width="200" height="300">
                    <h3 class="card-hote__name">Fraise T.</h3>
                    <p class="card-hote__role">Hôte à Montmorency</p>
                    <span class="rating">8,7</span>
                    <blockquote class="card-hote__quote">"En trois mois de mise en ligne, j'ai déjà couvert l'installation de mon nouveau spa."</blockquote>
                </article>
            </div>
        </section>

        <!-- BLOC 7 : CTA Proposer mon espace -->
        <section class="cta-hote" aria-labelledby="cta-hote-titre">
            <div class="cta-hote__box">
                <div class="cta-hote__content">
                    <h2 id="cta-hote-titre">Votre espace a bien plus à vous offrir</h2>
                    <p class="cta-hote__text">Rejoignez la plus grande communauté de partage d'espace aquatique d'Europe</p>
                    <a href="<?php echo esc_url(home_url('/proposer/')); ?>" class="btn btn-secondary cta-hote__btn">Commencez l'aventure côté propriétaire</a>
                </div>
                <img class="cta-hote__img" src="<?php echo esc_url(pp_asset('assets/images/cta/sauna.jpg')); ?>" alt="Femme assise dans un sauna en bois clair" loading="lazy" decoding="async" width="936" height="630">
            </div>
        </section>

        <!-- BLOC 8 : Bande de réassurance, les garanties -->
        <section class="reassurance" aria-labelledby="reassurance-titre">
            <h2 id="reassurance-titre">Les garanties PoolParty</h2>

            <div class="reassurance-grid">
                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
                    <h3 class="card-protection__title">Assurance incluse</h3>
                    <p class="card-protection__text">Toutes les réservations sont couvertes pour votre tranquillité.</p>
                </article>

                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <h3 class="card-protection__title">Paiement sécurisé</h3>
                    <p class="card-protection__text">Transaction simple et sécurisée directement sur le site.</p>
                </article>

                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                    <h3 class="card-protection__title">Qualité garantie</h3>
                    <p class="card-protection__text">Des espaces rigoureusement sélectionnés.</p>
                </article>

                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5Zm0 0a9 9 0 0 1 18 0m0 0v5a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3Z"/><path d="M21 16v2a4 4 0 0 1-4 4h-5"/></svg>
                    <h3 class="card-protection__title">Support 6j/7</h3>
                    <p class="card-protection__text">Notre équipe est là pour vous accompagner à chaque étape, du lundi au samedi.</p>
                </article>
            </div>
        </section>

        <!-- BLOC 9 : Des moments à vivre, cartes événements -->
        <section class="evenements" aria-labelledby="evenements-titre">
            <p class="evenements__label">A venir</p>
            <h2 id="evenements-titre">Des moments à vivre encore plus fort</h2>
            <p class="evenements__sub">Des formats clé en main pour transformer un simple créneau en un moment d'exception.</p>

            <div class="evenements-grid">
                <a class="card-event" href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>">
                    <img src="<?php echo esc_url(pp_asset('assets/images/evenements/anniversaire.jpg')); ?>" alt="Ananas à lunettes de soleil entouré de ballons de fête" loading="lazy" decoding="async" width="834" height="556">
                    <span class="card-event__label">Anniversaire</span>
                </a>
                <a class="card-event" href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>">
                    <img src="<?php echo esc_url(pp_asset('assets/images/evenements/famille.jpg')); ?>" alt="Parents et enfant jouant dans une piscine avec une bouée" loading="lazy" decoding="async" width="900" height="566">
                    <span class="card-event__label">Famille</span>
                </a>
                <a class="card-event" href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>">
                    <img src="<?php echo esc_url(pp_asset('assets/images/evenements/detente.jpg')); ?>" alt="Femme se relaxant dans un sauna" loading="lazy" decoding="async" width="900" height="600">
                    <span class="card-event__label">Détente</span>
                </a>
                <a class="card-event" href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>">
                    <img src="<?php echo esc_url(pp_asset('assets/images/evenements/pool-party.jpg')); ?>" alt="Groupe d'amis sautant dans une piscine tropicale au coucher du soleil" loading="lazy" decoding="async" width="900" height="600">
                    <span class="card-event__label">Pool Party</span>
                </a>
                <a class="card-event" href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>">
                    <img src="<?php echo esc_url(pp_asset('assets/images/evenements/sport.jpg')); ?>" alt="Séance d'aquagym dans une piscine intérieure" loading="lazy" decoding="async" width="833" height="667">
                    <span class="card-event__label">Sport</span>
                </a>
                <a class="card-event" href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>">
                    <img src="<?php echo esc_url(pp_asset('assets/images/evenements/team-building.jpg')); ?>" alt="Mains d'une équipe réunies au centre d'un cercle" loading="lazy" decoding="async" width="834" height="556">
                    <span class="card-event__label">Team building</span>
                </a>
            </div>

            <p class="evenements__more">et bien plus encore ...</p>
        </section>

        <!-- BLOC 10 : Newsletter -->
        <section class="newsletter" aria-labelledby="newsletter-titre">
            <div class="newsletter__box">
                <h2 id="newsletter-titre">Ne manquez aucune nouveauté</h2>
                <p class="newsletter__text">Rejoignez la plus grande communauté de partage d'espace aquatique d'Europe, ne manquez aucune nouveauté et bénéficiez d'offres privilégiées</p>
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
