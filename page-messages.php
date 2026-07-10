<?php
/**
 * Gabarit de la page « Messages » (messagerie interne).
 * Page hors maquette Figma, composée avec les briques du site (fil
 * d'Ariane, cartes, boutons). Permet, une fois connecté, d'échanger
 * avec l'hôte d'un espace sans passer par l'e-mail.
 *
 * Comme « Mes favoris » et « Mes réservations », la connexion est
 * simulée et les conversations vivent dans le localStorage : main.js
 * construit la boîte de réception (liste des conversations à gauche,
 * fil de discussion à droite) et gère l'envoi des messages. Trois
 * états pleine page (visiteur à connecter, aucune conversation, boîte
 * de réception) sont basculés par l'attribut hidden.
 * En-tête / pied de page fournis par header.php / footer.php ; la
 * feuille messages.css est chargée par functions.php. Page privée : noindex.
 */
get_header();
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane + titre + compteur (rempli par main.js) -->
        <section class="messages-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li class="is-current" aria-current="page">Messages</li>
                </ol>
            </nav>

            <h1>Messages</h1>
            <p class="messages-intro__sub" id="messages-compte" aria-live="polite"></p>
        </section>

        <!-- BLOC 2 : État visiteur. Les conversations sont rattachées au
             compte : on invite à se connecter avant de les afficher. -->
        <section class="messages-etat messages-etat--connexion" id="messages-connexion" aria-labelledby="messages-connexion-titre" hidden>
            <svg class="messages-etat__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            <h2 id="messages-connexion-titre">Connectez-vous pour accéder à vos messages</h2>
            <p>Vos échanges avec les hôtes sont rattachés à votre compte Pool Party. Connectez-vous pour retrouver ici vos conversations et poser vos questions avant de réserver.</p>
            <div class="messages-etat__actions">
                <button type="button" class="btn btn-primary js-open-login">Connexion</button>
                <a href="<?php echo esc_url(home_url('/inscription/')); ?>" class="btn btn-secondary">Inscription</a>
            </div>
        </section>

        <!-- BLOC 3 : État aucune conversation, une fois connecté -->
        <section class="messages-etat messages-etat--vide" id="messages-vide" aria-labelledby="messages-vide-titre" hidden>
            <svg class="messages-etat__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 10h.01M12 10h.01M16 10h.01"/></svg>
            <h2 id="messages-vide-titre">Aucune conversation pour le moment</h2>
            <p>Depuis la fiche d'un espace, contactez l'hôte : votre échange apparaîtra ici, comme une messagerie, sans passer par votre boîte mail.</p>
            <div class="messages-etat__actions">
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="btn btn-primary">Explorer les espaces</a>
            </div>
        </section>

        <!-- BLOC 4 : Boîte de réception (deux panneaux). Liste des
             conversations à gauche, fil de discussion à droite. En mobile,
             un seul panneau est visible à la fois (classe is-thread-open
             posée par main.js quand on ouvre une conversation). -->
        <section class="messagerie" id="messagerie" aria-label="Boîte de réception" hidden>

            <!-- Panneau gauche : liste des conversations -->
            <div class="messagerie__liste" aria-label="Vos conversations">
                <ul class="messagerie__convos" id="messagerie-convos"></ul>
            </div>

            <!-- Panneau droit : fil de discussion actif -->
            <div class="messagerie__fil" id="messagerie-fil" aria-live="polite">

                <!-- En-tête du fil : retour (mobile), hôte, lien vers le bien -->
                <div class="messagerie__fil-tete" id="messagerie-fil-tete" hidden>
                    <button type="button" class="messagerie__retour" id="messagerie-retour" aria-label="Retour à la liste des conversations">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <img class="messagerie__fil-photo" id="messagerie-fil-photo" src="" alt="">
                    <div class="messagerie__fil-titres">
                        <p class="messagerie__fil-hote" id="messagerie-fil-hote"></p>
                        <a class="messagerie__fil-bien" id="messagerie-fil-bien" href=""></a>
                    </div>
                </div>

                <!-- Messages du fil (construits par main.js) -->
                <div class="messagerie__messages" id="messagerie-messages"></div>

                <!-- Aucun fil sélectionné (desktop) -->
                <div class="messagerie__vide-fil" id="messagerie-vide-fil">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <p>Sélectionnez une conversation pour afficher les messages.</p>
                </div>

                <!-- Zone de saisie -->
                <form class="messagerie__saisie" id="messagerie-saisie" hidden>
                    <label class="sr-only" for="messagerie-champ">Votre message</label>
                    <textarea class="messagerie__champ" id="messagerie-champ" name="message" rows="1" placeholder="Écrivez votre message…" autocomplete="off"></textarea>
                    <button type="submit" class="messagerie__envoyer" aria-label="Envoyer le message" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/></svg>
                    </button>
                </form>
            </div>
        </section>
    </main>

<?php get_footer(); ?>
