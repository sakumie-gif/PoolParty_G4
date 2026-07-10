<?php
/**
 * Pied de page du tunnel « Proposer votre espace » : parcours immersif,
 * donc PAS de footer de site. On garde seulement la pop-up de connexion
 * (lien Connexion de l'écran compte), le bandeau cookies et les scripts
 * WordPress. Appelé via get_footer('proposer') depuis page-proposer.php.
 */
?>

    <!-- Pop-up de connexion (design Figma : Pop up login).
         Ouvert depuis le lien Connexion de l'écran compte, fermé par la
         croix, un clic sur le voile ou la touche Échap. -->
    <div class="popup-overlay" id="login-popup" hidden>
        <div class="login-popup">
            <div class="login-popup__card" role="dialog" aria-modal="true" aria-labelledby="login-titre">
                <div class="login-popup__head">
                    <h2 id="login-titre" class="login-popup__title">Bienvenue sur PoolParty</h2>
                    <button type="button" class="login-popup__close" aria-label="Fermer la fenêtre">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                <form class="login-popup__form" novalidate>
                    <div class="login-popup__fields">
                        <div class="form-field">
                            <label class="form-field__label" for="login-email">Adresse e-mail</label>
                            <input class="form-field__input" type="email" id="login-email" name="email" placeholder="Tapez votre adresse e-mail" autocomplete="email" required>
                        </div>
                        <div class="form-field">
                            <label class="form-field__label" for="login-password">Mot de passe</label>
                            <div class="form-field__wrap">
                                <input class="form-field__input" type="password" id="login-password" name="password" placeholder="Tapez votre mot de passe" autocomplete="current-password" required>
                                <button type="button" class="form-field__eye" aria-label="Afficher ou masquer le mot de passe" aria-pressed="false">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.06 12.35a1 1 0 0 1 0-.7 10.75 10.75 0 0 1 19.88 0 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-19.88 0"/><circle cx="12" cy="12" r="3"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <a href="#" class="login-popup__forgot js-forgot">Mot de passe oublié ?</a>

                    <button type="submit" class="btn btn-primary login-popup__submit" disabled>Continuer</button>

                    <p class="login-popup__signup">Vous n'avez pas de compte ? <a href="<?php echo esc_url(home_url('/inscription/')); ?>">Inscription</a></p>
                </form>

                <!-- Écran « Mot de passe oublié » : projet fictif, aucun e-mail
                     n'est réellement envoyé, on confirme simplement la demande. -->
                <form class="login-popup__reset" novalidate aria-labelledby="reset-titre" hidden>
                    <h3 id="reset-titre" class="login-popup__reset-titre">Mot de passe oublié</h3>
                    <p class="login-popup__reset-intro">Indiquez votre adresse e-mail : nous vous enverrons un lien pour choisir un nouveau mot de passe.</p>
                    <div class="login-popup__fields">
                        <div class="form-field">
                            <label class="form-field__label" for="reset-email">Adresse e-mail</label>
                            <input class="form-field__input" type="email" id="reset-email" name="reset-email" placeholder="Tapez votre adresse e-mail" autocomplete="email" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary login-popup__reset-submit" disabled>Recevoir le lien</button>
                    <p class="login-popup__reset-confirmation" role="status" hidden></p>
                    <button type="button" class="login-popup__reset-retour js-back-login">Retour à la connexion</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bandeau cookies (contenu construit par main.js) -->
    <div class="cookies-banner" id="cookies-banner"></div>

    <?php wp_footer(); ?>
</body>
</html>
