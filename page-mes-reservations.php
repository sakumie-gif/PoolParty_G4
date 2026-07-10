<?php
/**
 * Gabarit de la page « Mes réservations ».
 * Page hors maquette Figma, composée avec les briques du site (fil
 * d'Ariane, cartes, boutons, badges). Modèle des « Voyages » d'Airbnb :
 * un onglet À venir / Passées et, pour chaque demande, une carte
 * horizontale (photo, statut, dates, invités, total, actions).
 *
 * Les réservations sont conservées dans le localStorage du navigateur :
 * le tunnel « Confirmer et payer » y enregistre chaque demande envoyée à
 * l'hôte (main.js), et cette page les reconstruit. Trois états pleine
 * page (visiteur à connecter, aucune réservation, liste) sont basculés
 * par l'attribut hidden, exactement comme la page Mes favoris.
 * En-tête / pied de page fournis par header.php / footer.php ; la feuille
 * mes-reservations.css est chargée par functions.php. Page privée : noindex.
 */
get_header();
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane + titre + compteur (rempli par main.js) -->
        <section class="reservations-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li class="is-current" aria-current="page">Mes réservations</li>
                </ol>
            </nav>

            <h1>Mes réservations</h1>
            <p class="reservations-intro__sub" id="reservations-compte" aria-live="polite"></p>
        </section>

        <!-- BLOC 2 : État visiteur. Les réservations sont rattachées au
             compte : on invite à se connecter avant de les afficher. -->
        <section class="reservations-etat reservations-etat--connexion" id="reservations-connexion" aria-labelledby="reservations-connexion-titre" hidden>
            <svg class="reservations-etat__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/><path d="m9 15 2 2 4-4"/></svg>
            <h2 id="reservations-connexion-titre">Connectez-vous pour suivre vos réservations</h2>
            <p>Vos demandes de réservation sont rattachées à votre compte Pool Party. Connectez-vous pour retrouver ici leur statut, vos dates et vos créneaux.</p>
            <div class="reservations-etat__actions">
                <button type="button" class="btn btn-primary js-open-login">Connexion</button>
                <a href="<?php echo esc_url(home_url('/inscription/')); ?>" class="btn btn-secondary">Inscription</a>
            </div>
        </section>

        <!-- BLOC 3 : État aucune réservation, une fois connecté -->
        <section class="reservations-etat reservations-etat--vide" id="reservations-vide" aria-labelledby="reservations-vide-titre" hidden>
            <svg class="reservations-etat__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/><path d="M12 14v3M10.5 15.5h3"/></svg>
            <h2 id="reservations-vide-titre">Aucune réservation pour le moment</h2>
            <p>Réservez un espace : votre demande s'affichera ici avec sa date, son créneau et son statut, en attendant la réponse de l'hôte.</p>
            <div class="reservations-etat__actions">
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="btn btn-primary">Explorer les espaces</a>
            </div>
        </section>

        <!-- BLOC 4 : Onglets (À venir / Passées) + liste des demandes.
             Les cartes sont construites par main.js depuis le localStorage. -->
        <section class="reservations-liste" id="reservations-liste" aria-label="Vos réservations" hidden>
            <div class="reservations-tabs" role="tablist" aria-label="Filtrer les réservations">
                <button type="button" class="reservations-tab is-active" role="tab" id="tab-a-venir" data-filtre="a-venir" aria-selected="true" aria-controls="reservations-grid">À venir</button>
                <button type="button" class="reservations-tab" role="tab" id="tab-passees" data-filtre="passees" aria-selected="false" aria-controls="reservations-grid">Passées</button>
            </div>

            <div class="reservations-grid" id="reservations-grid" role="tabpanel" aria-labelledby="tab-a-venir"></div>

            <p class="reservations-onglet-vide" id="reservations-onglet-vide" hidden></p>
        </section>

        <!-- Pop-up : confirmer l'annulation d'une demande. Ouverte par le
             bouton « Annuler la demande » d'une carte ; la suppression
             n'a lieu qu'après confirmation (main.js). Reprend l'aspect
             épuré de la pop-up de connexion (carte blanche, croix simple),
             sans la texture eau en arrière-plan. -->
        <div class="popup-overlay" id="popup-annuler-resa" hidden>
            <div class="confirm-popup" role="dialog" aria-modal="true" aria-labelledby="popup-annuler-titre" aria-describedby="popup-annuler-texte">
                <div class="confirm-popup__head">
                    <h2 class="confirm-popup__title" id="popup-annuler-titre">Annuler cette demande ?</h2>
                    <button type="button" class="confirm-popup__close" aria-label="Fermer la fenêtre" data-resa-annuler-fermer>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <p class="confirm-popup__texte" id="popup-annuler-texte"></p>
                <div class="confirm-popup__actions">
                    <button type="button" class="btn btn-tertiary btn-medium" data-resa-annuler-fermer>Conserver</button>
                    <button type="button" class="btn btn-secondary btn-medium" data-resa-annuler-confirmer>Annuler la demande</button>
                </div>
            </div>
        </div>
    </main>

<?php get_footer(); ?>
