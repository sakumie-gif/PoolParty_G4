<?php
/**
 * Page Réservation : tunnel « Confirmer et payer » (checkout).
 * Markup repris tel quel de reservation.html du site statique ; seules
 * les URL (assets, liens internes) passent par WordPress. Le panneau
 * de réservation de la fiche produit (single-bien.php) redirige ici en
 * passant la formule, la date, le créneau et les invités dans l'URL ;
 * main.js (.checkout) préremplit le récapitulatif à partir de ces
 * paramètres. Page transactionnelle : noindex (voir functions.php).
 */

get_header();

$catalogue = esc_url(get_post_type_archive_link('bien'));

// Bien concerné par la réservation, transmis par la fiche produit via
// l'URL (?bien_id=ID). Sert à afficher le bon espace dans le récapitulatif
// et à nommer l'annonce dans l'e-mail envoyé à l'hôte. À défaut (accès
// direct à la page), on garde l'exemple de la maquette. Le paramètre est
// nommé « bien_id » et non « bien » : « bien » est le query var réservé
// du type de contenu, ce qui provoquerait une 404 sur cette page.
$pp_bien_id = isset($_GET['bien_id']) ? absint($_GET['bien_id']) : 0;
$pp_bien_ok = ($pp_bien_id
    && get_post_type($pp_bien_id) === 'bien'
    && get_post_status($pp_bien_id) === 'publish');

