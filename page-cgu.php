<?php
/**
 * Gabarit de la page « Conditions générales d'utilisation » (contenu repris de cgu.html).
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
                    <li><span class="is-current" aria-current="page">Conditions générales d'utilisation</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="legal-hero">
            <h1>Conditions générales d'utilisation</h1>
            <p>Les présentes conditions définissent les règles d'accès et d'usage de la plateforme Pool Party pour l'ensemble des visiteurs et utilisateurs inscrits. La validation d'un compte ou la réalisation d'une réservation vaut acceptation pleine et entière des présentes CGU (Code de la consommation, art. L221-11).</p>
            <p class="legal-hero__date">Dernière mise à jour : 4 juillet 2026</p>
        </section>

        <!-- BLOC 3 : Corps du document -->
        <div class="legal-layout legal-content">

            <!-- Rappel du cadre pédagogique -->
            <aside class="legal-notice" aria-label="Avertissement : projet pédagogique fictif">
                <p><strong>Document fictif.</strong> Ces conditions générales sont rédigées dans le cadre d'un projet pédagogique fictif et n'ont aucune valeur contractuelle réelle. Voir les <a href="<?php echo esc_url(home_url('/mentions-legales/')); ?>">mentions légales</a> pour le détail.</p>
            </aside>

            <!-- Sommaire -->
            <nav class="legal-sommaire" aria-labelledby="sommaire-titre">
                <h2 id="sommaire-titre">Sommaire</h2>
                <ol>
                    <li><a href="#article-1">Objet et champ d'application</a></li>
                    <li><a href="#article-2">Accès à la plateforme</a></li>
                    <li><a href="#article-3">Profils utilisateurs et rôles</a></li>
                    <li><a href="#article-4">Publication d'annonces</a></li>
                    <li><a href="#article-5">Réservations et paiement</a></li>
                    <li><a href="#article-6">Annulations et remboursements</a></li>
                    <li><a href="#article-7">Responsabilités des parties</a></li>
                    <li><a href="#article-8">Avis et modération</a></li>
                    <li><a href="#article-9">Données personnelles</a></li>
                    <li><a href="#article-10">Cookies</a></li>
                    <li><a href="#article-11">Propriété intellectuelle</a></li>
                    <li><a href="#article-12">Droit applicable et juridiction compétente</a></li>
                </ol>
            </nav>

            <section id="article-1" aria-labelledby="article-1-titre">
                <h2 id="article-1-titre">Article 1 : Objet et champ d'application</h2>
                <p>La plateforme Pool Party est une place de marché numérique permettant la mise en relation de particuliers souhaitant louer leur piscine, jacuzzi, spa, sauna ou hammam privé (ci-après désignés Hôtes) avec des particuliers souhaitant bénéficier de ces espaces (ci-après désignés Locataires).</p>
                <p>Pool Party n'est pas partie aux contrats conclus entre Hôtes et Locataires : la plateforme agit exclusivement en qualité d'intermédiaire de mise en relation et de prestataire de service de paiement sécurisé.</p>
                <p>Les présentes CGU peuvent être modifiées à tout moment par Pool Party. Les utilisateurs en seront informés par email ou via une notification dans leur espace personnel, avec un préavis minimum de 15 jours.</p>
            </section>

            <section id="article-2" aria-labelledby="article-2-titre">
                <h2 id="article-2-titre">Article 2 : Accès à la plateforme</h2>
                <p>L'accès à Pool Party est gratuit pour la consultation. La création d'un compte est nécessaire pour effectuer une réservation ou publier une annonce. L'utilisateur doit être une personne physique majeure (18 ans minimum) ou une personne morale dûment habilitée. La plateforme est accessible 24h/24 et 7j/7, sous réserve d'opérations de maintenance ou d'incidents techniques.</p>
                <p>Chaque utilisateur est responsable de la confidentialité de ses identifiants. Tout accès depuis un compte constitue une action engageant la responsabilité de son titulaire.</p>
            </section>

            <section id="article-3" aria-labelledby="article-3-titre">
                <h2 id="article-3-titre">Article 3 : Profils utilisateurs et rôles</h2>
                <p>La plateforme distingue trois types de profils, gérés au sein d'un compte unique :</p>
                <ul class="legal-liste">
                    <li><strong>Visiteur</strong> : utilisateur non connecté, pouvant consulter les annonces et les pages d'information.</li>
                    <li><strong>Locataire</strong> : utilisateur inscrit souhaitant réserver un espace aquatique. Il dispose d'un espace personnel donnant accès à ses réservations, ses avis et son historique.</li>
                    <li><strong>Hôte (Propriétaire)</strong> : utilisateur inscrit mettant en location un ou plusieurs espaces. Il dispose d'un tableau de bord dédié pour gérer ses annonces, son calendrier et les réservations reçues.</li>
                </ul>
                <p>Un même utilisateur peut cumuler les rôles de Locataire et d'Hôte au sein d'un compte unique, via un sélecteur de vue permettant de basculer d'un espace à l'autre.</p>
            </section>

            <section id="article-4" aria-labelledby="article-4-titre">
                <h2 id="article-4-titre">Article 4 : Publication d'annonces</h2>
                <p>Tout utilisateur ayant activé le profil Hôte peut publier une annonce de mise en location. L'annonce doit être exacte, complète et sincère. L'Hôte certifie être le propriétaire ou l'ayant droit du bien mis en location, et garantit que cet espace est conforme aux normes de sécurité applicables aux piscines privées accessibles à des tiers (DGCCRF, 2025).</p>
                <p>Pool Party se réserve le droit de refuser, suspendre ou supprimer toute annonce ne respectant pas les présentes CGU, les lois en vigueur ou les valeurs de la plateforme. Sont notamment interdits les espaces ne disposant pas d'un accès séparé du domicile privé et ceux dont le dispositif de sécurité réglementaire fait défaut : barrière, alarme, couverture ou abri conforme au décret n° 2004-499.</p>
            </section>

            <section id="article-5" aria-labelledby="article-5-titre">
                <h2 id="article-5-titre">Article 5 : Réservations et paiement</h2>
                <p>La réservation devient effective après confirmation de l'Hôte et encaissement du paiement. Le paiement en ligne est géré par Stripe, prestataire certifié PCI-DSS et conforme à la directive européenne DSP2, qui impose une authentification forte (SCA) pour toute transaction en ligne.</p>
                <p>Pool Party perçoit une commission de service sur chaque transaction, clairement affichée avant validation. Les fonds sont reversés à l'Hôte dans un délai convenu après la fin de la location. Aucun paiement hors plateforme n'est autorisé entre les utilisateurs.</p>
            </section>

            <section id="article-6" aria-labelledby="article-6-titre">
                <h2 id="article-6-titre">Article 6 : Annulations et remboursements</h2>
                <p>Pool Party propose trois politiques d'annulation types, choisies par l'Hôte au moment de la création de son annonce :</p>
                <ul class="legal-liste">
                    <li><strong>Souple</strong> : remboursement intégral si l'annulation intervient plus de 48 heures avant la date de début.</li>
                    <li><strong>Modérée</strong> : remboursement à 50 % si l'annulation intervient plus de 7 jours avant la date de début ; aucun remboursement en deçà.</li>
                    <li><strong>Stricte</strong> : remboursement à 25 % si l'annulation intervient plus de 14 jours avant ; aucun remboursement en deçà.</li>
                </ul>
                <p>En cas de circonstances exceptionnelles indépendantes de la volonté des parties (arrêté préfectoral de restriction d'eau type VigiEau, catastrophe naturelle), Pool Party peut appliquer une politique d'annulation dérogatoire avec remboursement intégral, sans pénalité pour l'Hôte.</p>
            </section>

            <section id="article-7" aria-labelledby="article-7-titre">
                <h2 id="article-7-titre">Article 7 : Responsabilités des parties</h2>
                <p>L'Hôte est seul responsable de l'état et de la sécurité de l'espace mis en location. Il doit disposer d'une assurance responsabilité civile couvrant les dommages pouvant survenir lors d'une location à des tiers (Code des assurances, art. L124-3).</p>
                <p>Le Locataire s'engage à utiliser l'espace conformément à sa destination, à respecter les règles fixées par l'Hôte et à restituer les lieux dans l'état dans lequel il les a trouvés. Tout dommage causé lors de la location relève de sa responsabilité civile.</p>
                <p>Pool Party, en qualité d'intermédiaire, ne peut être tenue responsable des dommages survenant lors de l'utilisation d'une piscine, ni des litiges entre Hôtes et Locataires, sauf faute prouvée de la plateforme dans l'exécution de ses obligations.</p>
            </section>

            <section id="article-8" aria-labelledby="article-8-titre">
                <h2 id="article-8-titre">Article 8 : Avis et modération</h2>
                <p>À l'issue de chaque location, le Locataire est invité à laisser un avis sur l'annonce. Les avis doivent être sincères, objectifs et rédigés de bonne foi. Pool Party se réserve le droit de supprimer tout avis diffamatoire, injurieux ou manifestement frauduleux, conformément à la loi du 29 juillet 1881 sur la liberté de la presse (art. 29).</p>
                <p>L'équipe de modération dispose d'un délai de 48 heures ouvrées pour traiter les signalements. Tout utilisateur dont le comportement serait contraire aux présentes CGU pourra voir son compte suspendu ou supprimé.</p>
            </section>

            <section id="article-9" aria-labelledby="article-9-titre">
                <h2 id="article-9-titre">Article 9 : Données personnelles</h2>
                <p>Pool Party collecte et traite les données personnelles des utilisateurs dans le respect du RGPD (UE 2016/679) et de la loi Informatique et Libertés. Les données ne sont jamais revendues à des tiers.</p>
                <p>La durée de conservation, la liste complète de vos droits (accès, rectification, effacement, portabilité, opposition, retrait du consentement), les modalités d'exercice par email à <a href="mailto:contact@poolparty.fr">contact@poolparty.fr</a> et les voies de recours auprès de la CNIL sont détaillés dans la <a href="<?php echo esc_url(home_url('/mentions-legales/#donnees')); ?>">politique de confidentialité, section Données personnelles</a>.</p>
            </section>

            <section id="article-10" aria-labelledby="article-10-titre">
                <h2 id="article-10-titre">Article 10 : Cookies</h2>
                <p>Le site Pool Party utilise des cookies pour améliorer la navigation, mesurer l'audience (Google Analytics 4) et mémoriser les préférences utilisateur. Conformément aux recommandations consolidées de la CNIL, un bandeau de gestion des cookies est affiché dès la première visite, permettant à l'utilisateur de choisir librement les catégories de cookies acceptées.</p>
                <p>Les cookies strictement nécessaires (session, authentification) ne nécessitent pas de consentement préalable. Le détail des catégories figure dans les <a href="<?php echo esc_url(home_url('/mentions-legales/#cookies')); ?>">mentions légales</a>.</p>
            </section>

            <section id="article-11" aria-labelledby="article-11-titre">
                <h2 id="article-11-titre">Article 11 : Propriété intellectuelle</h2>
                <p>La plateforme Pool Party, son code source, sa charte graphique, ses textes et logotypes sont protégés par le droit de la propriété intellectuelle. Les contenus déposés par les utilisateurs (photos, textes d'annonce) restent leur propriété. En les publiant sur la plateforme, l'utilisateur concède à Pool Party une licence non exclusive et gratuite d'utilisation pour la durée de l'annonce.</p>
            </section>

            <section id="article-12" aria-labelledby="article-12-titre">
                <h2 id="article-12-titre">Article 12 : Droit applicable et juridiction compétente</h2>
                <p>Les présentes CGU sont soumises au droit français. En cas de litige non résolu amiablement dans un délai de 30 jours, celui-ci sera porté devant les juridictions compétentes du ressort du Tribunal de Paris. Les utilisateurs consommateurs peuvent recourir gratuitement à un médiateur de la consommation (Code de la consommation, art. L612-1).</p>
            </section>

        </div>
    </main>

<?php get_footer(); ?>
