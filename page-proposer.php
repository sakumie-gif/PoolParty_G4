<?php
/**
 * Gabarit de la page « Proposer votre espace » (contenu repris de proposer.html).
 * Parcours immersif : on utilise l'en-tête et le pied de page ALLÉGÉS du
 * tunnel (header-proposer.php / footer-proposer.php), pas le header et le
 * footer complets du site, pour rester fidèle au statique. Les styles
 * propres à la page sont chargés dans functions.php.
 */
get_header('proposer');
?>

    <!-- Cycle de vie cliquable : une étape par écran de contenu réel,
         construit par main.js (pastille numérotée + nom en dessous, coche
         quand l'étape est faite, étape courante mise en avant). On peut
         cliquer une étape déjà atteinte pour y revenir. -->
    <nav class="pp-steps" id="pp-steps" aria-label="Étapes de création de l'annonce"></nav>

    <main class="proposer" id="proposer-parcours">
        <h1 class="sr-only">Proposer mon espace : création de votre annonce</h1>

        <!-- Bandeau de reprise : affiché quand un brouillon enregistré
             via Enregistrer et quitter a été restauré au chargement -->
        <div class="proposer-reprise" hidden>
            <p>Votre brouillon a été restauré, reprenez où vous en étiez. Les photos restent à réimporter.</p>
            <button type="button" class="btn btn-tertiary btn-small" data-reprise="reset">Recommencer à zéro</button>
        </div>

        <!-- ===================================================
             ÉCRAN A : Création du compte
             =================================================== -->
        <section class="proposer-ecran" data-ecran="compte" data-etape="0" data-avancement="0">
            <div class="proposer-centre proposer-centre--etroit">
                <h2 class="proposer-titre">Créez votre compte Pool Party</h2>
                <p class="proposer-sous-titre">Quelques informations suffisent pour commencer. Vous pourrez compléter votre profil plus tard.</p>

                <div class="proposer-carte">
                <form class="proposer-form" novalidate>
                    <div class="proposer-row">
                        <div class="form-field">
                            <label class="form-field__label" for="compte-prenom">Prénom</label>
                            <input class="form-field__input" id="compte-prenom" name="prenom" type="text" placeholder="Entrez votre prénom" autocomplete="given-name" required>
                        </div>
                        <div class="form-field">
                            <label class="form-field__label" for="compte-nom">Nom</label>
                            <input class="form-field__input" id="compte-nom" name="nom" type="text" placeholder="Entrez votre nom" autocomplete="family-name" required>
                        </div>
                    </div>
                    <div class="form-field">
                        <label class="form-field__label" for="compte-email">Adresse e-mail</label>
                        <input class="form-field__input" id="compte-email" name="email" type="email" placeholder="Tapez votre adresse e-mail" autocomplete="email" required>
                        <p class="form-field__aide">Nous vous enverrons un code de confirmation par e-mail.</p>
                    </div>
                    <div class="form-field">
                        <label class="form-field__label" for="compte-password">Mot de passe</label>
                        <div class="form-field__wrap">
                            <input class="form-field__input" id="compte-password" name="password" type="password" placeholder="8 caractères minimum" autocomplete="new-password" required>
                            <button type="button" class="form-field__eye" aria-label="Afficher ou masquer le mot de passe" aria-pressed="false">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.06 12.35a1 1 0 0 1 0-.7 10.75 10.75 0 0 1 19.88 0 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-19.88 0"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                    </div>
                    <label class="checkbox">
                        <input type="checkbox" name="cgu" required>
                        <span>J'accepte les <a href="<?php echo esc_url(home_url('/cgu/')); ?>">conditions d'utilisation</a> et la <a href="<?php echo esc_url(home_url('/mentions-legales/')); ?>">politique de confidentialité</a>.</span>
                    </label>

                    <!-- Rempli par le JS : aide et liste ce qui bloque le bouton Continuer -->
                    <p class="proposer-manque" data-manque aria-live="polite" hidden></p>

                    <!-- Bouton d'avancement dans la carte : sur cet écran il
                         remplace le Continuer de la barre du bas, plus visible
                         au ras du formulaire. Piloté par le même code que la
                         navigation du tunnel (data-nav="suivant-carte"). -->
                    <button type="button" class="btn btn-primary proposer-carte__suivant" data-nav="suivant-carte" disabled>Continuer</button>
                </form>

                <p class="proposer-deja">Vous avez déjà un compte ? <a href="#" class="js-open-login">Connexion</a></p>
                </div>
            </div>
        </section>

        <!-- Écran 1.1 : type d'espace détaillé -->
        <section class="proposer-ecran" data-ecran="type" data-etape="1" data-avancement="25" hidden>
            <div class="proposer-centre">
                <h2 class="proposer-titre">Parmi les propositions suivantes, laquelle décrit le mieux votre espace ?</h2>
                <div class="choix-grid" data-choix-groupe="type">
                    <button type="button" class="choix-card" data-valeur="Piscine enterrée" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/></svg>
                        <span class="choix-card__titre">Piscine enterrée</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Piscine hors-sol" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/></svg>
                        <span class="choix-card__titre">Piscine hors-sol</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Piscine intérieure" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                        <span class="choix-card__titre">Piscine intérieure</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Piscine naturelle" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                        <span class="choix-card__titre">Piscine naturelle</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Jacuzzi" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="7" cy="9" r="1.4"/><circle cx="12" cy="6" r="1.4"/><circle cx="17" cy="9" r="1.4"/><path d="M2 15c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 19c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/></svg>
                        <span class="choix-card__titre">Jacuzzi</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Spa" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M12 16.5A4.5 4.5 0 1 1 7.5 12 4.5 4.5 0 1 1 12 7.5a4.5 4.5 0 1 1 4.5 4.5 4.5 4.5 0 1 1-4.5 4.5"/></svg>
                        <span class="choix-card__titre">Spa</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Sauna" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
                        <span class="choix-card__titre">Sauna</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Hammam" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M16 17H7"/><path d="M17 21H9"/></svg>
                        <span class="choix-card__titre">Hammam</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Bain nordique" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.7 7.7a2.5 2.5 0 1 1 1.8 4.3H2"/><path d="M9.6 4.6A2 2 0 1 1 11 8H2"/><path d="M12.6 19.4A2 2 0 1 0 14 16H2"/></svg>
                        <span class="choix-card__titre">Bain nordique</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Écran 1.2 : mode d'accès -->
        <section class="proposer-ecran" data-ecran="acces" data-etape="1" data-avancement="45" hidden>
            <div class="proposer-centre proposer-centre--etroit">
                <h2 class="proposer-titre">Comment les invités profiteront-ils de votre espace ?</h2>
                <div class="choix-liste" data-choix-groupe="acces">
                    <button type="button" class="choix-ligne" data-valeur="Espace entier privatisé" aria-pressed="false">
                        <span class="choix-ligne__texte">
                            <span class="choix-ligne__titre">Un espace entier privatisé</span>
                            <span class="choix-ligne__desc">Les invités disposent du bassin et de ses abords en exclusivité pendant leur créneau.</span>
                        </span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6"/><path d="m15.5 7.5 3 3L22 7l-3-3"/></svg>
                    </button>
                    <button type="button" class="choix-ligne" data-valeur="Espace partagé" aria-pressed="false">
                        <span class="choix-ligne__texte">
                            <span class="choix-ligne__titre">Un espace partagé</span>
                            <span class="choix-ligne__desc">Vous ou d'autres invités pouvez être présents pendant le créneau réservé.</span>
                        </span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- Écran 1.3 : adresse -->
        <section class="proposer-ecran" data-ecran="adresse" data-etape="1" data-avancement="65" hidden>
            <div class="proposer-centre proposer-centre--etroit">
                <h2 class="proposer-titre">Où se situe votre espace ?</h2>
                <p class="proposer-sous-titre">Votre adresse est uniquement communiquée aux invités une fois leur réservation confirmée.</p>

                <form class="proposer-form" novalidate>
                    <div class="form-field">
                        <label class="form-field__label" for="adresse-pays">Pays / région</label>
                        <input class="form-field__input form-field__input--verrouille" id="adresse-pays" name="pays" type="text" value="France - FR" readonly aria-describedby="adresse-pays-aide">
                        <p class="form-field__aide" id="adresse-pays-aide">Pool Party n'est disponible qu'en France pour le moment.</p>
                    </div>
                    <div class="form-field">
                        <label class="form-field__label" for="adresse-complement">Bâtiment, résidence, étage (si applicable)</label>
                        <input class="form-field__input" id="adresse-complement" name="complement" type="text" placeholder="Complément d'adresse" autocomplete="off">
                    </div>
                    <div class="form-field">
                        <label class="form-field__label" for="adresse-voie">Numéro et libellé de voie</label>
                        <input class="form-field__input" id="adresse-voie" name="voie" type="text" placeholder="15 Route de Villiers" autocomplete="off" required>
                    </div>
                    <div class="proposer-row">
                        <div class="form-field">
                            <label class="form-field__label" for="adresse-cp">Code postal</label>
                            <input class="form-field__input" id="adresse-cp" name="cp" type="text" inputmode="numeric" placeholder="93160" autocomplete="off" required>
                        </div>
                        <div class="form-field">
                            <label class="form-field__label" for="adresse-commune">Commune</label>
                            <input class="form-field__input" id="adresse-commune" name="commune" type="text" placeholder="Noisy-le-Grand" autocomplete="off" required>
                        </div>
                    </div>
                </form>

                <picture>
                    <source srcset="<?php echo esc_url(pp_asset('assets/images/produit/map.webp')); ?>" type="image/webp">
                    <img class="proposer-map" src="<?php echo esc_url(pp_asset('assets/images/produit/map.png')); ?>" width="1760" height="480" loading="lazy" decoding="async" alt="Carte de localisation de votre espace, le repère sera ajusté après validation de l'adresse">
                </picture>
            </div>
        </section>

        <!-- Écran 1.4 : capacité -->
        <section class="proposer-ecran" data-ecran="capacite" data-etape="1" data-avancement="90" hidden>
            <div class="proposer-centre proposer-centre--etroit">
                <h2 class="proposer-titre">Donnez les informations principales concernant votre espace</h2>
                <p class="proposer-sous-titre">Vous pourrez ajouter d'autres informations plus tard, comme la température ou les dimensions du bassin.</p>

                <ul class="compteur-liste">
                    <li class="compteur-ligne">
                        <span class="compteur-ligne__label">Invités maximum</span>
                        <div class="compteur" data-compteur="invites" data-min="1" data-max="30">
                            <button type="button" class="compteur__btn" data-action="moins" aria-label="Retirer un invité">-</button>
                            <span class="compteur__valeur" aria-live="polite">8</span>
                            <button type="button" class="compteur__btn" data-action="plus" aria-label="Ajouter un invité">+</button>
                        </div>
                    </li>
                    <li class="compteur-ligne">
                        <span class="compteur-ligne__label">Vestiaires</span>
                        <div class="compteur" data-compteur="vestiaires" data-min="0" data-max="10">
                            <button type="button" class="compteur__btn" data-action="moins" aria-label="Retirer un vestiaire">-</button>
                            <span class="compteur__valeur" aria-live="polite">1</span>
                            <button type="button" class="compteur__btn" data-action="plus" aria-label="Ajouter un vestiaire">+</button>
                        </div>
                    </li>
                    <li class="compteur-ligne">
                        <span class="compteur-ligne__label">Salles d'eau accessibles</span>
                        <div class="compteur" data-compteur="salles-eau" data-min="0" data-max="10">
                            <button type="button" class="compteur__btn" data-action="moins" aria-label="Retirer une salle d'eau">-</button>
                            <span class="compteur__valeur" aria-live="polite">1</span>
                            <button type="button" class="compteur__btn" data-action="plus" aria-label="Ajouter une salle d'eau">+</button>
                        </div>
                    </li>
                </ul>
            </div>
        </section>

        <!-- Écran 2.1 : équipements -->
        <section class="proposer-ecran" data-ecran="equipements" data-etape="2" data-avancement="25" hidden>
            <div class="proposer-centre">
                <h2 class="proposer-titre">Indiquez aux invités les équipements de votre espace</h2>
                <p class="proposer-sous-titre">Vous pourrez en ajouter d'autres une fois votre annonce publiée.</p>

                <h3 class="proposer-groupe-titre">Équipements de confort</h3>
                <div class="choix-grid" data-choix-groupe="equipements" data-multi="true">
                    <button type="button" class="choix-card" data-valeur="Chauffage du bassin" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 4v10.54a4 4 0 1 1-4 0V4a2 2 0 0 1 4 0Z"/></svg>
                        <span class="choix-card__titre">Chauffage du bassin</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Éclairage nocturne" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5.76.76 1.23 1.52 1.41 2.5"/></svg>
                        <span class="choix-card__titre">Éclairage nocturne</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Transats" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 19h18"/><path d="M5 19V8l11 6"/><path d="M16 14v5"/></svg>
                        <span class="choix-card__titre">Transats</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Parasol" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 12a10.06 10.06 0 0 0-20 0Z"/><path d="M12 12v8a2 2 0 0 0 4 0"/><path d="M12 2v1"/></svg>
                        <span class="choix-card__titre">Parasol</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Douche extérieure" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v4"/><path d="M7 11a5 5 0 0 1 10 0"/><path d="M7 15v1M10 15v2M14 15v2M17 15v1"/></svg>
                        <span class="choix-card__titre">Douche extérieure</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Vestiaire" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.38 3.46 16 2a4 4 0 0 1-8 0L3.62 3.46a2 2 0 0 0-1.34 2.23l.58 3.47a1 1 0 0 0 .99.84H6v10a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V10h2.15a1 1 0 0 0 .99-.84l.58-3.47a2 2 0 0 0-1.34-2.23z"/></svg>
                        <span class="choix-card__titre">Vestiaire</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Barbecue" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 8h16"/><path d="M6 8a6 6 0 0 0 12 0"/><path d="M12 14v4"/><path d="m8 21 1-3"/><path d="m16 21-1-3"/></svg>
                        <span class="choix-card__titre">Barbecue</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Wifi" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13a10 10 0 0 1 14 0"/><path d="M8.5 16.5a5 5 0 0 1 7 0"/><path d="M2 8.82a15 15 0 0 1 20 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
                        <span class="choix-card__titre">Wifi</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Parking gratuit" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M9 17h6"/></svg>
                        <span class="choix-card__titre">Parking gratuit</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Serviettes fournies" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 12.18-8.58 3.91a2 2 0 0 1-1.66 0L3.18 12.18"/></svg>
                        <span class="choix-card__titre">Serviettes fournies</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Enceinte bluetooth" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="6" y="2" width="12" height="20" rx="2"/><circle cx="12" cy="14" r="4"/><line x1="12" y1="6" x2="12.01" y2="6"/></svg>
                        <span class="choix-card__titre">Enceinte bluetooth</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Rafraîchissements" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 8 1.75 12.28a2 2 0 0 0 2 1.72h4.54a2 2 0 0 0 2-1.72L18 8"/><path d="M5 8h14"/><path d="M7 15a6.47 6.47 0 0 1 5 0 6.47 6.47 0 0 0 5 0"/><path d="m12 8 1-6h2"/></svg>
                        <span class="choix-card__titre">Rafraîchissements</span>
                    </button>
                </div>

                <h3 class="proposer-groupe-titre">Équipements de sécurité</h3>
                <div class="choix-grid" data-choix-groupe="securite" data-multi="true">
                    <button type="button" class="choix-card" data-valeur="Barrière de sécurité" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 21V8l2-3 2 3v13"/><path d="M12 21V8l2-3 2 3v13"/><path d="M2 12h20"/><path d="M2 17h20"/></svg>
                        <span class="choix-card__titre">Barrière de sécurité</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Alarme immergée" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10.268 21a2 2 0 0 0 3.464 0"/><path d="M3.262 15.326A1 1 0 0 0 4 17h16a1 1 0 0 0 .74-1.673C19.41 13.956 18 12.499 18 8A6 6 0 0 0 6 8c0 4.499-1.411 5.956-2.738 7.326"/></svg>
                        <span class="choix-card__titre">Alarme immergée</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Bâche ou volet de sécurité" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
                        <span class="choix-card__titre">Bâche ou volet de sécurité</span>
                    </button>
                    <button type="button" class="choix-card" data-valeur="Trousse de secours" aria-pressed="false">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M12 10v6"/><path d="M9 13h6"/><path d="M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/></svg>
                        <span class="choix-card__titre">Trousse de secours</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Écran 2.2 : photos -->
        <section class="proposer-ecran" data-ecran="photos" data-etape="2" data-avancement="50" hidden>
            <div class="proposer-centre proposer-centre--etroit">
                <h2 class="proposer-titre">Ajoutez quelques photos de votre espace</h2>
                <p class="proposer-sous-titre">Pour commencer, vous aurez besoin de 5 photos. Vous pourrez en ajouter d'autres ou faire des modifications plus tard.</p>

                <label class="photos-zone">
                    <input type="file" class="sr-only" id="photos-input" accept=".jpg,.jpeg,.png,.webp" multiple>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                    <span class="photos-zone__titre">Glissez-déposez vos photos</span>
                    <span class="photos-zone__texte">ou</span>
                    <span class="btn btn-secondary btn-small">Parcourir</span>
                </label>

                <p class="photos-compteur" aria-live="polite">0 photo ajoutée sur 5 minimum</p>
                <ul class="photos-grid" aria-label="Photos ajoutées, la première sert de photo de couverture"></ul>
            </div>
        </section>

        <!-- Écran 2.3 : titre -->
        <section class="proposer-ecran" data-ecran="titre" data-etape="2" data-avancement="70" hidden>
            <div class="proposer-centre proposer-centre--etroit">
                <h2 class="proposer-titre">À présent, donnez un titre à votre annonce<span class="form-field__requis" aria-hidden="true">*</span></h2>
                <p class="proposer-sous-titre">Les titres courts sont généralement les plus efficaces. Ne vous inquiétez pas, vous pourrez toujours le modifier.</p>

                <div class="form-field">
                    <label class="sr-only" for="annonce-titre">Titre de l'annonce</label>
                    <textarea class="form-field__input proposer-textarea proposer-textarea--titre" id="annonce-titre" maxlength="50" placeholder="Piscine chauffée avec vue sur le jardin" aria-required="true"></textarea>
                    <p class="proposer-compteur"><span data-compteur-texte="annonce-titre">0</span>/50 caractères</p>
                </div>
            </div>
        </section>

        <!-- Écran 2.4 : description -->
        <section class="proposer-ecran" data-ecran="description" data-etape="2" data-avancement="90" hidden>
            <div class="proposer-centre proposer-centre--etroit">
                <h2 class="proposer-titre">Rédigez votre description<span class="form-field__requis" aria-hidden="true">*</span></h2>
                <p class="proposer-sous-titre">Racontez ce qui rend votre espace unique : l'ambiance, le cadre, les petites attentions qui font la différence.</p>

                <div class="form-field">
                    <label class="sr-only" for="annonce-description">Description de l'annonce</label>
                    <textarea class="form-field__input proposer-textarea" id="annonce-description" maxlength="500" placeholder="Détendez-vous au bord de notre piscine chauffée à 28 degrés, entourée d'un jardin arboré..." aria-required="true"></textarea>
                    <p class="proposer-compteur"><span data-compteur-texte="annonce-description">0</span>/500 caractères</p>
                </div>
            </div>
        </section>

        <!-- Écran 3.1 : mode de réservation -->
        <section class="proposer-ecran" data-ecran="reservation" data-etape="3" data-avancement="15" hidden>
            <div class="proposer-centre proposer-centre--etroit">
                <h2 class="proposer-titre">Comment souhaitez-vous accueillir vos premières réservations ?</h2>
                <div class="choix-liste" data-choix-groupe="reservation">
                    <button type="button" class="choix-ligne" data-valeur="Réservation instantanée" aria-pressed="false">
                        <span class="choix-ligne__texte">
                            <span class="choix-ligne__titre">Réservation instantanée</span>
                            <span class="choix-ligne__desc">Les invités réservent directement un créneau disponible, sans attendre votre accord.</span>
                        </span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    </button>
                    <button type="button" class="choix-ligne" data-valeur="Validation manuelle" aria-pressed="false">
                        <span class="choix-ligne__texte">
                            <span class="choix-ligne__titre">Validation manuelle</span>
                            <span class="choix-ligne__desc">Vous examinez chaque demande avant de l'accepter. Idéal pour vos premières locations.</span>
                        </span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                    </button>
                </div>
            </div>
        </section>

        <!-- Écran 3.2 : disponibilités -->
        <section class="proposer-ecran" data-ecran="disponibilites" data-etape="3" data-avancement="30" hidden>
            <div class="proposer-centre proposer-centre--etroit">
                <h2 class="proposer-titre">Quand votre espace est-il disponible ?</h2>
                <p class="proposer-sous-titre">Choisissez vos jours d'accueil et votre plage horaire habituelle. Vous pourrez affiner créneau par créneau depuis votre calendrier une fois l'annonce publiée.</p>

                <h3 class="proposer-groupe-titre">Jours d'accueil</h3>
                <div class="jours-liste" data-choix-groupe="jours" data-multi="true">
                    <button type="button" class="jour-pill" data-valeur="Lundi" aria-pressed="false">Lun</button>
                    <button type="button" class="jour-pill" data-valeur="Mardi" aria-pressed="false">Mar</button>
                    <button type="button" class="jour-pill" data-valeur="Mercredi" aria-pressed="false">Mer</button>
                    <button type="button" class="jour-pill" data-valeur="Jeudi" aria-pressed="false">Jeu</button>
                    <button type="button" class="jour-pill" data-valeur="Vendredi" aria-pressed="false">Ven</button>
                    <button type="button" class="jour-pill" data-valeur="Samedi" aria-pressed="false">Sam</button>
                    <button type="button" class="jour-pill" data-valeur="Dimanche" aria-pressed="false">Dim</button>
                </div>

                <h3 class="proposer-groupe-titre">Plage horaire</h3>
                <div class="dispo-horaire">
                    <p class="range-double__value"></p>
                    <div class="range-double">
                        <span class="range-double__fill"></span>
                        <input type="range" id="dispo-debut" class="range-double__input" data-borne="debut" min="6" max="24" step="1" value="9" aria-label="Heure de début">
                        <input type="range" id="dispo-fin" class="range-double__input" data-borne="fin" min="6" max="24" step="1" value="19" aria-label="Heure de fin">
                    </div>
                </div>
            </div>
        </section>

        <!-- Écran 3.3 : prix -->
        <section class="proposer-ecran" data-ecran="prix" data-etape="3" data-avancement="45" hidden>
            <div class="proposer-centre proposer-centre--etroit">
                <h2 class="proposer-titre">À présent, fixez votre prix par heure</h2>
                <p class="proposer-sous-titre">Vous pourrez le modifier à tout moment. Les espaces comparables au vôtre se louent entre 20 et 35 euros de l'heure.</p>

                <div class="prix-saisie">
                    <label class="sr-only" for="annonce-prix">Prix par heure en euros</label>
                    <input class="prix-saisie__input" id="annonce-prix" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="3" autocomplete="off" value="25">
                    <span class="prix-saisie__unite">€ / heure</span>
                </div>

                <dl class="prix-detail">
                    <div class="prix-detail__ligne">
                        <dt>Votre revenu par heure</dt>
                        <dd data-prix="revenu">25 €</dd>
                    </div>
                    <div class="prix-detail__ligne">
                        <dt>Prix affiché aux invités, frais de service de 15 % inclus</dt>
                        <dd data-prix="affiche">29 €</dd>
                    </div>
                </dl>
            </div>
        </section>

        <!-- Écran 3.4 : règles et sécurité -->
        <section class="proposer-ecran" data-ecran="regles" data-etape="3" data-avancement="60" hidden>
            <div class="proposer-centre proposer-centre--etroit">
                <h2 class="proposer-titre">Définissez vos règles</h2>
                <p class="proposer-sous-titre">Les invités s'engagent à respecter vos règles au moment de la réservation.</p>

                <ul class="regles-liste">
                    <li class="regles-ligne">
                        <label class="toggle"><input type="checkbox" name="regle-musique" checked><span class="toggle__track"></span> Musique autorisée</label>
                    </li>
                    <li class="regles-ligne">
                        <label class="toggle"><input type="checkbox" name="regle-animaux"><span class="toggle__track"></span> Animaux acceptés</label>
                    </li>
                    <li class="regles-ligne">
                        <label class="toggle"><input type="checkbox" name="regle-enfants" checked><span class="toggle__track"></span> Enfants bienvenus, sous la surveillance d'un adulte</label>
                    </li>
                    <li class="regles-ligne">
                        <label class="toggle"><input type="checkbox" name="regle-repas"><span class="toggle__track"></span> Repas et boissons autorisés au bord du bassin</label>
                    </li>
                </ul>

                <div class="regles-attestation">
                    <label class="checkbox">
                        <input type="checkbox" id="attestation-securite" required>
                        <span>J'atteste que mon bassin dispose d'au moins un dispositif de sécurité conforme à la réglementation française : barrière, alarme, couverture ou abri (norme NF P90).</span>
                    </label>
                </div>
            </div>
        </section>

        <!-- Écran 3.5 : versement des revenus -->
        <section class="proposer-ecran" data-ecran="versement" data-etape="3" data-avancement="80" hidden>
            <div class="proposer-centre proposer-centre--etroit">
                <h2 class="proposer-titre">Comment souhaitez-vous être payé ?</h2>
                <p class="proposer-sous-titre">Vos revenus sont versés par virement 24 heures après chaque créneau. Les paiements passent par Stripe : vos coordonnées bancaires ne sont jamais communiquées aux invités.</p>

                <form class="proposer-form" novalidate>
                    <div class="form-field">
                        <label class="form-field__label" for="versement-titulaire">Titulaire du compte</label>
                        <input class="form-field__input" id="versement-titulaire" name="titulaire" type="text" placeholder="Prénom et nom du titulaire" autocomplete="name" required>
                    </div>
                    <div class="form-field">
                        <label class="form-field__label" for="versement-iban">IBAN</label>
                        <input class="form-field__input" id="versement-iban" name="iban" type="text" placeholder="FR76 0000 0000 0000 0000 0000 000" autocomplete="off" spellcheck="false" required>
                        <p class="form-field__aide">Compte bancaire domicilié en France ou dans la zone SEPA.</p>
                    </div>
                </form>

                <ul class="reassurance-list versement-garanties">
                    <li class="reassurance-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Coordonnées chiffrées et stockées par Stripe
                    </li>
                    <li class="reassurance-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        Virement sous 24 heures après chaque créneau
                    </li>
                </ul>
            </div>
        </section>

        <!-- Écran 3.6 : récapitulatif avant publication -->
        <section class="proposer-ecran" data-ecran="recap" data-etape="3" data-avancement="90" hidden>
            <div class="proposer-centre">
                <h2 class="proposer-titre">Vérifiez votre annonce</h2>
                <p class="proposer-sous-titre">Voici ce que les invités verront. Assurez-vous que tout vous convient avant de publier.</p>

                <div class="recap-layout">
                    <article class="card-product recap-card">
                        <div class="card-product__media">
                            <img src="<?php echo esc_url(pp_asset('assets/images/piscines/annonce-lagny.jpg')); ?>" alt="Photo de couverture de votre annonce" data-recap="photo" loading="lazy" decoding="async" width="1024" height="728">
                        </div>
                        <span class="tag tag--nouveau card-product__tag">Nouveau</span>
                        <div class="card-product__body">
                            <div class="card-product__head">
                                <h3 class="card-product__title" data-recap="titre">Votre annonce</h3>
                            </div>
                            <p class="card-product__meta"><span data-recap="commune">Commune</span><span data-recap="type">Type d'espace</span></p>
                            <p class="card-product__meta"><span data-recap="invites">8 personnes</span></p>
                            <p class="card-product__price" data-recap="prix">25 €/ h</p>
                        </div>
                    </article>

                    <div class="recap-suite">
                        <h3 class="proposer-groupe-titre">Et ensuite ?</h3>
                        <ul class="recap-etapes">
                            <li class="recap-etape">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                                <div>
                                    <p class="recap-etape__titre">Nous vérifions votre annonce</p>
                                    <p class="recap-etape__texte">Notre équipe contrôle chaque nouvelle annonce sous 24 heures avant sa mise en avant.</p>
                                </div>
                            </li>
                            <li class="recap-etape">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 9h18"/></svg>
                                <div>
                                    <p class="recap-etape__titre">Vos créneaux sont prêts</p>
                                    <p class="recap-etape__texte">Les jours et horaires choisis s'affichent sur votre annonce, ajustables à tout moment depuis votre calendrier.</p>
                                </div>
                            </li>
                            <li class="recap-etape">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/><path d="M8 12h.01M12 12h.01M16 12h.01"/></svg>
                                <div>
                                    <p class="recap-etape__titre">Vous recevez vos premières demandes</p>
                                    <p class="recap-etape__texte">Les paiements sont sécurisés via Stripe et versés 24 heures après chaque créneau.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Écran 3.7 : confirmation de publication -->
        <section class="proposer-ecran" data-ecran="fin" data-etape="3" data-avancement="100" hidden>
            <div class="proposer-centre proposer-centre--etroit proposer-fin">
                <div class="proposer-fin__badge" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
                <h2 class="proposer-titre">Félicitations, votre annonce est en ligne&nbsp;!</h2>
                <p class="proposer-sous-titre">Notre équipe la vérifie sous 24 heures. Vous recevrez un e-mail dès qu'elle sera visible dans les résultats de recherche. En attendant, vous pouvez ouvrir votre calendrier et peaufiner votre profil d'hôte.</p>
                <div class="proposer-fin__actions">
                    <button type="button" class="btn btn-primary btn-medium" data-apercu-ouvrir>Voir mon annonce</button>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-tertiary btn-medium">Retour à l'accueil</a>
                </div>
                <p class="proposer-fin__note">Projet étudiant fictif : aucune annonce n'est réellement publiée.</p>
            </div>
        </section>
    </main>

    <!-- Barre de navigation du tunnel : Retour et Continuer.
         Le cycle de vie des 3 étapes est affiché en haut de la page. -->
    <div class="proposer-bar">
        <div class="proposer-bar__nav">
            <button type="button" class="proposer-bar__retour" data-nav="retour" hidden>Retour</button>
            <p class="proposer-bar__mention">Projet étudiant fictif : aucune annonce réelle ne peut être publiée.</p>
            <button type="button" class="btn btn-primary proposer-bar__suivant" data-nav="suivant" disabled>Continuer</button>
        </div>
    </div>

    <!-- Aperçu de l'annonce : ouvert par « Voir mon annonce » sur l'écran
         de confirmation. Comme le projet est fictif (aucune annonce n'est
         réellement publiée), on montre ici la fiche telle que la verront
         les invités, construite par main.js à partir des données saisies,
         plutôt que de renvoyer vers le catalogue. -->
    <div class="popup-overlay proposer-apercu" id="apercu-annonce" hidden>
        <div class="popup proposer-apercu__popup" role="dialog" aria-modal="true" aria-labelledby="apercu-annonce-titre">
            <button type="button" class="popup__close" aria-label="Fermer l'aperçu"></button>
            <h2 class="popup__title" id="apercu-annonce-titre">Aperçu de votre annonce</h2>
            <p class="proposer-apercu__intro">Voici la fiche que verront les invités.</p>
            <div class="popup__body proposer-apercu__corps">
                <article class="card-product proposer-apercu__carte">
                    <div class="card-product__media">
                        <img src="<?php echo esc_url(pp_asset('assets/images/piscines/annonce-lagny.jpg')); ?>" alt="Photo de couverture de votre annonce" data-apercu="photo" loading="lazy" decoding="async" width="1024" height="728">
                    </div>
                    <span class="tag tag--nouveau card-product__tag">Nouveau</span>
                    <div class="card-product__body">
                        <div class="card-product__head">
                            <h3 class="card-product__title" data-apercu="titre">Votre annonce</h3>
                        </div>
                        <p class="card-product__meta"><span data-apercu="commune">Commune</span><span data-apercu="type">Type d'espace</span></p>
                        <p class="card-product__meta"><span data-apercu="invites">8 personnes max</span></p>
                        <p class="card-product__price" data-apercu="prix">25 €/ h</p>
                    </div>
                </article>

                <div class="proposer-apercu__details">
                    <section class="proposer-apercu__bloc" data-apercu="description-bloc" hidden>
                        <h3 class="proposer-groupe-titre">Description</h3>
                        <p data-apercu="description"></p>
                    </section>
                    <section class="proposer-apercu__bloc" data-apercu="equipements-bloc" hidden>
                        <h3 class="proposer-groupe-titre">Équipements</h3>
                        <ul class="proposer-apercu__equipements" data-apercu="equipements"></ul>
                    </section>
                </div>
            </div>
        </div>
    </div>

<?php get_footer('proposer'); ?>
