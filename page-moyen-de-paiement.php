<?php
/**
 * Gabarit de la page « Moyens de paiement » (contenu repris de moyen-de-paiement.html).
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
                    <li><span class="is-current" aria-current="page">Moyen de paiement</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="paiement-hero">
            <h1>Moyens de paiement</h1>
            <p>Carte bancaire, PayPal, en une ou trois fois : réglez votre réservation comme vous préférez. Le prix affiché est le prix final et tout se passe sur le site.</p>
        </section>

        <!-- BLOC 3 : Les moyens de paiement acceptés -->
        <section class="paiement-moyens" aria-labelledby="moyens-titre">
            <h2 id="moyens-titre">Choisissez votre façon de payer</h2>

            <div class="paiement-moyens__grille">
                <article class="moyen-card">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    <h3 class="moyen-card__titre">Carte bancaire</h3>
                    <p class="moyen-card__texte">Le plus direct : saisissez vos coordonnées au moment de la réservation, puis confirmez auprès de votre banque grâce au 3D Secure. Les cartes à débit immédiat comme différé sont acceptées, y compris les cartes émises à l'étranger.</p>
                    <ul class="moyen-card__logos" aria-label="Cartes acceptées">
                        <li>Visa</li>
                        <li>Mastercard</li>
                        <li>CB</li>
                        <li>Amex</li>
                    </ul>
                </article>

                <article class="moyen-card">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6.5 21 8 13h4a5 5 0 0 0 5-5 4 4 0 0 0-4-4H7.5a1 1 0 0 0-1 .8L4 17"/><path d="M8 13c4.5 0 8-1 9-5"/></svg>
                    <h3 class="moyen-card__titre">PayPal</h3>
                    <p class="moyen-card__texte">Réglez depuis votre compte PayPal sans saisir de numéro de carte sur le site : votre adresse email et votre mot de passe PayPal suffisent. La protection des achats PayPal s'ajoute à celle de la plateforme.</p>
                </article>

                <article class="moyen-card">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/></svg>
                    <h3 class="moyen-card__titre">Paiement en 3 fois</h3>
                    <p class="moyen-card__texte">Sans frais ni dossier, par carte bancaire, dès 60 € de réservation : un premier versement le jour de la réservation, puis deux prélèvements automatiques à 30 et 60 jours sur la même carte.</p>
                </article>
            </div>

            <p class="paiement-moyens__note">Les chèques, les espèces et les virements directs à l'hôte ne sont pas acceptés : chaque réservation se règle sur Pool Party, c'est ce qui <a href="<?php echo esc_url(home_url('/paiement-securise/')); ?>">protège votre paiement</a>.</p>
        </section>

        <!-- BLOC 4 : Échéancier, quand êtes-vous débité -->
        <section class="moyen-echeancier" aria-labelledby="echeancier-titre">
            <h2 id="echeancier-titre">Quand êtes-vous débité ?</h2>
            <p class="moyen-echeancier__sub">Le montant total reste identique dans les deux cas : aucun frais ne s'ajoute en cours de route.</p>

            <div class="moyen-echeancier__grille">
                <article class="echeancier-card">
                    <h3 class="echeancier-card__titre">Payer en une fois</h3>
                    <p class="echeancier-card__aide">Le montant complet est débité au moment où vous confirmez la réservation.</p>
                    <dl class="resa__recap">
                        <div class="resa__ligne"><dt>Aujourd'hui, à la réservation</dt><dd>76 €</dd></div>
                        <div class="resa__ligne resa__ligne--total"><dt>Total débité</dt><dd>76 €</dd></div>
                    </dl>
                </article>

                <article class="echeancier-card">
                    <h3 class="echeancier-card__titre">Payer en 3 fois, sans frais</h3>
                    <p class="echeancier-card__aide">Un premier versement aujourd'hui, les deux suivants sont prélevés automatiquement.</p>
                    <dl class="resa__recap">
                        <div class="resa__ligne"><dt>Aujourd'hui, à la réservation</dt><dd>25,34 €</dd></div>
                        <div class="resa__ligne"><dt>À 30 jours</dt><dd>25,33 €</dd></div>
                        <div class="resa__ligne"><dt>À 60 jours</dt><dd>25,33 €</dd></div>
                        <div class="resa__ligne resa__ligne--total"><dt>Total débité</dt><dd>76 €</dd></div>
                    </dl>
                </article>
            </div>

            <p class="moyen-echeancier__note">Exemple pour une réservation de 76 €. En cas d'annulation gratuite, les versements restants sont annulés et les sommes déjà prélevées vous sont remboursées.</p>
        </section>

        <!-- BLOC 5 : Versements des hôtes -->
        <section class="moyen-hotes" aria-labelledby="hotes-titre">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01"/><path d="M18 12h.01"/></svg>
            <div class="moyen-hotes__contenu">
                <h2 id="hotes-titre">Et côté hôtes ?</h2>
                <p>Vous proposez votre espace ? Vos revenus sont versés par virement bancaire sur votre compte, 24 heures après la fin de chaque créneau. Les versements passent par Stripe : vos coordonnées bancaires ne sont jamais communiquées aux invités et le suivi se fait depuis votre espace hôte.</p>
                <p class="moyen-hotes__lien"><a href="<?php echo esc_url(home_url('/proposer/')); ?>" class="btn-link">Proposer mon espace</a></p>
            </div>
        </section>

        <!-- BLOC 6 : Renvoi vers le paiement sécurisé (garanties détaillées sur sa page dédiée) -->
        <section class="moyen-securite" aria-labelledby="securite-titre">
            <h2 id="securite-titre">Un paiement protégé de bout en bout</h2>
            <p class="paiement-renvoi__texte">Authentification 3D Secure, transactions opérées par Stripe, aucune donnée bancaire stockée et fonds conservés jusqu'au lendemain de votre créneau : la sécurité de chaque transaction est détaillée sur une page dédiée.</p>
            <p class="moyen-securite__lien">
                <a href="<?php echo esc_url(home_url('/paiement-securise/')); ?>" class="btn-link">Tout savoir sur le paiement sécurisé</a>
            </p>
        </section>

        <!-- BLOC 7 : Questions fréquentes sur les moyens de paiement -->
        <section class="paiement-faq" aria-labelledby="faq-titre">
            <h2 id="faq-titre">Vos questions sur les moyens de paiement</h2>

            <ul class="faq-liste">
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-reponse-1">Quelles cartes bancaires sont acceptées ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-reponse-1" hidden>
                        <p>Visa, Mastercard, CB et American Express, à débit immédiat ou différé, françaises comme étrangères. Les cartes prépayées fonctionnent si elles autorisent les paiements en ligne avec 3D Secure.</p>
                    </div>
                </li>
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-reponse-2">Comment fonctionne le paiement en 3 fois ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-reponse-2" hidden>
                        <p>Il est proposé par carte bancaire dès 60 € de réservation, sans frais ni justificatif. Le premier versement part le jour de la réservation, les deux suivants sont prélevés automatiquement à 30 et 60 jours sur la même carte. Veillez simplement à ce qu'elle reste valide jusqu'au dernier prélèvement.</p>
                    </div>
                </li>
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-reponse-3">Ma carte est refusée, que faire ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-reponse-3" hidden>
                        <p>Vérifiez d'abord le plafond de paiement de votre carte et sa date d'expiration, puis validez bien la confirmation 3D Secure envoyée par votre banque. Si le refus persiste, essayez une autre carte ou PayPal, ou écrivez-nous via la <a href="<?php echo esc_url(home_url('/contact/')); ?>">page contact</a>.</p>
                    </div>
                </li>
            </ul>

            <p class="paiement-lien"><a href="<?php echo esc_url(home_url('/faq/')); ?>" class="btn-link">Voir toutes les questions fréquentes</a></p>
        </section>

        <!-- BLOC 8 : CTA final -->
        <section class="paiement-cta" aria-labelledby="paiement-cta-titre">
            <h2 id="paiement-cta-titre">Réservez comme vous voulez</h2>
            <p>Carte ou PayPal, en une ou trois fois : le tarif reste le même.</p>
            <div class="paiement-cta__actions">
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="btn btn-secondary">Explorer les espaces</a>
                <a href="<?php echo esc_url(home_url('/paiement-securise/')); ?>" class="btn btn-tertiary">Le paiement sécurisé en détail</a>
            </div>
        </section>
    </main>

<?php get_footer(); ?>
