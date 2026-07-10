<?php
/**
 * Gabarit de la page « Mentions légales » (contenu repris de mentions-legales.html).
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 */
get_header();
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
                    <li><span class="is-current" aria-current="page">Mentions légales</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="legal-hero">
            <h1>Mentions légales</h1>
            <p>Cette page identifie le responsable du site, son hébergeur et les modalités de traitement de vos données personnelles, conformément à la loi pour la Confiance dans l'Économie Numérique (LCEN, art. 6, 2004).</p>
            <p class="legal-hero__date">Dernière mise à jour : 4 juillet 2026</p>
        </section>

        <!-- BLOC 3 : Corps du document -->
        <div class="legal-layout legal-content">

            <!-- Encart obligatoire : projet pédagogique fictif -->
            <aside class="legal-notice" aria-label="Avertissement : projet pédagogique fictif">
                <p><strong>Projet pédagogique fictif.</strong> Ce site internet est réalisé dans le cadre d'un projet pédagogique fictif mené à l'Institut F2i (formation Chef de Projet Digital, promotion 2025-2026). La société Pool Party, la cliente Mme Francine et l'ensemble des contenus présentés sont entièrement fictifs.</p>
                <p>Aucune réservation réelle n'est possible sur cette plateforme et aucune transaction financière n'est réellement encaissée. Ce projet est réalisé à des fins strictement académiques et ne constitue pas une offre commerciale.</p>
            </aside>

            <!-- Sommaire -->
            <nav class="legal-sommaire" aria-labelledby="sommaire-titre">
                <h2 id="sommaire-titre">Sommaire</h2>
                <ol>
                    <li><a href="#editeur">Éditeur du site</a></li>
                    <li><a href="#agence">Agence de conception et de réalisation</a></li>
                    <li><a href="#hebergement">Hébergement</a></li>
                    <li><a href="#propriete">Propriété intellectuelle</a></li>
                    <li><a href="#donnees">Données personnelles</a></li>
                    <li><a href="#cookies">Cookies</a></li>
                    <li><a href="#responsabilite">Responsabilité</a></li>
                    <li><a href="#credits">Crédits</a></li>
                </ol>
            </nav>

            <section id="editeur" aria-labelledby="editeur-titre">
                <h2 id="editeur-titre">1. Éditeur du site</h2>
                <dl class="legal-fiche">
                    <dt>Dénomination sociale</dt>
                    <dd>Pool Party SARL</dd>
                    <dt>Forme juridique</dt>
                    <dd>Société à responsabilité limitée (SARL)</dd>
                    <dt>Capital social</dt>
                    <dd>8 000 euros</dd>
                    <dt>Siège social</dt>
                    <dd>12 rue de Rivoli, 75001 Paris, France</dd>
                    <dt>Numéro SIRET</dt>
                    <dd>912 456 789 00015 (numéro fictif : projet étudiant)</dd>
                    <dt>Directrice de la publication</dt>
                    <dd>Mme Francine, fondatrice</dd>
                    <dt>Contact</dt>
                    <dd><a href="mailto:contact@poolparty.fr">contact@poolparty.fr</a></dd>
                    <dt>Téléphone</dt>
                    <dd><a href="tel:+33123456789">01 23 45 67 89</a></dd>
                </dl>
            </section>

            <section id="agence" aria-labelledby="agence-titre">
                <h2 id="agence-titre">2. Agence de conception et de réalisation</h2>
                <dl class="legal-fiche">
                    <dt>Nom</dt>
                    <dd>Lumina KAM Digital</dd>
                    <dt>Cheffe de projet</dt>
                    <dd>Audrey Garoscio</dd>
                    <dt>Équipe</dt>
                    <dd>Audrey Garoscio, Katia Messaoui, Moisette Moukoko</dd>
                    <dt>Formation</dt>
                    <dd>Institut F2i, diplôme Chef de Projet Digital (niveau 6)</dd>
                    <dt>Contact agence</dt>
                    <dd><a href="mailto:lumina.kam.digital@gmail.com">lumina.kam.digital@gmail.com</a></dd>
                </dl>
            </section>

            <section id="hebergement" aria-labelledby="hebergement-titre">
                <h2 id="hebergement-titre">3. Hébergement</h2>
                <p>Le site <strong>poolparty-dsp-ddm-f25a-o24b-g4.fr</strong> est hébergé par :</p>
                <dl class="legal-fiche">
                    <dt>Hébergeur</dt>
                    <dd>o2switch SAS</dd>
                    <dt>Adresse</dt>
                    <dd>222-224 Boulevard Gustave Flaubert, 63000 Clermont-Ferrand, France</dd>
                    <dt>Téléphone</dt>
                    <dd><a href="tel:+33444446040">04 44 44 60 40</a></dd>
                    <dt>Site web</dt>
                    <dd><a href="https://www.o2switch.fr" rel="external noopener">www.o2switch.fr</a></dd>
                    <dt>SIRET</dt>
                    <dd>510 909 807 00032</dd>
                </dl>
            </section>

            <section id="propriete" aria-labelledby="propriete-titre">
                <h2 id="propriete-titre">4. Propriété intellectuelle</h2>
                <p>L'ensemble des contenus présents sur ce site, notamment les textes, images, illustrations, logotypes, icônes et éléments graphiques, sont la propriété exclusive de l'équipe Lumina KAM Digital ou font l'objet de licences d'utilisation appropriées.</p>
                <p>Toute reproduction, représentation, modification, publication ou transmission de ces éléments, par quelque moyen que ce soit, est strictement interdite sans l'accord préalable et écrit des auteurs (Code de la Propriété Intellectuelle, art. L111-1).</p>
            </section>

            <section id="donnees" aria-labelledby="donnees-titre">
                <h2 id="donnees-titre">5. Données personnelles</h2>
                <p>Ce site est susceptible de collecter des données personnelles dans le cadre de la navigation, de la création de compte, du formulaire de contact et de l'inscription à la newsletter. Le traitement de ces données est soumis au Règlement Général sur la Protection des Données (RGPD, UE 2016/679) et à la loi Informatique et Libertés du 6 janvier 1978 modifiée.</p>
                <p>Les données collectées ne sont jamais revendues à des tiers. Elles sont conservées pendant une durée de 3 ans à compter de la dernière activité du compte.</p>
                <h3>Vos droits</h3>
                <p>Conformément au RGPD, vous disposez des droits suivants, exerçables en écrivant à <a href="mailto:contact@poolparty.fr">contact@poolparty.fr</a> :</p>
                <ul class="legal-liste">
                    <li>Droit d'accès à vos données (art. 15 RGPD)</li>
                    <li>Droit de rectification (art. 16 RGPD)</li>
                    <li>Droit à l'effacement, dit droit à l'oubli (art. 17 RGPD)</li>
                    <li>Droit à la portabilité de vos données (art. 20 RGPD)</li>
                    <li>Droit d'opposition au traitement (art. 21 RGPD)</li>
                    <li>Droit de retirer votre consentement à tout moment pour les traitements fondés sur celui-ci</li>
                </ul>
                <p>En cas de réclamation non résolue, vous pouvez saisir la Commission Nationale de l'Informatique et des Libertés (CNIL) via le site <a href="https://www.cnil.fr" rel="external noopener">cnil.fr</a>.</p>
            </section>

            <section id="cookies" aria-labelledby="cookies-titre">
                <h2 id="cookies-titre">6. Cookies</h2>
                <p>Un bandeau de gestion des cookies est présent sur le site, conformément aux recommandations consolidées de la CNIL. Il vous permet d'accepter ou de refuser librement chaque catégorie de cookies, le refus étant aussi simple que l'acceptation.</p>
                <ul class="legal-liste">
                    <li><strong>Cookies strictement nécessaires</strong> (session, authentification) : indispensables au fonctionnement du site, ils ne nécessitent pas de consentement préalable.</li>
                    <li><strong>Cookies de mesure d'audience</strong> (Google Analytics 4) : déposés uniquement avec votre consentement, ils servent à comprendre l'usage du site pour l'améliorer.</li>
                    <li><strong>Cookies de préférences</strong> : ils mémorisent vos choix de navigation (recherches récentes, favoris) avec votre consentement.</li>
                </ul>
                <p>Vous pouvez modifier vos choix à tout moment depuis le lien de gestion des cookies présent en pied de page.</p>
            </section>

            <section id="responsabilite" aria-labelledby="responsabilite-titre">
                <h2 id="responsabilite-titre">7. Responsabilité</h2>
                <p>Lumina KAM Digital et Pool Party s'efforcent de fournir sur ce site des informations aussi précises que possible. Toutefois, compte tenu du caractère pédagogique et fictif du projet, les informations publiées sont susceptibles d'inexactitudes ou d'omissions et ne sauraient engager la responsabilité des auteurs.</p>
            </section>

            <section id="credits" aria-labelledby="credits-titre">
                <h2 id="credits-titre">8. Crédits</h2>
                <p>Conception, design et intégration : Lumina KAM Digital. Les photographies d'illustration proviennent de la banque d'images Unsplash et sont utilisées dans le respect de leur licence. Les personnes, lieux et annonces présentés sont fictifs.</p>
            </section>

        </div>
    </main>

<?php get_footer(); ?>
