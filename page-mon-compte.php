<?php
/**
 * Gabarit de la page « Mon compte » (espace membre).
 * Cinq blocs en cartes beiges (référence : page Contact) : informations
 * personnelles, mot de passe, préférences de notification, copie des
 * données, fermeture du compte. Logique dans inc/mon-compte.php.
 */

get_header();

$pp_connecte = is_user_logged_in();
$pp_user     = wp_get_current_user();
$pp_flash    = poolparty_g4_mon_compte_flash();
$pp_erreur   = ($pp_flash && $pp_flash[1] === 'alerte') ? $pp_flash : null;
$pp_succes   = ($pp_flash && $pp_flash[1] === 'ok') ? $pp_flash : null;

// Bandeau d'erreur affiché au-dessus du bloc concerné.
$pp_bloc_erreur = function ($bloc) use ($pp_erreur) {
    if ($pp_erreur && $pp_erreur[2] === $bloc) {
        echo '<div class="mon-compte__erreur" role="alert">' . esc_html($pp_erreur[0]) . '</div>';
    }
};
?>

    <main id="contenu">
        <section class="mon-compte-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li class="is-current" aria-current="page">Mon compte</li>
                </ol>
            </nav>
        </section>

        <section class="hero-page hero-page--membre">
            <h1>Mon compte</h1>
            <p class="hero-page__texte">Gérez vos informations, vos notifications et vos données personnelles.</p>
        </section>

        <section class="mon-compte">

            <?php if (!$pp_connecte) : ?>

                <div class="mon-compte__etat-vide">
                    <h2>Connectez-vous à votre compte</h2>
                    <p>Cette page est réservée aux membres. Connectez-vous pour gérer vos informations, vos notifications et vos données.</p>
                    <button type="button" class="btn btn-primary js-open-login">Connexion</button>
                </div>

            <?php else : ?>

                <!-- Colonne d'onglets, comme Mes réservations : pilules en
                     mobile, colonne verticale beige à partir de 1024px -->
                <aside class="mon-compte-tabs-panel">
                    <div class="mon-compte-tabs" role="tablist" aria-label="Sections du compte">
                        <button type="button" class="mon-compte-tab is-active" role="tab" data-onglet="bloc-infos" aria-selected="true">Informations personnelles</button>
                        <button type="button" class="mon-compte-tab" role="tab" data-onglet="bloc-mdp" aria-selected="false">Mot de passe</button>
                        <button type="button" class="mon-compte-tab" role="tab" data-onglet="bloc-notifs" aria-selected="false">Notifications</button>
                        <button type="button" class="mon-compte-tab" role="tab" data-onglet="bloc-donnees" aria-selected="false">Mes données</button>
                        <button type="button" class="mon-compte-tab" role="tab" data-onglet="bloc-fermer" aria-selected="false">Fermer mon compte</button>
                    </div>
                </aside>

                <section class="mon-compte-contenu">

                <!-- Bloc 1 : informations personnelles -->
                <section class="mon-compte__bloc" id="bloc-infos" aria-labelledby="titre-infos">
                    <h2 id="titre-infos">Informations personnelles</h2>
                    <?php $pp_bloc_erreur('infos'); ?>
                    <form class="mon-compte__form" method="post" action="">
                        <input type="hidden" name="pp_compte_action" value="infos">
                        <?php wp_nonce_field('pp_compte_infos'); ?>
                        <div class="mon-compte__rangee">
                            <div class="form-field">
                                <label class="form-field__label" for="compte-prenom">Prénom</label>
                                <input class="form-field__input" type="text" id="compte-prenom" name="prenom" required autocomplete="given-name" value="<?php echo esc_attr($pp_user->first_name); ?>">
                            </div>
                            <div class="form-field">
                                <label class="form-field__label" for="compte-nom">Nom</label>
                                <input class="form-field__input" type="text" id="compte-nom" name="nom" required autocomplete="family-name" value="<?php echo esc_attr($pp_user->last_name); ?>">
                            </div>
                        </div>
                        <div class="form-field">
                            <label class="form-field__label" for="compte-affiche">Nom affiché</label>
                            <input class="form-field__input" type="text" id="compte-affiche" name="nom_affiche" value="<?php echo esc_attr($pp_user->display_name); ?>">
                            <p class="form-field__aide">C'est le nom que les autres membres voient sur le site.</p>
                        </div>
                        <div class="form-field">
                            <label class="form-field__label" for="compte-email">Adresse e-mail</label>
                            <input class="form-field__input" type="email" id="compte-email" name="email" required autocomplete="email" value="<?php echo esc_attr($pp_user->user_email); ?>">
                            <p class="form-field__aide">En cas de changement, un e-mail d'information est envoyé à votre ancienne adresse.</p>
                        </div>
                        <div class="mon-compte__actions">
                            <button type="submit" class="btn btn-primary btn-medium">Enregistrer</button>
                        </div>
                    </form>
                </section>

                <!-- Bloc 2 : mot de passe -->
                <section class="mon-compte__bloc" id="bloc-mdp" aria-labelledby="titre-mdp" hidden>
                    <h2 id="titre-mdp">Mot de passe</h2>
                    <?php $pp_bloc_erreur('mdp'); ?>
                    <form class="mon-compte__form" method="post" action="">
                        <input type="hidden" name="pp_compte_action" value="mdp">
                        <?php wp_nonce_field('pp_compte_mdp'); ?>
                        <div class="form-field">
                            <label class="form-field__label" for="compte-mdp-actuel">Mot de passe actuel</label>
                            <div class="form-field__wrap">
                                <input class="form-field__input" type="password" id="compte-mdp-actuel" name="mdp_actuel" required autocomplete="current-password">
                                <button type="button" class="form-field__eye js-compte-oeil" aria-label="Afficher ou masquer le mot de passe" aria-pressed="false">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.06 12.35a1 1 0 0 1 0-.7 10.75 10.75 0 0 1 19.88 0 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-19.88 0"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-field">
                            <label class="form-field__label" for="compte-mdp-nouveau">Nouveau mot de passe</label>
                            <div class="form-field__wrap">
                                <input class="form-field__input" type="password" id="compte-mdp-nouveau" name="mdp_nouveau" required minlength="8" placeholder="8 caractères minimum" autocomplete="new-password">
                                <button type="button" class="form-field__eye js-compte-oeil" aria-label="Afficher ou masquer le mot de passe" aria-pressed="false">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.06 12.35a1 1 0 0 1 0-.7 10.75 10.75 0 0 1 19.88 0 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-19.88 0"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-field">
                            <label class="form-field__label" for="compte-mdp-confirme">Confirmez le nouveau mot de passe</label>
                            <div class="form-field__wrap">
                                <input class="form-field__input" type="password" id="compte-mdp-confirme" name="mdp_confirme" required minlength="8" autocomplete="new-password">
                                <button type="button" class="form-field__eye js-compte-oeil" aria-label="Afficher ou masquer le mot de passe" aria-pressed="false">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.06 12.35a1 1 0 0 1 0-.7 10.75 10.75 0 0 1 19.88 0 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-19.88 0"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="mon-compte__actions">
                            <button type="submit" class="btn btn-primary btn-medium">Enregistrer</button>
                        </div>
                    </form>
                </section>

                <!-- Bloc 3 : préférences de notification -->
                <section class="mon-compte__bloc" id="bloc-notifs" aria-labelledby="titre-notifs" hidden>
                    <h2 id="titre-notifs">Préférences de notification</h2>
                    <form class="mon-compte__form" method="post" action="">
                        <input type="hidden" name="pp_compte_action" value="prefs">
                        <?php wp_nonce_field('pp_compte_prefs'); ?>
                        <?php foreach (poolparty_g4_types_notification() as $pp_type => $pp_libelle) : ?>
                            <label class="checkbox">
                                <input type="checkbox" name="notif_<?php echo esc_attr($pp_type); ?>" value="1"<?php checked(poolparty_g4_notif_active($pp_user->ID, $pp_type)); ?>>
                                <span><?php echo esc_html($pp_libelle); ?></span>
                            </label>
                        <?php endforeach; ?>
                        <p class="mon-compte__note">Les e-mails liés à la sécurité de votre compte et aux réservations confirmées vous sont toujours envoyés.</p>
                        <div class="mon-compte__actions">
                            <button type="submit" class="btn btn-primary btn-medium">Enregistrer</button>
                        </div>
                    </form>
                </section>

                <!-- Bloc 4 : copie des données -->
                <section class="mon-compte__bloc" id="bloc-donnees" aria-labelledby="titre-donnees" hidden>
                    <h2 id="titre-donnees">Obtenir une copie de mes données</h2>
                    <p>Téléchargez un fichier rassemblant votre profil, vos annonces, vos réservations, vos avis et vos messages. Les coordonnées des autres membres n'y figurent jamais.</p>
                    <form class="mon-compte__actions" method="post" action="">
                        <input type="hidden" name="pp_compte_action" value="export">
                        <?php wp_nonce_field('pp_compte_export'); ?>
                        <button type="submit" class="btn btn-secondary btn-medium">Télécharger mes données (JSON)</button>
                    </form>
                </section>

                <!-- Bloc 5 : fermeture du compte -->
                <section class="mon-compte__bloc" id="bloc-fermer" aria-labelledby="titre-fermer" hidden>
                    <h2 id="titre-fermer">Fermer mon compte</h2>
                    <?php $pp_bloc_erreur('fermer'); ?>
                    <p>Cette action est définitive. Vos annonces et vos réservations passées seront conservées par la plateforme de façon anonyme. Vos favoris et vos préférences seront supprimés.</p>
                    <div class="mon-compte__actions">
                        <button type="button" class="btn btn-tertiary btn-medium" id="pp-ouvrir-fermeture">Fermer mon compte</button>
                    </div>
                </section>

                </section><!-- /.mon-compte-contenu -->

            <?php endif; ?>
        </section>
    </main>

    <?php if ($pp_connecte) : ?>

        <!-- Pop-up de confirmation de fermeture (style confirm-popup épuré) -->
        <div class="popup-overlay" id="pp-popup-fermeture" hidden>
            <div class="confirm-popup" role="dialog" aria-modal="true" aria-labelledby="pp-fermeture-titre">
                <div class="confirm-popup__head">
                    <h2 class="confirm-popup__title" id="pp-fermeture-titre">Fermer votre compte ?</h2>
                    <button type="button" class="confirm-popup__close js-compte-fermer-popup" aria-label="Fermer la fenêtre">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>
                <p class="confirm-popup__texte">Cette action est définitive. Pour confirmer, saisissez votre mot de passe.</p>
                <form method="post" action="">
                    <input type="hidden" name="pp_compte_action" value="fermer">
                    <?php wp_nonce_field('pp_compte_fermer'); ?>
                    <div class="form-field">
                        <label class="form-field__label" for="pp-fermeture-mdp">Mot de passe</label>
                        <div class="form-field__wrap">
                            <input class="form-field__input" type="password" id="pp-fermeture-mdp" name="mdp_fermeture" required autocomplete="current-password">
                            <button type="button" class="form-field__eye js-compte-oeil" aria-label="Afficher ou masquer le mot de passe" aria-pressed="false">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.06 12.35a1 1 0 0 1 0-.7 10.75 10.75 0 0 1 19.88 0 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-19.88 0"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>
                    <div class="confirm-popup__actions">
                        <button type="button" class="btn btn-tertiary btn-medium js-compte-fermer-popup">Annuler</button>
                        <button type="submit" class="btn btn-secondary btn-medium">Fermer mon compte</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($pp_succes) : ?>
            <!-- Toast de confirmation (bas droite, s'efface tout seul) -->
            <div class="mon-compte__toast is-visible" id="pp-compte-toast" role="status">
                <span><?php echo esc_html($pp_succes[0]); ?></span>
            </div>
        <?php endif; ?>

        <script>
        (function () {
            // Onglets : une carte visible à la fois. L'ancre (#bloc-mdp...)
            // posée par les redirections rouvre le bon onglet.
            var onglets = document.querySelectorAll('.mon-compte-tab');
            function activerOnglet(cible) {
                var trouve = false;
                onglets.forEach(function (o) { trouve = trouve || o.dataset.onglet === cible; });
                if (!trouve) { return; }
                onglets.forEach(function (o) {
                    var actif = o.dataset.onglet === cible;
                    o.classList.toggle('is-active', actif);
                    o.setAttribute('aria-selected', actif ? 'true' : 'false');
                    var bloc = document.getElementById(o.dataset.onglet);
                    if (bloc) { bloc.hidden = !actif; }
                });
            }
            onglets.forEach(function (o) {
                o.addEventListener('click', function () {
                    activerOnglet(o.dataset.onglet);
                    history.replaceState(null, '', '#' + o.dataset.onglet);
                });
            });
            if (window.location.hash) {
                activerOnglet(window.location.hash.slice(1));
            }

            var toast = document.getElementById('pp-compte-toast');
            if (toast) {
                setTimeout(function () { toast.classList.remove('is-visible'); }, 6000);
            }
            var ouvrir = document.getElementById('pp-ouvrir-fermeture');
            var popup = document.getElementById('pp-popup-fermeture');
            if (ouvrir && popup) {
                ouvrir.addEventListener('click', function () {
                    popup.hidden = false;
                    var champ = document.getElementById('pp-fermeture-mdp');
                    if (champ) { champ.focus(); }
                });
                popup.querySelectorAll('.js-compte-fermer-popup').forEach(function (b) {
                    b.addEventListener('click', function () { popup.hidden = true; });
                });
                popup.addEventListener('click', function (e) { if (e.target === popup) { popup.hidden = true; } });
            }
            document.querySelectorAll('.js-compte-oeil').forEach(function (oeil) {
                oeil.addEventListener('click', function () {
                    var champ = oeil.closest('.form-field__wrap').querySelector('input');
                    var visible = champ.type === 'text';
                    champ.type = visible ? 'password' : 'text';
                    oeil.setAttribute('aria-pressed', visible ? 'false' : 'true');
                });
            });
        })();
        </script>

    <?php endif; ?>

<?php get_footer(); ?>