if ($pp_bien_ok) {
    $pp_r_titre = get_the_title($pp_bien_id);
    $pp_r_image = poolparty_g4_image_url($pp_bien_id);
    $pp_r_alt   = poolparty_g4_meta($pp_bien_id, 'alt');
    $pp_r_note  = str_replace('.', ',', poolparty_g4_meta($pp_bien_id, 'note'));
    $pp_r_avis  = poolparty_g4_meta($pp_bien_id, 'nb_avis');
    $pp_r_lien  = get_permalink($pp_bien_id);
    $pp_r_hote_data = poolparty_g4_get_hote(poolparty_g4_meta($pp_bien_id, 'id_hote'));
    $pp_r_hote  = ($pp_r_hote_data && !empty($pp_r_hote_data['prenom'])) ? $pp_r_hote_data['prenom'] : 'votre hôte';
} else {
    $pp_r_titre = 'Piscine avec terrasse à Boulogne-Billancourt';
    $pp_r_image = pp_asset('assets/images/piscines/annonce-pantin.jpg');
    $pp_r_alt   = 'La piscine et sa terrasse en bois au milieu du jardin paysager';
    $pp_r_note  = '4,6';
    $pp_r_avis  = '19';
    $pp_r_lien  = get_post_type_archive_link('bien');
    $pp_r_hote  = 'Julien';
}
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane + titre de la page -->
        <section class="checkout-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><a href="<?php echo $catalogue; ?>">Espaces</a></li>
                    <li class="is-current" aria-current="page">Confirmer et payer</li>
                </ol>
            </nav>

            <div class="checkout-entete">
                <a href="<?php echo esc_url($pp_r_lien); ?>" class="checkout-retour" aria-label="Revenir à l'annonce">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
                </a>
                <h1>Confirmer et payer</h1>
            </div>
        </section>

        <!-- BLOC 2 : formulaire de paiement + panneau récapitulatif -->
        <div class="checkout-layout">

            <form class="checkout" id="checkout-form" data-garantie="6" data-annonce="<?php echo esc_attr($pp_r_titre); ?>" data-hote="<?php echo esc_attr($pp_r_hote); ?>">

                <!-- Coordonnées du locataire : indispensables pour que
                     l'hôte reçoive la demande et puisse répondre -->
                <section class="checkout-card" aria-labelledby="coordonnees-titre">
                    <h2 id="coordonnees-titre">Vos coordonnées</h2>

                    <!-- Réservation réservée aux membres : connexion obligatoire.
                         Le bloc affiché dépend de la classe is-connected du body ;
                         les coordonnées proviennent du compte, plus de saisie. -->
                    <div class="checkout-gate checkout-compte--visiteur">
                        <p class="checkout-card__texte">La réservation est réservée aux membres Pool&nbsp;Party. Connectez-vous ou créez votre compte pour envoyer votre demande à <?php echo esc_html($pp_r_hote); ?>&nbsp;: c'est à l'adresse de votre compte qu'il vous répondra.</p>
                        <div class="checkout-gate__actions">
                            <button type="button" class="btn btn-secondary btn-medium js-open-login">Se connecter</button>
                            <a class="btn btn-tertiary btn-medium" href="<?php echo esc_url(home_url('/inscription/')); ?>">Créer un compte</a>
                        </div>
                    </div>

                    <div class="checkout-identite checkout-compte--connecte">
                        <p class="checkout-card__texte"><?php echo esc_html($pp_r_hote); ?> reçoit votre demande et vous répondra à l'adresse de votre compte.</p>
                        <p class="checkout-identite__ligne">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span data-checkout="identite">Connecté à votre compte</span>
                        </p>
                    </div>
                </section>

                <!-- Échéance de paiement -->
                <section class="checkout-card" aria-labelledby="echeance-titre">
                    <h2 id="echeance-titre">Choisissez quand vous souhaitez payer</h2>
                    <div class="checkout-choix">
                        <label class="radio checkout-choix__ligne">
                            <input type="radio" name="echeance" value="comptant" checked>
                            <span class="checkout-choix__texte">Payez <span data-checkout="montant-comptant">103,50€</span> maintenant</span>
                        </label>
                        <label class="radio checkout-choix__ligne">
                            <input type="radio" name="echeance" value="trois-fois">
                            <span class="checkout-choix__texte">
                                Payez en 3 fois, sans frais
                                <span class="checkout-choix__aide">Un premier versement de <span data-checkout="montant-premier">34,50€</span> aujourd'hui, puis 2 fois <span data-checkout="montant-tiers">34,50€</span>.</span>
                            </span>
                        </label>
                    </div>
                </section>

                <!-- Mode de paiement -->
                <section class="checkout-card" aria-labelledby="paiement-titre">
                    <h2 id="paiement-titre">Mode de paiement</h2>
                    <div class="checkout-choix">
                        <label class="radio checkout-choix__ligne">
                            <input type="radio" name="paiement" value="carte" checked>
                            <span class="checkout-choix__texte checkout-choix__texte--icone">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                                Carte bancaire
                            </span>
                        </label>
                        <label class="radio checkout-choix__ligne">
                            <input type="radio" name="paiement" value="paypal">
                            <span class="checkout-choix__texte checkout-choix__texte--icone">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6.5 21 8 13h4a5 5 0 0 0 5-5 4 4 0 0 0-4-4H7.5a1 1 0 0 0-1 .8L4 17"/><path d="M8 13c4.5 0 8-1 9-5"/></svg>
                                PayPal
                            </span>
                        </label>
                    </div>

                    <!-- Champs de la carte, masqués quand PayPal est choisi -->
                    <div class="checkout-carte" data-checkout="champs-carte">
                        <div class="form-field">
                            <label class="form-field__label" for="carte-numero">Numéro de carte</label>
                            <input class="form-field__input" id="carte-numero" name="carte-numero" type="text" inputmode="numeric" autocomplete="cc-number" placeholder="1234 5678 9012 3456">
                        </div>
                        <div class="checkout-carte__rangee">
                            <div class="form-field">
                                <label class="form-field__label" for="carte-expiration">Expiration</label>
                                <input class="form-field__input" id="carte-expiration" name="carte-expiration" type="text" inputmode="numeric" autocomplete="cc-exp" placeholder="MM/AA">
                            </div>
                            <div class="form-field">
                                <label class="form-field__label" for="carte-cvc">Cryptogramme</label>
                                <input class="form-field__input" id="carte-cvc" name="carte-cvc" type="text" inputmode="numeric" autocomplete="cc-csc" placeholder="3 chiffres">
                            </div>
                        </div>
                        <div class="form-field">
                            <label class="form-field__label" for="carte-titulaire">Titulaire de la carte</label>
                            <input class="form-field__input" id="carte-titulaire" name="carte-titulaire" type="text" autocomplete="cc-name" placeholder="Nom tel qu'il figure sur la carte">
                        </div>
                    </div>

                    <ul class="checkout-logos" aria-label="Cartes acceptées">
                        <li>Visa</li>
                        <li>Mastercard</li>
                        <li>CB</li>
                        <li>Amex</li>
                    </ul>
                </section>

                <!-- Garantie annulation en option -->
                <section class="checkout-card checkout-card--option" aria-labelledby="garantie-titre">
                    <div class="checkout-option">
                        <div>
                            <h2 id="garantie-titre">Ajouter la garantie annulation ?</h2>
                            <p class="checkout-option__prix">Oui, ajouter pour <span data-checkout="prix-garantie">6€</span></p>
                            <p class="checkout-option__aide">Disponible uniquement au moment de la réservation.</p>
                        </div>
                        <button type="button" class="btn btn-tertiary checkout-option__btn" data-checkout="garantie-btn" aria-pressed="false">Ajouter</button>
                    </div>
                    <p class="checkout-option__detail">Annulez jusqu'à 2h avant le début du créneau, quelle que soit la raison, et récupérez l'intégralité du montant payé. Sans la garantie, l'annulation reste gratuite jusqu'à 48h avant.</p>
                </section>

                <!-- Message à l'hôte -->
                <section class="checkout-card" aria-labelledby="message-titre">
                    <h2 id="message-titre">Un mot pour <?php echo esc_html($pp_r_hote); ?> ?</h2>
                    <p class="checkout-card__texte">Dites-lui en quelques mots l'occasion de votre venue : il pourra préparer l'espace en conséquence. Facultatif mais toujours apprécié.</p>
                    <div class="form-field">
                        <label class="form-field__label sr-only" for="checkout-message">Votre message</label>
                        <textarea class="form-field__input" id="checkout-message" name="message" placeholder="Bonjour Julien, nous venons fêter..."></textarea>
                    </div>
                </section>

                <!-- Conditions + validation -->
                <p class="checkout-conditions">En sélectionnant le bouton ci-dessous, j'accepte les <a href="<?php echo esc_url(home_url('/cgv/')); ?>">conditions de réservation</a> et les <a href="<?php echo esc_url(home_url('/cgu/')); ?>">conditions d'utilisation</a>, et je confirme avoir pris connaissance de la <a href="<?php echo esc_url(home_url('/mentions-legales/')); ?>">politique de confidentialité</a>.</p>

                <button type="submit" class="btn btn-secondary btn-large checkout-submit">Demander la réservation</button>
                <p class="checkout-note">Aucun débit immédiat : un e-mail est envoyé à <?php echo esc_html($pp_r_hote); ?>, qui confirme la disponibilité de son espace sous 24h maximum. Vous ne serez débité qu'après sa confirmation.</p>
            </form>

            <!-- Panneau récapitulatif de la réservation -->
            <aside class="recap-panel" aria-labelledby="recap-titre">
                <h2 class="sr-only" id="recap-titre">Récapitulatif de la réservation</h2>

                <p class="recap-alerte">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2v4"/><path d="m6.4 6.4 2.8 2.8"/><path d="M2 12h4"/><path d="m6.4 17.6 2.8-2.8"/><path d="M12 22v-4"/><path d="m17.6 17.6-2.8-2.8"/><path d="M22 12h-4"/><path d="m17.6 6.4-2.8 2.8"/></svg>
                    Perle rare ! Les créneaux de cette piscine partent vite en été.
                </p>

                <div class="recap">
                    <a class="recap__annonce" href="<?php echo esc_url($pp_r_lien); ?>" aria-label="Voir la fiche de l'annonce : <?php echo esc_attr($pp_r_titre); ?>">
                        <img src="<?php echo esc_url($pp_r_image); ?>" alt="<?php echo esc_attr($pp_r_alt); ?>">
                        <div>
                            <p class="recap__titre"><?php echo esc_html($pp_r_titre); ?></p>
                            <span class="rating rating--small"><?php echo esc_html($pp_r_note); ?> <span class="rating__count">(<?php echo esc_html($pp_r_avis); ?> avis)</span></span>
                            <p class="recap__hote">Espace proposé par <?php echo esc_html($pp_r_hote); ?></p>
                        </div>
                    </a>

                    <p class="recap__annulation">Annulation gratuite jusqu'à 48h avant le début du créneau. <a href="<?php echo esc_url(home_url('/cgv/')); ?>">Consulter les conditions complètes</a></p>

                    <dl class="recap__infos">
                        <div class="recap__info">
                            <dt>Date et créneau</dt>
                            <dd data-checkout="recap-date">15/07/2026, Après-midi 14h-18h</dd>
                            <button type="button" class="btn btn-tertiary btn-small recap__modifier" data-popup-ouvrir="popup-dates">Modifier</button>
                        </div>
                        <div class="recap__info">
                            <dt>Invités</dt>
                            <dd data-checkout="recap-invites">2 adultes, 2 enfants</dd>
                            <button type="button" class="btn btn-tertiary btn-small recap__modifier" data-popup-ouvrir="popup-invites">Modifier</button>
                        </div>
                    </dl>

                    <dl class="resa__recap recap__prix">
                        <div class="resa__ligne">
                            <dt data-checkout="libelle">Demi-journée privative</dt>
                            <dd data-checkout="sous-total">90,00€</dd>
                        </div>
                        <div class="resa__ligne">
                            <dt>Frais de service (15%)</dt>
                            <dd data-checkout="frais">13,50€</dd>
                        </div>
                        <div class="resa__ligne" data-checkout="ligne-garantie" hidden>
                            <dt>Garantie annulation</dt>
                            <dd data-checkout="montant-garantie">6,00€</dd>
                        </div>
                        <div class="resa__ligne resa__ligne--total">
                            <dt>Total TTC</dt>
                            <dd data-checkout="total">103,50€</dd>
                        </div>
                    </dl>
                    <p class="resa__ecolo">Dont <span data-checkout="ecolo">0,90€</span> est reversé pour l'écologie</p>

                    <ul class="reassurance-list">
                        <li class="reassurance-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5l-8-3z"/><path d="m8.5 12 2.5 2.5 4.5-4.5"/></svg>
                            Paiement 100% sécurisé
                        </li>
                        <li class="reassurance-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 9h18M10 13l4 4M14 13l-4 4"/></svg>
                            Annulation gratuite jusqu'à 48h avant
                        </li>
                        <li class="reassurance-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                            Confirmation sous 24h maximum
                        </li>
                    </ul>

                    <p class="recap__caution">L'hôte demande un dépôt de garantie de 200 €. Il est bloqué séparément avant votre arrivée puis libéré au plus tard 7 jours après le créneau.</p>
                </div>
            </aside>
        </div>

        <!-- Pop-up : modifier la date et le créneau -->
        <div class="popup-overlay" id="popup-dates" hidden>
            <div class="popup popup--checkout" role="dialog" aria-modal="true" aria-labelledby="popup-dates-titre">
                <button type="button" class="popup__close" aria-label="Fermer" data-popup-fermer></button>
                <h2 class="popup__title" id="popup-dates-titre">Modifiez votre créneau</h2>
                <div class="popup__body">
                    <div class="form-field resa-field">
                        <label class="form-field__label" for="checkout-date">Date</label>
                        <input class="form-field__input" id="checkout-date" name="date" form="checkout-form" type="text" value="15/07/2026" readonly>
                    </div>
                    <div class="form-field checkout-creneau">
                        <label class="form-field__label" for="checkout-creneau">Créneau</label>
                        <div class="input-search checkout-creneau__pill">
                            <input type="text" id="checkout-creneau" name="creneau" form="checkout-form" value="Après-midi 14h-18h" readonly>
                            <button type="button" class="input-search__chevron" aria-label="Choisir le créneau" aria-expanded="false" aria-controls="checkout-creneau-liste"></button>
                        </div>
                        <ul class="checkout-creneau__liste" id="checkout-creneau-liste" hidden>
                            <li><button type="button" class="dropdown-item">Matin 9h-13h</button></li>
                            <li><button type="button" class="dropdown-item is-active">Après-midi 14h-18h</button></li>
                            <li><button type="button" class="dropdown-item">Soirée 18h-22h</button></li>
                        </ul>
                    </div>
                    <p class="popup-aide">Cliquez sur le champ Date pour ouvrir le calendrier, puis choisissez le créneau qui vous arrange.</p>
                </div>
                <div class="popup__actions">
                    <button type="button" class="btn btn-tertiary btn-medium" data-popup-fermer>Annuler</button>
                    <button type="button" class="btn btn-primary btn-medium" data-popup-valider>Enregistrer</button>
                </div>
            </div>
        </div>

        <!-- Pop-up : modifier le nombre d'invités -->
        <div class="popup-overlay" id="popup-invites" hidden>
            <div class="popup popup--checkout" role="dialog" aria-modal="true" aria-labelledby="popup-invites-titre">
                <button type="button" class="popup__close" aria-label="Fermer" data-popup-fermer></button>
                <h2 class="popup__title" id="popup-invites-titre">Modifiez le nombre d'invités</h2>
                <div class="popup__body">
                    <p class="popup-aide">Cet espace accueille 6 personnes au maximum, sans compter les bébés. Les enfants restent sous la surveillance d'un adulte.</p>
                    <ul class="compteur-liste">
                        <li class="compteur-ligne">
                            <div>
                                <span class="compteur-ligne__label">Adultes</span>
                                <span class="compteur-ligne__aide">13 ans et +</span>
                            </div>
                            <div class="compteur" data-compteur="adultes" data-min="1" data-max="6">
                                <button type="button" class="compteur__btn" data-action="moins" aria-label="Retirer un adulte">-</button>
                                <span class="compteur__valeur" aria-live="polite">2</span>
                                <button type="button" class="compteur__btn" data-action="plus" aria-label="Ajouter un adulte">+</button>
                            </div>
                        </li>
                        <li class="compteur-ligne">
                            <div>
                                <span class="compteur-ligne__label">Enfants</span>
                                <span class="compteur-ligne__aide">De 2 à 12 ans</span>
                            </div>
                            <div class="compteur" data-compteur="enfants" data-min="0" data-max="6">
                                <button type="button" class="compteur__btn" data-action="moins" aria-label="Retirer un enfant">-</button>
                                <span class="compteur__valeur" aria-live="polite">2</span>
                                <button type="button" class="compteur__btn" data-action="plus" aria-label="Ajouter un enfant">+</button>
                            </div>
                        </li>
                        <li class="compteur-ligne">
                            <div>
                                <span class="compteur-ligne__label">Bébés</span>
                                <span class="compteur-ligne__aide">Moins de 3 ans</span>
                            </div>
                            <div class="compteur" data-compteur="bebes" data-min="0" data-max="4">
                                <button type="button" class="compteur__btn" data-action="moins" aria-label="Retirer un bébé">-</button>
                                <span class="compteur__valeur" aria-live="polite">0</span>
                                <button type="button" class="compteur__btn" data-action="plus" aria-label="Ajouter un bébé">+</button>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="popup__actions">
                    <button type="button" class="btn btn-tertiary btn-medium" data-popup-fermer>Annuler</button>
                    <button type="button" class="btn btn-primary btn-medium" data-popup-valider>Enregistrer</button>
                </div>
            </div>
        </div>

        <!-- Pop-up : confirmation de la demande, ouverte par main.js une
             fois l'e-mail envoyé à l'hôte. Le message est complété côté JS
             (nom de l'hôte + date du créneau). -->
        <div class="popup-overlay" id="popup-confirmation" hidden>
            <div class="popup popup--confirmation" role="dialog" aria-modal="true" aria-labelledby="popup-confirmation-titre">
                <button type="button" class="popup__close" aria-label="Fermer" data-popup-fermer></button>
                <div class="confirmation-icone" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
                <h2 class="popup__title" id="popup-confirmation-titre">Demande envoyée !</h2>
                <div class="popup__body">
                    <p class="confirmation-texte" data-checkout="confirmation-texte"></p>
                </div>
                <div class="popup__actions">
                    <button type="button" class="btn btn-tertiary btn-medium" data-popup-fermer>Rester sur cette page</button>
                    <a class="btn btn-primary btn-medium" href="<?php echo esc_url(home_url('/mes-reservations/')); ?>">Voir mes réservations</a>
                </div>
            </div>
        </div>
    </main>

<?php get_footer(); ?>
