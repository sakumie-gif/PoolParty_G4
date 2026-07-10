<?php
/**
 * Gabarit de la page « Accessibilité » (contenu repris de accessibilite.html).
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 */
get_header();
?>

    <main id="contenu">

        <!-- BLOC 1 : Fil d'Ariane -->
        <div class="access-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><span class="is-current" aria-current="page">Accessibilité</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="access-hero">
            <h1>Un site pensé pour tout le monde</h1>
            <p>Réserver un moment de détente doit rester simple, quels que soient votre matériel, votre navigateur ou votre situation de handicap. Voici nos engagements, l'état d'avancement du site et les moyens de nous alerter en cas de difficulté.</p>
        </section>

        <!-- BLOC 3 : Nos engagements (quatre piliers) -->
        <section class="access-piliers" aria-labelledby="access-piliers-titre">
            <h2 id="access-piliers-titre">Nos engagements au quotidien</h2>
            <p class="access-piliers__sub">Quatre principes guident la conception de chaque page du site.</p>

            <ul class="access-piliers__liste">
                <li class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="6" width="20" height="12" rx="2"/><path d="M6 10h.01M10 10h.01M14 10h.01M18 10h.01M6 14h.01M18 14h.01M9 14h6"/></svg>
                    <h3 class="card-protection__title">Navigation au clavier</h3>
                    <p class="card-protection__text">Menus, formulaires, carrousels et fenêtres se pilotent sans souris, avec les touches Tab, Entrée et Échap.</p>
                </li>
                <li class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 0 20z" fill="currentColor" stroke="none"/></svg>
                    <h3 class="card-protection__title">Contrastes vérifiés</h3>
                    <p class="card-protection__text">Les couleurs de la charte sont contrôlées avec WebAIM et Stark pour garantir une lecture confortable des textes.</p>
                </li>
                <li class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4.702a.705.705 0 0 0-1.203-.498L6.413 7.587A1.4 1.4 0 0 1 5.416 8H3a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h2.416a1.4 1.4 0 0 1 .997.413l3.383 3.384A.705.705 0 0 0 11 19.298z"/><path d="M16 9a5 5 0 0 1 0 6"/><path d="M19.364 18.364a9 9 0 0 0 0-12.728"/></svg>
                    <h3 class="card-protection__title">Lecteurs d'écran</h3>
                    <p class="card-protection__text">Balises sémantiques, attributs ARIA et hiérarchie de titres rigoureuse guident les technologies d'assistance.</p>
                </li>
                <li class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    <h3 class="card-protection__title">Alternatives textuelles</h3>
                    <p class="card-protection__text">Chaque image utile porte une description ; les visuels décoratifs sont masqués pour ne pas gêner l'écoute.</p>
                </li>
            </ul>
        </section>

        <!-- BLOC 4 : Déclaration d'accessibilité -->
        <section class="access-declaration" aria-labelledby="access-declaration-titre">
            <h2 id="access-declaration-titre">Déclaration d'accessibilité</h2>
            <p>Pool Party s'engage à rendre son site internet accessible conformément à l'article 47 de la loi n° 2005-102 du 11 février 2005. Cette déclaration s'applique au site <strong>poolparty-dsp-ddm-f25a-o24b-g4.fr</strong>.</p>

            <!-- État de conformité : taux illustré par la barre de progression -->
            <div class="access-conformite">
                <h3>État de conformité</h3>
                <p>Le site Pool Party est <strong>partiellement conforme</strong> au RGAA version 4.1 (Référentiel général d'amélioration de l'accessibilité), en raison des non-conformités listées ci-dessous.</p>
                <div class="access-conformite__score">
                    <p class="access-conformite__valeur">82 % des critères RGAA respectés</p>
                    <div class="progress" data-value="82" role="progressbar" aria-valuenow="82" aria-valuemin="0" aria-valuemax="100" aria-label="Taux de conformité RGAA : 82 pour cent">
                        <span class="progress__bar"></span>
                    </div>
                </div>
            </div>

            <h3>Résultats des tests</h3>
            <p>L'audit de conformité a été réalisé en interne en mai 2026 sur un échantillon de pages représentatives : accueil, liste des annonces, fiche annonce, contact et parcours de dépôt d'annonce. Les vérifications ont combiné les outils WAVE et axe DevTools, un parcours complet au clavier et une lecture avec NVDA sous Windows.</p>

            <h3>Contenus non accessibles</h3>
            <p>Les éléments suivants ne sont pas encore conformes et font l'objet d'un plan de correction :</p>
            <ul class="access-liste">
                <li>La carte interactive de la page de recherche ne propose pas encore d'alternative sous forme de liste pour les lecteurs d'écran.</li>
                <li>Certaines photos d'annonces déposées par les hôtes arrivent sans description ; un contrôle à la publication est en cours de mise en place.</li>
                <li>Le calendrier de réservation reste difficile à parcourir au clavier sur certaines combinaisons navigateur et lecteur d'écran.</li>
            </ul>

            <h3>Technologies utilisées</h3>
            <p>Le site s'appuie sur HTML5, CSS3 et JavaScript, sans dépendance à un cadriciel externe. Il a été testé avec les navigateurs Firefox, Chrome, Edge et Safari, sur ordinateur et sur mobile.</p>

            <p class="access-declaration__date">Déclaration établie le 18 mai 2026, mise à jour le 4 juillet 2026.</p>
        </section>

        <!-- BLOC 5 : Aides à la navigation -->
        <section class="access-aides" aria-labelledby="access-aides-titre">
            <h2 id="access-aides-titre">Adapter le site à vos besoins</h2>
            <p class="access-aides__sub">Quelques réglages simples, disponibles dans tous les navigateurs, améliorent le confort de lecture.</p>

            <ul class="access-aides__liste">
                <li class="access-aide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                    <div>
                        <h3 class="access-aide__titre">Agrandir le texte</h3>
                        <p>Maintenez la touche Ctrl (Cmd sur Mac) et appuyez sur + pour zoomer, sur - pour dézoomer, sur 0 pour revenir à la taille d'origine. La mise en page s'adapte jusqu'à un zoom de 200 %.</p>
                    </div>
                </li>
                <li class="access-aide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h16M4 12h10M4 18h7"/></svg>
                    <div>
                        <h3 class="access-aide__titre">Naviguer par titres</h3>
                        <p>Chaque page suit une hiérarchie de titres stricte : avec un lecteur d'écran, la touche H permet de sauter de section en section sans tout écouter.</p>
                    </div>
                </li>
                <li class="access-aide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    <div>
                        <h3 class="access-aide__titre">Se repérer avec le fil d'Ariane</h3>
                        <p>Présent en haut de chaque page intérieure, il indique où vous vous trouvez et permet de remonter d'un niveau en un clic.</p>
                    </div>
                </li>
                <li class="access-aide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                    <div>
                        <h3 class="access-aide__titre">Réduire les animations</h3>
                        <p>Le site respecte le réglage "Réduire les animations" de votre système : les transitions décoratives sont alors désactivées.</p>
                    </div>
                </li>
            </ul>
        </section>

        <!-- BLOC 6 : Signalement et voies de recours -->
        <section class="access-contact" aria-labelledby="access-contact-titre">
            <div class="access-contact__cta">
                <h2 id="access-contact-titre">Une difficulté sur le site ?</h2>
                <p>Si vous n'arrivez pas à accéder à un contenu ou à un service, écrivez-nous. Nous vous répondons sous 5 jours ouvrés avec une solution ou une alternative accessible.</p>
                <div class="access-contact__actions">
                    <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-secondary btn-medium">Nous écrire</a>
                    <a href="mailto:accessibilite@poolparty.fr" class="btn btn-tertiary btn-medium">accessibilite@poolparty.fr</a>
                </div>
            </div>

            <div class="access-recours">
                <h3>Voies de recours</h3>
                <p>Si vous avez signalé un défaut d'accessibilité et que vous n'avez pas obtenu de réponse satisfaisante, vous pouvez :</p>
                <ul class="access-liste">
                    <li>écrire un message au <a href="https://formulaire.defenseurdesdroits.fr" rel="external">Défenseur des droits</a> ;</li>
                    <li>contacter le délégué du Défenseur des droits de votre région ;</li>
                    <li>envoyer un courrier par la poste (gratuit, sans affranchissement) : Défenseur des droits, Libre réponse 71120, 75342 Paris CEDEX 07.</li>
                </ul>
            </div>
        </section>

    </main>

<?php get_footer(); ?>
