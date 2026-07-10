<?php
/**
 * Gabarit de la page « Presse » (contenu repris de presse.html).
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 */
get_header();

// Dossier des ressources presse téléchargeables (PDF des communiqués,
// charte, dossier, ZIP des logos et des photos) livrées avec le thème.
$presse_dir = trailingslashit(get_template_directory_uri()) . 'assets/presse/';
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane -->
        <div class="presse-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><span class="is-current" aria-current="page">Presse</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="presse-hero">
            <p class="presse-hero__surtitre">Espace presse</p>
            <h1>Pool Party dans les médias</h1>
            <p>Journalistes, blogueurs, créateurs de contenu : retrouvez ici nos communiqués, nos chiffres clés et toutes les ressources visuelles pour parler de Pool Party, la plateforme de location de piscines entre particuliers.</p>
            <div class="presse-hero__actions">
                <a href="#kit-presse" class="btn btn-secondary btn-medium">Télécharger le kit presse</a>
                <a href="#contact-presse" class="btn btn-tertiary btn-medium">Contacter le service presse</a>
            </div>
        </section>

        <!-- BLOC 3 : Chiffres clés -->
        <section class="presse-chiffres" aria-labelledby="presse-chiffres-titre">
            <h2 id="presse-chiffres-titre">Pool Party en chiffres</h2>
            <ul class="presse-chiffres__grille">
                <li class="presse-chiffre">
                    <span class="presse-chiffre__valeur">350</span>
                    <span class="presse-chiffre__label">espaces référencés en Île-de-France</span>
                </li>
                <li class="presse-chiffre">
                    <span class="presse-chiffre__valeur">18&nbsp;000</span>
                    <span class="presse-chiffre__label">baignades réservées depuis le lancement</span>
                </li>
                <li class="presse-chiffre">
                    <span class="presse-chiffre__valeur">4,8/5</span>
                    <span class="presse-chiffre__label">note moyenne attribuée par les locataires</span>
                </li>
                <li class="presse-chiffre">
                    <span class="presse-chiffre__valeur">2025</span>
                    <span class="presse-chiffre__label">année de création à Paris</span>
                </li>
            </ul>
        </section>

        <!-- BLOC 4 : Communiqués de presse -->
        <section class="presse-communiques" aria-labelledby="presse-communiques-titre">
            <h2 id="presse-communiques-titre">Communiqués de presse</h2>
            <ul class="presse-communiques__liste">
                <li>
                    <article class="presse-communique">
                        <div class="presse-communique__entete">
                            <time class="presse-communique__date" datetime="2026-06-12">12 juin 2026</time>
                            <span class="tag tag--nouveau">Nouveau</span>
                        </div>
                        <h3 class="presse-communique__titre">Pool Party lance la réservation instantanée pour l'été 2026</h3>
                        <p class="presse-communique__resume">Les locataires peuvent désormais réserver un créneau de baignade en quelques secondes, sans attendre la validation de l'hôte. La fonctionnalité couvre déjà plus de la moitié des annonces de la plateforme.</p>
                        <div class="presse-communique__actions">
                            <button type="button" class="btn-link js-communique-toggle" aria-expanded="false" aria-controls="communique-1">Lire le communiqué</button>
                            <a href="<?php echo esc_url($presse_dir . 'communique-2026-06-reservation-instantanee.pdf'); ?>" class="link-signal link-signal--simple" download>Télécharger le PDF</a>
                        </div>
                        <div class="presse-communique__complet" id="communique-1" hidden>
                            <p>À quelques jours de l'été, Pool Party déploie la réservation instantanée sur son catalogue d'espaces aquatiques et bien-être en Île-de-France. Jusqu'ici, chaque demande devait être confirmée manuellement par le propriétaire ; les hôtes qui le souhaitent peuvent maintenant ouvrir leurs créneaux à la réservation immédiate, exactement comme on réserve une table au restaurant.</p>
                            <p>Concrètement, le locataire choisit une date, un créneau horaire et le nombre de participants, puis confirme son paiement : la réservation est acquise sur-le-champ, avec un e-mail récapitulatif envoyé aux deux parties. Les hôtes gardent la main sur leur calendrier et peuvent, à tout moment, repasser une annonce en validation manuelle.</p>
                            <p>« Nous voulions retirer le principal point de friction du parcours : l'attente. Réserver une piscine près de chez soi doit être aussi simple qu'un achat en ligne », explique l'équipe produit de Pool Party. La réservation instantanée est active sur 54 % des annonces au lancement, une part appelée à croître tout au long de la saison.</p>
                        </div>
                    </article>
                </li>
                <li>
                    <article class="presse-communique">
                        <div class="presse-communique__entete">
                            <time class="presse-communique__date" datetime="2026-04-03">3 avril 2026</time>
                        </div>
                        <h3 class="presse-communique__titre">Le cap des 300 espaces référencés est franchi en Île-de-France</h3>
                        <p class="presse-communique__resume">Piscines, jacuzzis, spas, saunas et hammams : l'offre de la plateforme a triplé depuis le lancement, portée par l'arrivée de nouveaux hôtes en Seine-et-Marne et dans les Yvelines.</p>
                        <div class="presse-communique__actions">
                            <button type="button" class="btn-link js-communique-toggle" aria-expanded="false" aria-controls="communique-2">Lire le communiqué</button>
                            <a href="<?php echo esc_url($presse_dir . 'communique-2026-04-300-espaces.pdf'); ?>" class="link-signal link-signal--simple" download>Télécharger le PDF</a>
                        </div>
                        <div class="presse-communique__complet" id="communique-2" hidden>
                            <p>Dix-huit mois après ses débuts, Pool Party dépasse les 300 espaces disponibles à la réservation en Île-de-France. La plateforme, qui avait ouvert avec une centaine d'annonces concentrées en petite couronne, couvre désormais l'ensemble des départements franciliens.</p>
                            <p>La croissance est particulièrement marquée en grande couronne : la Seine-et-Marne et les Yvelines représentent à elles seules près d'un tiers des nouvelles annonces du trimestre. Les catégories bien-être — spas, saunas et hammams — connaissent la plus forte progression, signe d'un usage qui s'étend au-delà de la seule baignade estivale.</p>
                            <p>« Chaque nouvel hôte, c'est un jardin, une terrasse ou un espace détente qui trouve une seconde vie et complète les revenus de son propriétaire », souligne l'équipe de Pool Party. Pour accompagner cette montée en charge, la plateforme a renforcé son processus de vérification des annonces et son accompagnement des nouveaux hôtes.</p>
                        </div>
                    </article>
                </li>
                <li>
                    <article class="presse-communique">
                        <div class="presse-communique__entete">
                            <time class="presse-communique__date" datetime="2026-01-15">15 janvier 2026</time>
                        </div>
                        <h3 class="presse-communique__titre">Pool Party lève 1,5 million d'euros pour accélérer en région parisienne</h3>
                        <p class="presse-communique__resume">Ce premier tour de table financera le recrutement de l'équipe, le renforcement des garanties proposées aux hôtes et l'ouverture de nouvelles catégories d'espaces bien-être.</p>
                        <div class="presse-communique__actions">
                            <button type="button" class="btn-link js-communique-toggle" aria-expanded="false" aria-controls="communique-3">Lire le communiqué</button>
                            <a href="<?php echo esc_url($presse_dir . 'communique-2026-01-levee-de-fonds.pdf'); ?>" class="link-signal link-signal--simple" download>Télécharger le PDF</a>
                        </div>
                        <div class="presse-communique__complet" id="communique-3" hidden>
                            <p>Pool Party annonce la clôture d'un premier tour de financement d'amorçage de 1,5 million d'euros, mené auprès de business angels et d'un fonds spécialisé dans l'économie du partage. L'opération valide un modèle qui, en un an, a démontré l'appétit des Franciliens pour la location d'espaces aquatiques entre particuliers.</p>
                            <p>Les fonds seront alloués à trois priorités : le doublement de l'équipe — produit, opérations et relation hôtes —, le renforcement de la couverture assurantielle incluse dans chaque réservation, et l'élargissement du catalogue aux espaces bien-être (spas, saunas, hammams) déjà plébiscités par les utilisateurs.</p>
                            <p>« Cette levée nous donne les moyens de nos ambitions tout en gardant notre exigence : la confiance. Un paiement sécurisé, une assurance systématique et des hôtes vérifiés restent au cœur de l'expérience », indique la direction de Pool Party. La plateforme confirme son objectif de couvrir toute l'Île-de-France avant d'envisager d'autres métropoles.</p>
                        </div>
                    </article>
                </li>
                <li>
                    <article class="presse-communique">
                        <div class="presse-communique__entete">
                            <time class="presse-communique__date" datetime="2025-09-08">8 septembre 2025</time>
                        </div>
                        <h3 class="presse-communique__titre">Premier été réussi : 12 500 baignades réservées sur la plateforme</h3>
                        <p class="presse-communique__resume">Trois mois après son lancement, Pool Party dresse le bilan de sa première saison : un panier moyen de 95 euros, des hôtes qui complètent leurs revenus et des piscines qui ne dorment plus.</p>
                        <div class="presse-communique__actions">
                            <button type="button" class="btn-link js-communique-toggle" aria-expanded="false" aria-controls="communique-4">Lire le communiqué</button>
                            <a href="<?php echo esc_url($presse_dir . 'communique-2025-09-premier-ete.pdf'); ?>" class="link-signal link-signal--simple" download>Télécharger le PDF</a>
                        </div>
                        <div class="presse-communique__complet" id="communique-4" hidden>
                            <p>Lancée au début de l'été 2025, Pool Party clôt sa première saison avec 12 500 baignades réservées en Île-de-France. La plateforme, qui met en relation propriétaires de piscines et particuliers, confirme l'existence d'une véritable demande pour la location de créneaux à l'heure ou à la demi-journée.</p>
                            <p>Le panier moyen s'établit à 95 euros par réservation, pour des groupes de quatre à six personnes en moyenne. Côté hôtes, la plateforme observe qu'une piscine mise en location le week-end génère un complément de revenu significatif, tout en restant pleinement disponible pour son propriétaire le reste du temps.</p>
                            <p>« Ce premier été valide notre intuition de départ : il y a, à côté de chez soi, des espaces sous-utilisés et des gens qui rêvent d'un après-midi au bord de l'eau. Notre rôle est de créer la confiance qui rend la rencontre possible », résume l'équipe fondatrice. Forte de ce bilan, Pool Party aborde la rentrée avec l'ambition d'étoffer son offre bien-être pour prolonger la saison.</p>
                        </div>
                    </article>
                </li>
            </ul>
        </section>

        <!-- BLOC 5 : Retombées presse -->
        <section class="presse-retombees" aria-labelledby="presse-retombees-titre">
            <h2 id="presse-retombees-titre">Ils parlent de nous</h2>
            <ul class="presse-retombees__grille">
                <li>
                    <article class="presse-citation">
                        <blockquote class="presse-citation__texte">"Le bon coin de la baignade : Pool Party transforme les piscines privées d'Île-de-France en petits paradis à partager."</blockquote>
                        <footer>
                            <p class="presse-citation__media">Le Parisien</p>
                            <p class="presse-citation__date">Juin 2026</p>
                        </footer>
                    </article>
                </li>
                <li>
                    <article class="presse-citation">
                        <blockquote class="presse-citation__texte">"Une idée simple et un parcours de réservation soigné : la jeune pousse parisienne veut faire de la piscine partagée un réflexe estival."</blockquote>
                        <footer>
                            <p class="presse-citation__media">BFM Business</p>
                            <p class="presse-citation__date">Avril 2026</p>
                        </footer>
                    </article>
                </li>
                <li>
                    <article class="presse-citation">
                        <blockquote class="presse-citation__texte">"On a testé la location de piscine entre particuliers : verdict, on a passé le meilleur dimanche de l'été sans quitter la banlieue."</blockquote>
                        <footer>
                            <p class="presse-citation__media">Konbini</p>
                            <p class="presse-citation__date">Août 2025</p>
                        </footer>
                    </article>
                </li>
            </ul>
        </section>

        <!-- BLOC 6 : Kit presse -->
        <section class="presse-kit" id="kit-presse" aria-labelledby="presse-kit-titre">
            <h2 id="presse-kit-titre">Kit presse</h2>
            <p class="presse-kit__sous-titre">Logos, photos et charte graphique sont libres d'utilisation dans le cadre d'un article ou d'un reportage consacré à Pool Party.</p>
            <ul class="presse-kit__grille">
                <li>
                    <article class="presse-ressource">
                        <div class="presse-ressource__visuel presse-ressource__visuel--logo">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo/logo-full.png'); ?>" alt="Logo Pool Party sur fond beige">
                        </div>
                        <h3 class="presse-ressource__titre">Logos</h3>
                        <p class="presse-ressource__texte">Le logo Pool Party en versions couleur et blanche, formats PNG et SVG.</p>
                        <a href="<?php echo esc_url($presse_dir . 'poolparty-logos.zip'); ?>" class="link-signal" download>Télécharger les logos</a>
                    </article>
                </li>
                <li>
                    <article class="presse-ressource">
                        <div class="presse-ressource__visuel">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-croissy.jpg'); ?>" alt="Piscine privée référencée sur Pool Party, entourée d'un jardin">
                        </div>
                        <h3 class="presse-ressource__titre">Photos officielles</h3>
                        <p class="presse-ressource__texte">Une sélection de photos haute définition d'espaces référencés sur la plateforme.</p>
                        <a href="<?php echo esc_url($presse_dir . 'poolparty-photos.zip'); ?>" class="link-signal" download>Télécharger les photos</a>
                    </article>
                </li>
                <li>
                    <article class="presse-ressource">
                        <div class="presse-ressource__visuel presse-ressource__visuel--charte">
                            <span class="presse-ressource__pastille presse-ressource__pastille--principal" aria-hidden="true"></span>
                            <span class="presse-ressource__pastille presse-ressource__pastille--vert" aria-hidden="true"></span>
                            <span class="presse-ressource__pastille presse-ressource__pastille--corail" aria-hidden="true"></span>
                            <span class="presse-ressource__pastille presse-ressource__pastille--sable" aria-hidden="true"></span>
                        </div>
                        <h3 class="presse-ressource__titre">Charte graphique</h3>
                        <p class="presse-ressource__texte">Couleurs, typographies et règles d'utilisation de la marque Pool Party.</p>
                        <a href="<?php echo esc_url($presse_dir . 'poolparty-charte-graphique.pdf'); ?>" class="link-signal" download>Télécharger la charte</a>
                    </article>
                </li>
                <li>
                    <article class="presse-ressource">
                        <div class="presse-ressource__visuel">
                            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/hero/hero.jpg'); ?>" alt="Baignade dans une piscine louée avec Pool Party">
                        </div>
                        <h3 class="presse-ressource__titre">Dossier de presse</h3>
                        <p class="presse-ressource__texte">L'histoire de Pool Party, son modèle, son équipe et ses chiffres clés en un document.</p>
                        <a href="<?php echo esc_url($presse_dir . 'poolparty-dossier-presse.pdf'); ?>" class="link-signal" download>Télécharger le dossier</a>
                    </article>
                </li>
            </ul>
        </section>

        <!-- BLOC 7 : Contact presse -->
        <section class="presse-contact" id="contact-presse" aria-labelledby="presse-contact-titre">
            <div class="presse-contact__carte">
                <h2 id="presse-contact-titre">Contact presse</h2>
                <p class="presse-contact__nom">Camille Renard</p>
                <p class="presse-contact__role">Responsable communication et relations médias</p>
                <ul class="presse-contact__coordonnees">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                        <a href="mailto:presse@poolparty.fr">presse@poolparty.fr</a>
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>
                        <a href="tel:+33645890127">+33 6 45 89 01 27</a>
                    </li>
                </ul>
                <p class="presse-contact__delai">Réponse garantie sous 24 heures ouvrées pour toute demande d'interview ou de visuel.</p>
            </div>
        </section>
    </main>

<?php get_footer(); ?>
