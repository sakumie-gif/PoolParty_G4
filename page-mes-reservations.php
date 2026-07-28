<?php
/**
 * Gabarit de la page « Mes réservations » V2.
 * -------------------------------------------
 * Page unique pour les deux casquettes d'un compte : la bascule
 * Hôte / Locataire change la vue, pas le rôle. Onglets En attente /
 * A venir / Passées / Avis dans une colonne (desktop) ou un
 * sélecteur à pilules (mobile). Les données viennent de la base
 * (CPT reservation + avis en commentaires WordPress), localisées
 * dans ppData et rendues par js/mes-reservations-v2.js ; les
 * actions (accepter, refuser, annuler, avis) passent en AJAX.
 * Remplace aussi l'ancienne page « Demandes de réservation »,
 * qui redirige désormais ici. Page privée : noindex.
 */

get_header();
?>

    <main id="contenu">
        <!-- Fil d'Ariane -->
        <section class="reservations-intro rv2-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li class="is-current" aria-current="page">Réservations</li>
                </ol>
            </nav>
        </section>

        <!-- Bandeau : titre + bascule Hôte / Locataire (maquette Figma).
             La bascule change la vue affichée, pas le compte. -->
        <section class="hero-page hero-page--membre">
            <h1>Réservations</h1>
            <div class="rv2-toggle" role="group" aria-label="Choisir la vue">
                <button type="button" class="rv2-toggle__btn" data-role="hote" aria-pressed="false">Hôte</button>
                <button type="button" class="rv2-toggle__btn is-active" data-role="locataire" aria-pressed="true">Locataire</button>
            </div>
        </section>

        <?php if (!is_user_logged_in()) : ?>

            <!-- État visiteur : les réservations sont rattachées au compte -->
            <section class="reservations-etat reservations-etat--connexion" aria-labelledby="reservations-connexion-titre">
                <svg class="reservations-etat__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/><path d="m9 15 2 2 4-4"/></svg>
                <h2 id="reservations-connexion-titre">Connectez-vous pour suivre vos réservations</h2>
                <p>Vos demandes de réservation sont rattachées à votre compte Pool Party. Connectez-vous pour retrouver ici leur statut, vos dates et vos créneaux.</p>
                <div class="reservations-etat__actions">
                    <button type="button" class="btn btn-primary js-open-login">Connexion</button>
                    <a href="<?php echo esc_url(home_url('/inscription/')); ?>" class="btn btn-secondary">Inscription</a>
                </div>
            </section>

        <?php else : ?>

        <!-- Colonne d'onglets + contenu (rendus par mes-reservations-v2.js) -->
        <div class="rv2-layout">

            <aside class="rv2-tabs-panel">
                <div class="rv2-tabs" role="tablist" aria-label="Filtrer les réservations">
                    <button type="button" class="rv2-tab is-active" role="tab" id="rv2-tab-en-attente" data-filtre="en-attente" aria-selected="true" aria-controls="rv2-contenu">En attente</button>
                    <button type="button" class="rv2-tab" role="tab" id="rv2-tab-a-venir" data-filtre="a-venir" aria-selected="false" aria-controls="rv2-contenu">A venir</button>
                    <button type="button" class="rv2-tab" role="tab" id="rv2-tab-passees" data-filtre="passees" aria-selected="false" aria-controls="rv2-contenu">Passées</button>
                    <button type="button" class="rv2-tab" role="tab" id="rv2-tab-avis" data-filtre="avis" aria-selected="false" aria-controls="rv2-contenu">Avis</button>
                </div>
            </aside>

            <section class="rv2-contenu" id="rv2-contenu" role="tabpanel" aria-labelledby="rv2-tab-en-attente">

                <p class="rv2-count" id="rv2-count" aria-live="polite"></p>

                <!-- Outils de l'onglet Avis : sous-onglets (vue hôte),
                     pilules Trier par / Filtrer (composants du catalogue) -->
                <div class="rv2-avis-outils" id="rv2-avis-outils" hidden>
                    <div class="reservations-tabs rv2-avis-soustabs" id="rv2-avis-soustabs" role="tablist" aria-label="Filtrer les avis" hidden>
                        <button type="button" class="reservations-tab is-active" role="tab" data-sousonglet="espaces" aria-selected="true">Avis sur vos espaces</button>
                        <button type="button" class="reservations-tab" role="tab" data-sousonglet="locataires" aria-selected="false">Vos locataires</button>
                    </div>
                    <div class="rv2-avis-filtres">
                        <div class="rv2-pilule">
                            <button type="button" class="menu-item" id="rv2-tri-btn" aria-expanded="false" aria-controls="rv2-tri-liste">Trier par</button>
                            <ul class="rv2-pilule__liste" id="rv2-tri-liste" hidden>
                                <li><button type="button" class="dropdown-item is-active" data-tri="recent">Plus récents d'abord</button></li>
                                <li><button type="button" class="dropdown-item" data-tri="ancien">Plus anciens d'abord</button></li>
                            </ul>
                        </div>
                        <div class="rv2-pilule">
                            <button type="button" class="menu-item" id="rv2-filtre-btn" aria-expanded="false" aria-controls="rv2-filtre-liste">Filtrer</button>
                            <ul class="rv2-pilule__liste" id="rv2-filtre-liste" hidden></ul>
                        </div>
                    </div>
                </div>

                <div class="reservations-grid" id="rv2-grid"></div>

                <!-- Bon à savoir : sur En attente (délai de 24 h) et
                     A venir (règles d'annulation), texte selon la vue -->
                <section class="rv2-savoir" aria-labelledby="rv2-savoir-titre" hidden>
                    <div class="rv2-savoir__head">
                        <span class="rv2-savoir__icone" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18h6"/><path d="M10 21h4"/><path d="M12 3a6 6 0 0 0-3.5 10.9c.6.5 1 1.2 1.2 2.1h4.6c.2-.9.6-1.6 1.2-2.1A6 6 0 0 0 12 3z"/></svg>
                        </span>
                        <h2 id="rv2-savoir-titre">Bon à savoir</h2>
                    </div>

                    <div class="rv2-savoir__texte" id="rv2-savoir-locataire">
                        <p>Le propriétaire dispose de 24 heures pour répondre à votre demande. Sans réponse de sa part dans ce délai, celle-ci sera automatiquement annulée.</p>
                        <p>Vous pouvez envoyer plusieurs demandes pour augmenter vos chances de trouver un espace disponible.</p>
                        <p>Attention : votre réservation sera confirmée dès qu'un propriétaire accepte votre demande. Pensez donc à annuler les autres demandes qui ne vous intéressent plus.</p>
                    </div>

                    <div class="rv2-savoir__texte" id="rv2-savoir-hote" hidden>
                        <p>Vous disposez de 24 heures pour répondre à une demande de réservation.</p>
                        <p>Passé ce délai, celle-ci sera automatiquement annulée.</p>
                        <p>Pour préserver votre visibilité dans les résultats de recherche, pensez à accepter ou refuser chaque demande, même lorsque vous n'êtes pas disponible.</p>
                        <p>Un locataire peut contacter plusieurs propriétaires en même temps. La réservation est confirmée auprès du premier propriétaire qui l'accepte.</p>
                    </div>

                    <div class="rv2-savoir__texte" id="rv2-savoir-avenir-locataire" hidden>
                        <p>Un empêchement ? Envoyez un message à votre hôte depuis la messagerie pour demander l'annulation.</p>
                        <p>Demande faite plus de 48 heures avant le créneau : la réservation est annulée et intégralement remboursée. Moins de 48 heures avant, l'annulation reste possible mais la réservation n'est pas remboursée.</p>
                        <p>Tout passe par la plateforme : n'échangez jamais d'argent en dehors de Pool Party.</p>
                    </div>

                    <div class="rv2-savoir__texte" id="rv2-savoir-avenir-hote" hidden>
                        <p>Un imprévu (accident, travaux, impossibilité de recevoir) ? Vous pouvez annuler une réservation confirmée depuis cette page.</p>
                        <p>Expliquez toujours la raison : votre message est transmis au locataire et il est intégralement remboursé.</p>
                        <p>Prévenez le plus tôt possible : les annulations tardives ou répétées réduisent la visibilité de votre annonce.</p>
                    </div>

                    <p class="rv2-savoir__aide">Besoin d'aide ? <a href="<?php echo esc_url(home_url('/contact/')); ?>">Contactez-nous</a></p>
                </section>

            </section>

        </div>

        <!-- Pop-up de confirmation générique (style login épuré) :
             titre, texte, étoiles et champ remplis selon l'action. -->
        <div class="popup-overlay" id="rv2-popup" hidden>
            <div class="confirm-popup" role="dialog" aria-modal="true" aria-labelledby="rv2-popup-titre" aria-describedby="rv2-popup-texte">
                <div class="confirm-popup__head">
                    <h2 class="confirm-popup__title" id="rv2-popup-titre"></h2>
                    <button type="button" class="confirm-popup__close" aria-label="Fermer la fenêtre" data-rv2-fermer>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <p class="confirm-popup__texte" id="rv2-popup-texte"></p>
                <div class="rv2-popup-etoiles" id="rv2-popup-etoiles" role="radiogroup" aria-label="Votre note sur 5" hidden>
                    <?php for ($pp_note = 1; $pp_note <= 5; $pp_note++) : ?>
                    <button type="button" data-note="<?php echo (int) $pp_note; ?>" aria-label="<?php echo (int) $pp_note; ?> <?php echo $pp_note > 1 ? 'étoiles' : 'étoile'; ?> sur 5"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.6c.3 0 .58.17.71.45l2.45 4.96 5.48.8c.65.09.91.89.44 1.35l-3.96 3.86.93 5.45c.11.65-.57 1.14-1.15.84L12 17.74l-4.9 2.57c-.58.3-1.26-.19-1.15-.84l.93-5.45-3.96-3.86c-.47-.46-.21-1.26.44-1.35l5.48-.8 2.45-4.96A.79.79 0 0 1 12 2.6z"/></svg></button>
                    <?php endfor; ?>
                </div>
                <textarea class="rv2-popup-textarea" id="rv2-popup-champ" rows="4" placeholder="Écrivez votre message" aria-label="Votre message" hidden></textarea>
                <p class="rv2-popup-erreur" id="rv2-popup-erreur" role="alert" hidden></p>
                <div class="confirm-popup__actions">
                    <button type="button" class="btn btn-tertiary btn-medium" id="rv2-popup-garder" data-rv2-fermer></button>
                    <button type="button" class="btn btn-secondary btn-medium" id="rv2-popup-confirmer"></button>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </main>

<?php get_footer(); ?>
