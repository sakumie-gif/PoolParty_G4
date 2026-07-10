<?php
/**
 * Pied de page commun : footer 5 colonnes, pop-up de connexion,
 * bandeau cookies et scripts WordPress. Markup repris tel quel
 * de index.html, seules les URL passent par WordPress.
 */
?>

    <!-- ============================================================
         FOOTER commun a toutes les pages
         ============================================================ -->
    <footer class="site-footer">
        <div class="footer-main">

            <!-- Marque -->
            <div class="footer-brand">
                <img src="<?php echo esc_url(pp_asset('assets/images/logo/logo-full-white.png')); ?>" alt="Pool Party" class="footer-logo" loading="lazy" decoding="async" width="459" height="174">
                <p class="footer-desc">La plateforme qui réunit propriétaires de piscines et amateurs de baignade dans toute la France.</p>
                <ul class="footer-social">
                    <li><a href="https://www.whatsapp.com/" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp (nouvel onglet)"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 4a8 8 0 0 0-6.8 12.2L4 20l3.9-1.1A8 8 0 1 0 12 4zm0 1.6a6.4 6.4 0 0 1 5.4 9.8l-.3.5.5 1.9-2-.5-.4.2A6.4 6.4 0 1 1 12 5.6zm-2.5 3c-.2 0-.4 0-.6.3-.2.3-.7.7-.7 1.7s.7 2 .8 2.1c.1.2 1.4 2.3 3.5 3.1 1.7.7 2.1.6 2.5.5.4 0 1.2-.5 1.3-1 .2-.5.2-.9.1-1l-.6-.3s-1.2-.6-1.4-.6c-.2-.1-.3-.1-.5.1l-.6.8c-.1.1-.2.1-.4 0-.2-.1-.9-.3-1.7-1-.6-.6-1-1.2-1.2-1.4-.1-.2 0-.3.1-.4l.3-.4c.1-.1.1-.3.2-.4 0-.1 0-.3 0-.4 0-.1-.5-1.3-.7-1.7-.1-.3-.3-.3-.4-.3z"/></svg></a></li>
                    <li><a href="https://www.instagram.com/pool.partyfr/" target="_blank" rel="noopener noreferrer" aria-label="Instagram (nouvel onglet)"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg></a></li>
                    <li><a href="https://www.facebook.com/profile.php?id=61591946695522" target="_blank" rel="noopener noreferrer" aria-label="Facebook (nouvel onglet)"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13 22v-8h2.7l.4-3H13V9.2c0-.9.3-1.5 1.6-1.5H16V5.1C15.7 5 14.8 5 13.8 5 11.6 5 10 6.3 10 8.9V11H7.5v3H10v8h3z"/></svg></a></li>
                    <li><a href="https://www.tiktok.com/@pool_partyfr" target="_blank" rel="noopener noreferrer" aria-label="TikTok (nouvel onglet)"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16 4c.3 2 1.7 3.6 3.7 3.9v2.6c-1.4 0-2.7-.4-3.7-1.1v5.8c0 3-2.4 5.3-5.3 5.3S5.3 18.1 5.3 15.2 7.7 9.9 10.7 9.9c.3 0 .6 0 .9.1v2.7c-.3-.1-.6-.2-.9-.2-1.5 0-2.6 1.2-2.6 2.7s1.2 2.7 2.6 2.7c1.5 0 2.7-1.2 2.7-2.7V4H16z"/></svg></a></li>
                    <li><a href="https://www.pinterest.fr/" target="_blank" rel="noopener noreferrer" aria-label="Pinterest (nouvel onglet)"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 4C7.6 4 5 6.9 5 10.1c0 1.5.8 3.3 2.1 3.9.2.1.3 0 .4-.2 0-.1.1-.5.2-.7 0-.2 0-.3-.1-.4-.4-.5-.7-1.3-.7-2.1 0-2.1 1.6-4 4.4-4 2.4 0 3.7 1.4 3.7 3.3 0 2.5-1.1 4.6-2.7 4.6-.9 0-1.6-.7-1.4-1.7.3-1.1.8-2.3.8-3 0-.7-.4-1.3-1.2-1.3-1 0-1.7 1-1.7 2.3 0 .9.3 1.4.3 1.4l-1.2 5c-.2 1.3-.1 2.9 0 3.1.1.1.2.1.3 0 .1-.1 1.4-1.8 1.9-3.4.1-.4.7-2.6.7-2.6.3.6 1.3 1.1 2.3 1.1 3 0 5.1-2.8 5.1-6.2C19 6.7 16.1 4 12 4z"/></svg></a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="footer-col">
                <h3 class="footer-title">Contact</h3>
                <ul>
                    <li>12 rue de Rivoli, 75001 Paris</li>
                    <li><a href="tel:+33123456789">01 23 45 67 89</a></li>
                    <li><a href="mailto:contact@poolparty.fr">contact@poolparty.fr</a></li>
                </ul>
            </div>

            <!-- Service client : liste gérable depuis Apparence > Menus,
                 repli sur les liens du statique tant que le menu n'existe pas -->
            <div class="footer-col">
                <h3 class="footer-title">Service client</h3>
                <?php if (has_nav_menu('footer_service')) : ?>
                    <?php wp_nav_menu(array('theme_location' => 'footer_service', 'container' => false, 'items_wrap' => '<ul>%3$s</ul>')); ?>
                <?php else : ?>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a></li>
                    <li><a href="<?php echo esc_url(home_url('/faq/')); ?>">FAQ</a></li>
                    <li><a href="<?php echo esc_url(home_url('/moyen-de-paiement/')); ?>">Moyen de paiement</a></li>
                    <li><a href="<?php echo esc_url(home_url('/assurance/')); ?>">Assurances</a></li>
                    <li><a href="<?php echo esc_url(home_url('/accessibilite/')); ?>">Accessibilité</a></li>
                    <li><a href="<?php echo esc_url(home_url('/a-propos/')); ?>">À propos</a></li>
                </ul>
                <?php endif; ?>
            </div>

            <!-- En savoir plus : liste gérable depuis Apparence > Menus -->
            <div class="footer-col">
                <h3 class="footer-title">En savoir plus</h3>
                <?php if (has_nav_menu('footer_savoir')) : ?>
                    <?php wp_nav_menu(array('theme_location' => 'footer_savoir', 'container' => false, 'items_wrap' => '<ul>%3$s</ul>')); ?>
                <?php else : ?>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/partenaires/')); ?>">Partenaires</a></li>
                    <li><a href="<?php echo esc_url(home_url('/devenir-partenaire/')); ?>">Devenir Partenaire</a></li>
                    <li><a href="<?php echo esc_url(home_url('/presse/')); ?>">Presse</a></li>
                    <li><a href="<?php echo esc_url(home_url('/presse/#kit-presse')); ?>">Kit presse</a></li>
                </ul>
                <?php endif; ?>
            </div>

            <!-- Mentions légales : liste gérable depuis Apparence > Menus -->
            <div class="footer-col">
                <h3 class="footer-title">Mentions légales</h3>
                <?php if (has_nav_menu('footer_legal')) : ?>
                    <?php wp_nav_menu(array('theme_location' => 'footer_legal', 'container' => false, 'items_wrap' => '<ul>%3$s</ul>')); ?>
                <?php else : ?>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/paiement-securise/')); ?>">Paiement sécurisé</a></li>
                    <li><a href="<?php echo esc_url(home_url('/cgv/')); ?>">CGV (Conditions générales de vente)</a></li>
                    <li><a href="<?php echo esc_url(home_url('/cgu/')); ?>">CGU (Conditions d'utilisation)</a></li>
                    <li><a href="<?php echo esc_url(home_url('/mentions-legales/')); ?>">Politique de confidentialité</a></li>
                    <li><a href="<?php echo esc_url(home_url('/mentions-legales/#cookies')); ?>">Politique des cookies</a></li>
                </ul>
                <?php endif; ?>
            </div>

        </div>

        <div class="footer-bottom">
            <p>© 2026 Pool Party SARL : projet étudiant fictif, aucune réservation réelle ne peut être effectuée.</p>
        </div>
    </footer>

    <!-- Pop-up de connexion (design Figma : Pop up login).
         Ouvert depuis le lien Connexion du menu, fermé par la
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
