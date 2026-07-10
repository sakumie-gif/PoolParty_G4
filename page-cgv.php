<?php
/**
 * Gabarit de la page « Conditions générales de vente » (contenu repris de cgv.html).
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
                    <li><span class="is-current" aria-current="page">Conditions générales de vente</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="legal-hero">
            <h1>Conditions générales de vente</h1>
            <p>Les présentes conditions s'appliquent à toute transaction financière réalisée via la plateforme Pool Party. Elles constituent, conformément aux articles L221-1 et suivants et L441-1 du Code de la consommation, le socle unique de la relation commerciale entre Pool Party, les Hôtes et les Locataires. Toute réservation ou mise en location payante vaut acceptation sans réserve des présentes CGV.</p>
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
                    <li><a href="#article-1">Identification du prestataire intermédiaire</a></li>
                    <li><a href="#article-2">Nature et description des services</a></li>
                    <li><a href="#article-3">Prix et modalités de paiement</a></li>
                    <li><a href="#article-4">Formation du contrat de location</a></li>
                    <li><a href="#article-5">Droit de rétractation</a></li>
                    <li><a href="#article-6">Annulation et remboursement</a></li>
                    <li><a href="#article-7">Dépôt de garantie et empreinte bancaire</a></li>
                    <li><a href="#article-8">Obligations de l'Hôte prestataire</a></li>
                    <li><a href="#article-9">Garanties légales</a></li>
                    <li><a href="#article-10">Commission Pool Party et fiscalité des Hôtes</a></li>
                    <li><a href="#article-11">Litiges et médiation</a></li>
                    <li><a href="#article-12">Droit applicable</a></li>
                </ol>
            </nav>

            <section id="article-1" aria-labelledby="article-1-titre">
                <h2 id="article-1-titre">Article 1 : Identification du prestataire intermédiaire</h2>
                <p>Pool Party SARL, 12 rue de Rivoli, 75001 Paris, France. Directrice de la publication : Mme Francine. Email : <a href="mailto:contact@poolparty.fr">contact@poolparty.fr</a>.</p>
                <p>Pool Party agit en qualité d'intermédiaire entre Hôtes et Locataires. La vente effective du service de location est conclue directement entre les deux parties. Pool Party perçoit une commission de mise en relation et de gestion du paiement sécurisé.</p>
            </section>

            <section id="article-2" aria-labelledby="article-2-titre">
                <h2 id="article-2-titre">Article 2 : Nature et description des services</h2>
                <p>Les services proposés via Pool Party sont des locations de courte durée d'espaces aquatiques et de bien-être privés (piscines, jacuzzis, spas, saunas, hammams) mis à disposition par des particuliers sur le territoire français.</p>
                <p>Chaque annonce précise : description complète, équipements disponibles, capacité d'accueil maximale, horaires d'accès, règles de la maison, tarification et politique d'annulation.</p>
            </section>

            <section id="article-3" aria-labelledby="article-3-titre">
                <h2 id="article-3-titre">Article 3 : Prix et modalités de paiement</h2>
                <p>Les prix affichés sont en euros TTC. Ils incluent le tarif fixé par l'Hôte et la commission de Pool Party, clairement détaillée avant validation. Le montant total est affiché de façon transparente à l'étape de récapitulatif, sans frais cachés (Code de la consommation, art. L111-1).</p>
                <p>Le paiement s'effectue exclusivement en ligne, par carte bancaire ou PayPal, via Stripe, certifié PCI-DSS niveau 1, avec authentification forte (3D Secure 2 / SCA) conforme à la directive européenne DSP2. Les données bancaires ne sont jamais stockées par Pool Party.</p>
            </section>

            <section id="article-4" aria-labelledby="article-4-titre">
                <h2 id="article-4-titre">Article 4 : Formation du contrat de location</h2>
                <p>Le processus de réservation suit les étapes suivantes :</p>
                <ul class="legal-liste">
                    <li><strong>Étape 1</strong> : sélection de l'espace, de la date, du créneau et du nombre de participants.</li>
                    <li><strong>Étape 2</strong> : affichage du récapitulatif complet (description, prix détaillé, politique d'annulation).</li>
                    <li><strong>Étape 3</strong> : enregistrement du paiement sécurisé via Stripe (empreinte bancaire, sans débit immédiat).</li>
                    <li><strong>Étape 4</strong> : notification à l'Hôte, qui dispose d'un délai de 24 heures pour confirmer ou refuser.</li>
                    <li><strong>Étape 5</strong> : débit du paiement et envoi de l'email de confirmation au Locataire, formant le contrat de location.</li>
                </ul>
                <p>En cas de refus ou d'absence de réponse dans le délai imparti, aucun débit n'est effectué et l'empreinte bancaire est libérée sans frais.</p>
            </section>

            <section id="article-5" aria-labelledby="article-5-titre">
                <h2 id="article-5-titre">Article 5 : Droit de rétractation</h2>
                <p>Conformément à l'article L221-28 du Code de la consommation, le droit de rétractation de 14 jours applicable aux contrats à distance ne s'applique pas aux prestations de loisirs prévues à une date ou une période spécifique. Les conditions d'annulation de l'Hôte, acceptées lors de la réservation, constituent le seul dispositif applicable.</p>
            </section>

            <section id="article-6" aria-labelledby="article-6-titre">
                <h2 id="article-6-titre">Article 6 : Annulation et remboursement</h2>
                <p>Les modalités d'annulation sont celles définies à l'article 6 des <a href="<?php echo esc_url(home_url('/cgu/#article-6')); ?>">conditions générales d'utilisation</a> (politiques souple, modérée ou stricte choisies par l'Hôte) et rappelées sur la page de confirmation avant paiement.</p>
                <p>En cas de force majeure dûment constatée, le remboursement intégral est accordé sans pénalité, conformément à l'article 1218 du Code civil. Pool Party traite toute réclamation dans un délai de 5 jours ouvrés.</p>
            </section>

            <section id="article-7" aria-labelledby="article-7-titre">
                <h2 id="article-7-titre">Article 7 : Dépôt de garantie et empreinte bancaire</h2>
                <p>Lors de la confirmation de la réservation, une autorisation préalable de carte bancaire d'un montant de 200 euros est réalisée par Stripe à titre de dépôt de garantie. Cette somme n'est pas débitée et reste bloquée temporairement sur le compte du Locataire. Le dépôt de garantie est libéré automatiquement dans un délai de 7 jours calendaires après la fin du créneau réservé, sauf signalement de dommage par l'Hôte dans les 48 heures suivant la fin de la location.</p>
                <p>En cas de signalement de dommage, la procédure de litige prévue à l'article 11 des présentes CGV est déclenchée. Pool Party intervient comme médiateur entre les parties et peut, après examen contradictoire des éléments fournis (photos horodatées avant et après location, témoignages, devis de réparation), débiter tout ou partie du dépôt de garantie au profit de l'Hôte. Le Locataire conserve la possibilité de contester la retenue par voie de médiation (article 11) ou de recours juridictionnel (article 12).</p>
                <p>Aucun prélèvement supérieur au montant du dépôt ne peut être effectué sans accord exprès du Locataire. Le montant du dépôt de garantie peut être réévalué par l'Hôte pour les espaces premium ou les locations de longue durée, dans la limite de 500 euros, et doit être affiché de manière transparente sur l'annonce.</p>
            </section>

            <section id="article-8" aria-labelledby="article-8-titre">
                <h2 id="article-8-titre">Article 8 : Obligations de l'Hôte prestataire</h2>
                <p>L'Hôte s'engage à fournir le service tel que décrit dans son annonce. Il doit :</p>
                <ul class="legal-liste">
                    <li>Garantir que l'espace est conforme aux normes de sécurité réglementaires (décret n° 2004-499).</li>
                    <li>Disposer d'une assurance responsabilité civile couvrant les locations à des tiers.</li>
                    <li>Accueillir le Locataire dans des conditions d'hygiène satisfaisantes.</li>
                    <li>Signaler tout incident ou dysfonctionnement dans les meilleurs délais à Pool Party.</li>
                </ul>
                <p>Le manquement répété à ces obligations peut entraîner la suspension du compte, sans préjudice des voies de recours du Locataire.</p>
            </section>

            <section id="article-9" aria-labelledby="article-9-titre">
                <h2 id="article-9-titre">Article 9 : Garanties légales</h2>
                <p>Le Locataire bénéficie de la garantie légale de conformité (Code de la consommation, art. L217-1 et suivants) et de la garantie contre les vices cachés (Code civil, art. 1641 et suivants) en cas de non-conformité manifeste du service rendu par rapport à la description de l'annonce.</p>
            </section>

            <section id="article-10" aria-labelledby="article-10-titre">
                <h2 id="article-10-titre">Article 10 : Commission Pool Party et fiscalité des Hôtes</h2>
                <p>Pool Party perçoit une commission de service sur chaque transaction, déduite automatiquement du montant reversé à l'Hôte. Son taux est affiché sur la page de création d'annonce.</p>
                <p>Les revenus perçus par les Hôtes sont soumis à la réglementation fiscale française. Pool Party adresse chaque année un récapitulatif des sommes perçues, conformément à l'article 242 bis du Code général des impôts.</p>
            </section>

            <section id="article-11" aria-labelledby="article-11-titre">
                <h2 id="article-11-titre">Article 11 : Litiges et médiation</h2>
                <p>En cas de litige non résolu amiablement dans un délai de 30 jours, le Locataire consommateur peut recourir gratuitement à un médiateur de la consommation (Code de la consommation, art. L612-1). La Commission européenne met à disposition une plateforme de règlement en ligne des litiges (RLL) à l'adresse <a href="https://ec.europa.eu/consumers/odr" rel="external noopener">ec.europa.eu/consumers/odr</a>.</p>
            </section>

            <section id="article-12" aria-labelledby="article-12-titre">
                <h2 id="article-12-titre">Article 12 : Droit applicable</h2>
                <p>Les présentes CGV sont régies par le droit français. Tout litige non résolu amiablement sera porté devant les juridictions compétentes du ressort du Tribunal de Paris, sous réserve des règles protectrices applicables aux consommateurs.</p>
            </section>

        </div>
    </main>

<?php get_footer(); ?>
