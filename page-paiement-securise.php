<?php
/**
 * Gabarit de la page « Paiement sécurisé » (contenu repris de paiement-securise.html).
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 */
get_header();
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane -->
        <div class="paiement-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><span class="is-current" aria-current="page">Paiement sécurisé</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="paiement-hero">
            <h1>Paiement sécurisé</h1>
            <p>Chaque réservation se règle directement sur Pool Party, via une connexion chiffrée. Votre argent est conservé jusqu'au lendemain de votre créneau : voici comment tout fonctionne.</p>
        </section>

        <!-- BLOC 3 : Bande de garanties (mêmes items que la bande réassurance du site) -->
        <section class="paiement-garanties" aria-label="Les garanties de paiement">
            <ul class="reassurance-list">
                <li class="reassurance-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Connexion chiffrée SSL/TLS
                </li>
                <li class="reassurance-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                    Authentification 3D Secure
                </li>
                <li class="reassurance-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    Transactions opérées par Stripe
                </li>
            </ul>
        </section>

        <!-- BLOC 4 : Le parcours de votre argent, 3 étapes numérotées -->
        <section class="paiement-etapes" aria-labelledby="etapes-titre">
            <h2 id="etapes-titre">Votre argent, étape par étape</h2>
            <p class="paiement-etapes__sub">Du règlement au versement de l'hôte, rien ne se fait sans garde-fou.</p>

            <ol class="paiement-etapes__liste">
                <li class="etape-card">
                    <span class="etape-card__numero" aria-hidden="true">1</span>
                    <h3 class="etape-card__titre">Vous réglez sur Pool Party</h3>
                    <p class="etape-card__texte">Une empreinte bancaire est enregistrée dès votre demande, par carte bancaire ou PayPal, en une fois ou en trois fois sans frais. Vous n'êtes débité qu'une fois l'hôte a confirmé, sous 24 heures. Le prix affiché est le prix final, commission comprise.</p>
                </li>
                <li class="etape-card">
                    <span class="etape-card__numero" aria-hidden="true">2</span>
                    <h3 class="etape-card__titre">Nous conservons les fonds</h3>
                    <p class="etape-card__texte">Le montant reste bloqué sur un compte de cantonnement Stripe pendant toute la durée de la réservation. L'hôte ne peut pas y toucher avant votre venue.</p>
                </li>
                <li class="etape-card">
                    <span class="etape-card__numero" aria-hidden="true">3</span>
                    <h3 class="etape-card__titre">L'hôte est payé après votre créneau</h3>
                    <p class="etape-card__texte">Le versement part 24 heures après la fin de votre créneau. En cas de souci sur place, signalez-le dans ce délai : les fonds restent bloqués le temps de trouver une solution.</p>
                </li>
            </ol>
        </section>

        <!-- BLOC 5 : Renvoi vers les moyens de paiement (détaillés sur leur page dédiée) -->
        <section class="paiement-moyens" aria-labelledby="moyens-titre">
            <h2 id="moyens-titre">Carte, PayPal ou paiement en 3 fois</h2>
            <p class="paiement-renvoi__texte">Vous réglez par carte bancaire, PayPal ou en trois fois sans frais, toujours sur Pool Party. Le détail des moyens acceptés et de l'échéancier de débit est réuni sur une page dédiée.</p>
            <p class="paiement-lien"><a href="<?php echo esc_url(home_url('/moyen-de-paiement/')); ?>" class="btn-link">Voir les moyens de paiement</a></p>
        </section>

        <!-- BLOC 6 : Grille des protections (composant Card Protection de global.css) -->
        <section class="paiement-protections" aria-labelledby="protections-titre">
            <h2 id="protections-titre">Ce qui protège chaque transaction</h2>

            <div class="paiement-protections__grille">
                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <h3 class="card-protection__title">Chiffrement SSL</h3>
                    <p class="card-protection__text">Vos données circulent chiffrées entre votre navigateur et nos serveurs, du premier clic à la confirmation.</p>
                </article>

                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                    <h3 class="card-protection__title">3D Secure</h3>
                    <p class="card-protection__text">Chaque paiement par carte est confirmé auprès de votre banque, par SMS ou via son application.</p>
                </article>

                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                    <h3 class="card-protection__title">Aucune donnée stockée</h3>
                    <p class="card-protection__text">Votre numéro de carte n'est jamais enregistré chez nous : il est traité par Stripe, certifié PCI DSS niveau 1.</p>
                </article>

                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                    <h3 class="card-protection__title">Remboursement garanti</h3>
                    <p class="card-protection__text">Annulation gratuite jusqu'à 48h avant le créneau pour les annonces en politique souple. Le remboursement est traité sous 5 à 7 jours ouvrés.</p>
                </article>
            </div>
        </section>

        <!-- BLOC 7 : La règle d'or, ne jamais payer hors plateforme -->
        <section class="paiement-alerte" aria-labelledby="alerte-titre">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
            <div class="paiement-alerte__contenu">
                <h2 id="alerte-titre">La règle d'or : tout passe par le site</h2>
                <p>Ne réglez jamais une réservation en dehors de Pool Party. Un hôte qui demande un virement, des espèces ou un paiement par une autre application ne respecte pas nos conditions : en payant hors du site, vous perdez la protection des fonds, l'assistance et tout recours en cas de litige.</p>
                <p>Face à une demande de ce type, refusez et prévenez-nous : <a href="<?php echo esc_url(home_url('/contact/')); ?>">contactez le service client</a>, nous prenons le relais.</p>
            </div>
        </section>

        <!-- BLOC 8 : Questions fréquentes sur le paiement -->
        <section class="paiement-faq" aria-labelledby="faq-titre">
            <h2 id="faq-titre">Vos questions sur le paiement</h2>

            <ul class="faq-liste">
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-reponse-1">Quand suis-je débité ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-reponse-1" hidden>
                        <p>Dès que l'hôte confirme votre demande, sous 24 heures maximum. En paiement en trois fois, seul le premier versement est prélevé à la confirmation ; les deux suivants partent à 30 et 60 jours.</p>
                    </div>
                </li>
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-reponse-2">Mes coordonnées bancaires sont-elles conservées ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-reponse-2" hidden>
                        <p>Non. Votre numéro de carte est transmis directement à Stripe, notre prestataire de paiement, et n'est jamais stocké sur nos serveurs. Vos coordonnées ne sont pas non plus communiquées à l'hôte.</p>
                    </div>
                </li>
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-reponse-3">Un hôte me demande de payer en dehors du site, que faire ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-reponse-3" hidden>
                        <p>Refusez systématiquement. Payer hors de la plateforme vous prive de la protection des fonds et de tout recours. Signalez la demande au service client depuis la <a href="<?php echo esc_url(home_url('/contact/')); ?>">page contact</a> : nous traitons ces signalements en priorité.</p>
                    </div>
                </li>
            </ul>

            <p class="paiement-lien"><a href="<?php echo esc_url(home_url('/faq/')); ?>" class="btn-link">Voir toutes les questions fréquentes</a></p>
        </section>

        <!-- BLOC 9 : CTA final -->
        <section class="paiement-cta" aria-labelledby="paiement-cta-titre">
            <h2 id="paiement-cta-titre">Réservez l'esprit tranquille</h2>
            <p>Votre paiement est protégé du premier clic au grand plongeon.</p>
            <div class="paiement-cta__actions">
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="btn btn-secondary">Explorer les espaces</a>
                <a href="<?php echo esc_url(home_url('/cgv/')); ?>" class="btn btn-tertiary">Lire les conditions de vente</a>
            </div>
        </section>
    </main>

<?php get_footer(); ?>
