<?php
/**
 * Gabarit de la page « FAQ » (contenu repris de faq.html).
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 */
get_header();
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane -->
        <div class="faq-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><span class="is-current" aria-current="page">FAQ</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page + recherche dans la FAQ -->
        <section class="faq-hero">
            <h1>Comment pouvons-nous vous aider ?</h1>
            <p>Réservation, paiement, annulation, assurance : les réponses aux questions que vous nous posez le plus souvent.</p>

            <form class="input-search faq-hero__recherche" role="search" aria-label="Rechercher dans les questions fréquentes">
                <label class="sr-only" for="faq-recherche">Rechercher une question</label>
                <input type="search" id="faq-recherche" name="faq-recherche" placeholder="Rechercher une question : annulation, remboursement...">
                <button type="submit" class="input-search__submit" aria-label="Lancer la recherche">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                        <line x1="16.5" y1="16.5" x2="21" y2="21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>
        </section>

        <!-- BLOC 3 : Navigation par thème -->
        <nav class="faq-themes" aria-label="Thèmes de la FAQ">
            <ul>
                <li>
                    <a class="faq-themes__pill" href="#faq-reservations">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/><path d="m9 16 2 2 4-4"/></svg>
                        Réservations
                    </a>
                </li>
                <li>
                    <a class="faq-themes__pill" href="#faq-paiement">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        Paiement et remboursement
                    </a>
                </li>
                <li>
                    <a class="faq-themes__pill" href="#faq-annulation">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/><path d="m14 14-4 4"/><path d="m10 14 4 4"/></svg>
                        Annulation
                    </a>
                </li>
                <li>
                    <a class="faq-themes__pill" href="#faq-hotes">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                        Hôtes et annonces
                    </a>
                </li>
                <li>
                    <a class="faq-themes__pill" href="#faq-securite">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                        Sécurité et assurance
                    </a>
                </li>
                <li>
                    <a class="faq-themes__pill" href="#faq-compte">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/></svg>
                        Compte et données
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Message affiché quand la recherche ne trouve rien (géré en JS) -->
        <p class="faq-vide" id="faq-vide" hidden>Aucune question ne correspond à votre recherche. Essayez un autre mot ou <a href="<?php echo esc_url(home_url('/contact/')); ?>">écrivez-nous directement</a>.</p>

        <!-- BLOC 4 : Les questions, regroupées par thème -->
        <div class="faq-contenu">

            <section class="faq-section" id="faq-reservations" aria-labelledby="faq-reservations-titre">
                <h2 id="faq-reservations-titre">Réservations</h2>
                <ul class="faq-liste">
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-resa-1">Comment réserver un espace ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-resa-1" hidden>
                            <p>Choisissez une annonce, sélectionnez votre date, votre créneau horaire et le nombre d'invités, puis validez le paiement en ligne. L'hôte confirme votre demande sous 24 heures maximum et vous recevez alors un email de confirmation avec le récapitulatif de votre réservation.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-resa-2">Puis-je modifier ma réservation après l'avoir validée ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-resa-2" hidden>
                            <p>Oui. Rendez-vous dans votre espace personnel, rubrique Mes réservations, jusqu'à 48 heures avant le début du créneau. Le changement de date ou d'horaire reste soumis aux disponibilités de l'hôte.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-resa-3">Combien d'invités puis-je amener ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-resa-3" hidden>
                            <p>La capacité maximale est indiquée sur chaque annonce et fixée par l'hôte. Elle doit être respectée : en cas de dépassement constaté sur place, l'hôte peut mettre fin au créneau sans remboursement.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-resa-4">Comment se passe l'arrivée sur place ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-resa-4" hidden>
                            <p>L'adresse exacte et les consignes d'accès vous sont envoyées par email dès la confirmation de la réservation. Le jour J, l'hôte vous accueille, vous présente l'espace et reste joignable pendant toute la durée du créneau.</p>
                        </div>
                    </li>
                </ul>
            </section>

            <section class="faq-section" id="faq-paiement" aria-labelledby="faq-paiement-titre">
                <h2 id="faq-paiement-titre">Paiement et remboursement</h2>
                <ul class="faq-liste">
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-pay-1">Quels moyens de paiement sont acceptés ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-pay-1" hidden>
                            <p>Les cartes Visa, Mastercard, CB et American Express, ainsi que PayPal. Tous les paiements passent par une connexion sécurisée avec authentification 3D Secure ; aucun règlement en espèces n'est autorisé sur place.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-pay-2">La commission Pool Party est-elle incluse dans le prix affiché ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-pay-2" hidden>
                            <p>Oui, le prix affiché sur chaque annonce comprend la commission de la plateforme. Aucun frais supplémentaire ne s'ajoute au moment du paiement.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-pay-3">Quand suis-je débité ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-pay-3" hidden>
                            <p>Une empreinte bancaire est enregistrée au moment de votre demande, puis le montant total est débité dès que l'hôte confirme la réservation, sous 24 heures maximum. L'hôte, lui, ne reçoit son versement qu'après la fin du créneau : c'est votre garantie en cas d'annulation ou de problème sur place.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-pay-4">Quels sont les délais de remboursement en cas d'annulation ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-pay-4" hidden>
                            <p>Les remboursements sont traités sous 5 à 7 jours ouvrés après validation de la demande. Le délai final dépend ensuite de votre banque.</p>
                        </div>
                    </li>
                </ul>
            </section>

            <section class="faq-section" id="faq-annulation" aria-labelledby="faq-annulation-titre">
                <h2 id="faq-annulation-titre">Annulation</h2>
                <ul class="faq-liste">
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-annul-1">Comment annuler ma réservation ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-annul-1" hidden>
                            <p>Rendez-vous dans votre espace personnel, rubrique Mes réservations, puis choisissez Annuler sur la réservation concernée. Le remboursement dépend de la politique d'annulation choisie par l'hôte : avec la politique souple, la plus répandue, l'annulation est gratuite jusqu'à 48 heures avant le début du créneau.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-annul-2">Que se passe-t-il en cas d'annulation par l'hôte ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-annul-2" hidden>
                            <p>Vous êtes prévenu immédiatement par email et la réservation est intégralement remboursée. Notre équipe vous propose aussi des espaces similaires disponibles aux mêmes dates.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-annul-3">Il pleut le jour de ma réservation : suis-je couvert ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-annul-3" hidden>
                            <p>Pour les espaces extérieurs non couverts, une alerte météo officielle (vigilance orange ou rouge) ouvre droit à une annulation sans frais jusqu'au début du créneau. Une simple averse ne donne en revanche pas droit à un remboursement.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-annul-4">Puis-je être remboursé si je pars avant la fin du créneau ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-annul-4" hidden>
                            <p>Non, le créneau réservé reste dû dans son intégralité, même en cas de départ anticipé. Si un problème sur place a écourté votre venue, signalez-le au support dans les 24 heures : chaque situation est étudiée individuellement.</p>
                        </div>
                    </li>
                </ul>
            </section>

            <section class="faq-section" id="faq-hotes" aria-labelledby="faq-hotes-titre">
                <h2 id="faq-hotes-titre">Hôtes et annonces</h2>
                <ul class="faq-liste">
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-hote-1">Comment proposer mon espace sur Pool Party ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-hote-1" hidden>
                            <p>Cliquez sur Proposer votre espace, puis laissez-vous guider : description du bien, photos, équipements, tarif et disponibilités. L'annonce est relue par notre équipe et publiée sous 24 heures. <a href="<?php echo esc_url(home_url('/proposer/')); ?>">Commencer maintenant</a>.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-hote-2">Combien puis-je gagner en louant ma piscine ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-hote-2" hidden>
                            <p>Vous fixez librement votre tarif horaire en fonction de votre espace, de vos équipements et de votre secteur. La commission de la plateforme est déduite automatiquement de chaque versement ; le montant net vous est indiqué avant la publication de l'annonce.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-hote-3">Dois-je déclarer les revenus de mes locations ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-hote-3" hidden>
                            <p>Oui, les revenus tirés de la location de votre espace doivent être déclarés à l'administration fiscale. Pool Party vous fournit un récapitulatif annuel de vos versements pour faciliter votre déclaration.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-hote-4">Puis-je refuser une demande de réservation ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-hote-4" hidden>
                            <p>Oui. Les profils des locataires sont vérifiés (identité et téléphone) et notés après chaque location, et vous restez libre d'accepter ou de refuser une demande avant sa confirmation. Vous pouvez aussi bloquer des dates à tout moment dans votre calendrier.</p>
                        </div>
                    </li>
                </ul>
            </section>

            <section class="faq-section" id="faq-securite" aria-labelledby="faq-securite-titre">
                <h2 id="faq-securite-titre">Sécurité et assurance</h2>
                <ul class="faq-liste">
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-secu-1">Les baignades sont-elles surveillées ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-secu-1" hidden>
                            <p>Non, aucun maître-nageur n'est présent sur place. Les enfants restent sous la surveillance permanente des adultes qui les accompagnent, et les consignes de sécurité propres à chaque espace figurent sur l'annonce.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-secu-2">Que couvre l'assurance Pool Party ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-secu-2" hidden>
                            <p>Chaque réservation inclut une garantie couvrant les dommages matériels causés à l'espace pendant le créneau, dans les conditions et plafonds fixés par notre partenaire assurance, rappelés lors de la réservation. Elle complète votre responsabilité civile, elle ne la remplace pas.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-secu-3">Que faire en cas de problème sur place ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-secu-3" hidden>
                            <p>Contactez d'abord l'hôte, joignable pendant toute la durée du créneau. Si le problème persiste, signalez-le à notre support depuis votre espace personnel dans les 24 heures suivant la location : photos et messages échangés servent de justificatifs.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-secu-4">Les espaces proposés sont-ils vérifiés ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-secu-4" hidden>
                            <p>Oui. Chaque annonce est relue par notre équipe avant publication : photos, équipements et informations de sécurité sont contrôlés. Les avis laissés après chaque réservation proviennent uniquement de locataires ayant réellement séjourné sur place.</p>
                        </div>
                    </li>
                </ul>
            </section>

            <section class="faq-section" id="faq-compte" aria-labelledby="faq-compte-titre">
                <h2 id="faq-compte-titre">Compte et données</h2>
                <ul class="faq-liste">
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-cpt-1">Comment créer un compte ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-cpt-1" hidden>
                            <p>Cliquez sur Connexion puis sur Créer un compte : une adresse email et un mot de passe suffisent. La création est gratuite et nécessaire pour réserver un espace ou publier une annonce.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-cpt-2">J'ai oublié mon mot de passe, que faire ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-cpt-2" hidden>
                            <p>Cliquez sur Mot de passe oublié dans la fenêtre de connexion. Un email de réinitialisation vous est envoyé dans les minutes qui suivent ; pensez à vérifier votre dossier de courriers indésirables.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-cpt-3">Mes données personnelles sont-elles partagées ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-cpt-3" hidden>
                            <p>Non, vos données servent uniquement au traitement de vos réservations et de vos demandes. Elles ne sont jamais revendues à des tiers, conformément à notre <a href="<?php echo esc_url(home_url('/mentions-legales/')); ?>">politique de confidentialité</a>.</p>
                        </div>
                    </li>
                    <li class="faq-item">
                        <h3 class="faq-item__question">
                            <button type="button" aria-expanded="false" aria-controls="faq-reponse-cpt-4">Comment supprimer mon compte ?</button>
                        </h3>
                        <div class="faq-item__reponse" id="faq-reponse-cpt-4" hidden>
                            <p>Depuis votre espace personnel, rubrique Paramètres, choisissez Supprimer mon compte. Vos données sont alors effacées dans les délais prévus par le RGPD, hors obligations légales de conservation liées aux paiements.</p>
                        </div>
                    </li>
                </ul>
            </section>

        </div>

        <!-- BLOC 5 : CTA vers le contact -->
        <section class="faq-cta" aria-labelledby="faq-cta-titre">
            <h2 id="faq-cta-titre">Vous n'avez pas trouvé votre réponse ?</h2>
            <p>Notre équipe vous répond en moins de 24 heures, du lundi au samedi.</p>
            <div class="faq-cta__actions">
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-secondary">Contacter l'équipe</a>
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="btn btn-tertiary">Explorer les espaces</a>
            </div>
        </section>
    </main>

<?php get_footer(); ?>
