<?php
/**
 * Gabarit de la page « Inscription » (contenu repris de inscription.html).
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 */
get_header();
?>

    <main id="contenu">
        <!-- Fil d'Ariane -->
        <div class="inscription-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><span class="is-current" aria-current="page">Inscription</span></li>
                </ol>
            </nav>
        </div>

        <!-- En mobile : titre, formulaire puis avantages empilés.
             En desktop : titre et avantages à gauche, formulaire à droite. -->
        <div class="inscription-layout">

            <!-- Titre et sous-titre -->
            <section class="inscription-pitch" aria-labelledby="inscription-titre">
                <h1 id="inscription-titre">Créez votre compte Pool Party</h1>
                <p class="inscription-pitch__sub">Un compte unique pour réserver un espace ou proposer le vôtre : piscines, jacuzzis, spas, saunas et hammams près de chez vous.</p>
            </section>

            <!-- Formulaire d'inscription (même patron de champs que la
                 pop-up de connexion, composant form-field de global.css) -->
            <section class="inscription-carte" aria-label="Formulaire d'inscription">
                <div class="inscription-carte__card">
                    <form class="inscription-form" id="inscription-form" novalidate>
                        <div class="inscription-form__fields">
                            <div class="inscription-form__row">
                                <div class="form-field">
                                    <label class="form-field__label" for="signup-prenom">Prénom</label>
                                    <input class="form-field__input" type="text" id="signup-prenom" name="prenom" placeholder="Entrez votre prénom" autocomplete="given-name" required>
                                </div>
                                <div class="form-field">
                                    <label class="form-field__label" for="signup-nom">Nom</label>
                                    <input class="form-field__input" type="text" id="signup-nom" name="nom" placeholder="Entrez votre nom" autocomplete="family-name" required>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="form-field__label" for="signup-email">Adresse e-mail</label>
                                <input class="form-field__input" type="email" id="signup-email" name="email" placeholder="Tapez votre adresse e-mail" autocomplete="email" required>
                                <p class="form-field__aide">Nous vous enverrons un code de confirmation par e-mail.</p>
                            </div>
                            <div class="form-field">
                                <label class="form-field__label" for="signup-password">Mot de passe</label>
                                <div class="form-field__wrap">
                                    <input class="form-field__input" type="password" id="signup-password" name="password" placeholder="8 caractères minimum" autocomplete="new-password" required>
                                    <button type="button" class="form-field__eye" aria-label="Afficher ou masquer le mot de passe" aria-pressed="false">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.06 12.35a1 1 0 0 1 0-.7 10.75 10.75 0 0 1 19.88 0 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-19.88 0"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <label class="inscription-form__cgu">
                            <input type="checkbox" id="signup-cgu" name="cgu">
                            <span>J'accepte les <a href="<?php echo esc_url(home_url('/cgu/')); ?>">conditions d'utilisation</a> et la <a href="<?php echo esc_url(home_url('/mentions-legales/')); ?>">politique de confidentialité</a>.</span>
                        </label>

                        <p class="inscription-manque" data-manque aria-live="polite" hidden></p>

                        <button type="submit" class="btn btn-primary inscription-form__submit" disabled>Créer mon compte</button>

                        <p class="inscription-form__connexion">Vous avez déjà un compte ? <a href="#" class="js-open-login">Connexion</a></p>
                    </form>

                    <!-- État de confirmation affiché par main.js après soumission -->
                    <div class="inscription-succes" id="inscription-succes" hidden>
                        <span class="inscription-succes__icone" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
                        </span>
                        <h2>Bienvenue sur Pool Party !</h2>
                        <p role="status">Votre compte est créé et vous êtes maintenant connecté. Il ne reste plus qu'à trouver votre prochain moment de détente.</p>
                        <div class="inscription-succes__actions">
                            <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="btn btn-primary">Explorer les espaces</a>
                            <a href="<?php echo esc_url(home_url('/proposer/')); ?>" class="btn btn-tertiary">Proposer mon espace</a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Avantages du compte -->
            <ul class="inscription-avantages">
                <li class="inscription-avantage">
                    <span class="inscription-avantage__icone" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/><path d="m9 16 2 2 4-4"/></svg>
                    </span>
                    <div>
                        <h2>Réservez en quelques clics</h2>
                        <p>Choisissez votre créneau, réglez en ligne et recevez la confirmation immédiatement.</p>
                    </div>
                </li>
                <li class="inscription-avantage">
                    <span class="inscription-avantage__icone" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <div>
                        <h2>Paiement sécurisé</h2>
                        <p>Vos règlements sont protégés et l'hôte n'est payé qu'après votre venue.</p>
                    </div>
                </li>
                <li class="inscription-avantage">
                    <span class="inscription-avantage__icone" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                    </span>
                    <div>
                        <h2>Annonces vérifiées</h2>
                        <p>Chaque espace est contrôlé par notre équipe avant sa mise en ligne.</p>
                    </div>
                </li>
            </ul>

        </div>
    </main>

<?php get_footer(); ?>
