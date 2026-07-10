<?php
/**
 * Gabarit de la page « Contact » (contenu repris de contact.html).
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 */
get_header();

// Formulaire de contact fictif (projet étudiant, aucun e-mail réel), mais
// dégradation propre sans JavaScript : si la page reçoit la soumission
// (POST) avec un jeton valide, on affiche la confirmation côté serveur au
// lieu de recharger un formulaire vide. Avec JS activé, main.js intercepte
// l'envoi et affiche la même confirmation sans recharger la page.
$pp_contact_envoye = (
    (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST')
    && isset($_POST['pp_contact_nonce'])
    && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['pp_contact_nonce'])), 'pp_contact_envoi')
);
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane -->
        <div class="contact-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><span class="is-current" aria-current="page">Contact</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="contact-hero">
            <h1>Une question ? Nous sommes là.</h1>
            <p>Une question, un doute, une demande spécifique. Notre équipe vous répond en moins de 24 heures, du lundi au samedi.</p>
        </section>

        <!-- BLOC 3 : Coordonnées + formulaire -->
        <div class="contact-layout">

            <!-- Colonne coordonnées -->
            <div class="contact-aside">

                <section class="contact-card" aria-labelledby="contact-card-titre">
                    <h2 id="contact-card-titre">Contactez-nous</h2>
                    <p class="contact-card__sub">Parlons de votre projet</p>
                    <img class="contact-card__photo" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/contact/contact-telephone.jpg'); ?>" alt="Un conseiller Pool Party répond à un message sur son téléphone" loading="lazy" decoding="async" width="818" height="372">

                    <ul class="contact-card__infos">
                        <li class="contact-info">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                            <div>
                                <span class="contact-info__label">Email</span>
                                <a href="mailto:contact@poolparty.fr">contact@poolparty.fr</a>
                            </div>
                        </li>
                        <li class="contact-info">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>
                            <div>
                                <span class="contact-info__label">Téléphone</span>
                                <a href="tel:+33123456789">01 23 45 67 89</a>
                            </div>
                        </li>
                        <li class="contact-info">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                            <div>
                                <span class="contact-info__label">Adresse</span>
                                <span>12 rue de Rivoli, 75001 Paris</span>
                            </div>
                        </li>
                    </ul>
                </section>

                <section class="contact-avant" aria-labelledby="contact-avant-titre">
                    <h2 class="contact-avant__titre" id="contact-avant-titre">Avant de nous écrire</h2>
                    <ul>
                        <li><a href="<?php echo esc_url(home_url('/faq/')); ?>" class="link-signal">Consulter la FAQ</a></li>
                        <li><a href="<?php echo esc_url(home_url('/faq/#faq-annulation')); ?>" class="link-signal">Annuler ma réservation</a></li>
                        <li><a href="<?php echo esc_url(home_url('/faq/#faq-paiement')); ?>" class="link-signal">Comprendre la commission</a></li>
                    </ul>
                </section>

            </div>

            <!-- Colonne formulaire -->
            <section class="contact-form-card" aria-labelledby="contact-form-titre">
                <h2 class="sr-only" id="contact-form-titre">Formulaire de contact</h2>

                <?php if ($pp_contact_envoye) : ?>
                <p class="contact-form__confirmation" role="status">Merci pour votre message ! Notre équipe vous répond sous 24 heures.</p>
                <?php else : ?>
                <form class="contact-form" action="<?php echo esc_url(get_permalink()); ?>" method="post">
                    <?php wp_nonce_field('pp_contact_envoi', 'pp_contact_nonce'); ?>

                    <div class="contact-form__row">
                        <div class="form-field">
                            <label class="form-field__label" for="contact-nom">Nom</label>
                            <input class="form-field__input" id="contact-nom" name="nom" type="text" placeholder="Entrez votre nom" autocomplete="family-name" required>
                        </div>
                        <div class="form-field">
                            <label class="form-field__label" for="contact-prenom">Prénom</label>
                            <input class="form-field__input" id="contact-prenom" name="prenom" type="text" placeholder="Entrez votre prénom" autocomplete="given-name" required>
                        </div>
                    </div>

                    <div class="contact-form__row">
                        <div class="form-field">
                            <label class="form-field__label" for="contact-email">Email</label>
                            <input class="form-field__input" id="contact-email" name="email" type="email" placeholder="Votre@email.com" autocomplete="email" required>
                        </div>
                        <div class="form-field">
                            <label class="form-field__label" for="contact-telephone">Téléphone</label>
                            <input class="form-field__input" id="contact-telephone" name="telephone" type="tel" placeholder="+33 1 00 00 00 00" autocomplete="tel">
                        </div>
                    </div>

                    <!-- Sélecteur de profil (pilule select, composant Input Chevron) -->
                    <div class="form-field contact-select">
                        <label class="form-field__label" for="contact-profil">Vous êtes ?</label>
                        <div class="input-search contact-select__pill">
                            <input type="text" id="contact-profil" name="profil" placeholder="Sélectionner ..." readonly>
                            <button type="button" class="input-search__chevron" aria-label="Choisir votre profil" aria-expanded="false" aria-controls="contact-profil-liste"></button>
                        </div>
                        <ul class="contact-select__liste" id="contact-profil-liste" hidden>
                            <li><button type="button" class="dropdown-item">Locataire</button></li>
                            <li><button type="button" class="dropdown-item">Propriétaire d'un espace</button></li>
                            <li><button type="button" class="dropdown-item">Futur hôte</button></li>
                            <li><button type="button" class="dropdown-item">Autre</button></li>
                        </ul>
                    </div>

                    <!-- Sélecteur d'objet de la demande -->
                    <div class="form-field contact-select">
                        <label class="sr-only" for="contact-objet">Objet de votre demande</label>
                        <div class="input-search contact-select__pill">
                            <input type="text" id="contact-objet" name="objet" placeholder="Sélectionner ..." readonly>
                            <button type="button" class="input-search__chevron" aria-label="Choisir l'objet de votre demande" aria-expanded="false" aria-controls="contact-objet-liste"></button>
                        </div>
                        <ul class="contact-select__liste" id="contact-objet-liste" hidden>
                            <li><button type="button" class="dropdown-item">Question sur une réservation</button></li>
                            <li><button type="button" class="dropdown-item">Annulation et remboursement</button></li>
                            <li><button type="button" class="dropdown-item">Proposer mon espace</button></li>
                            <li><button type="button" class="dropdown-item">Problème de paiement</button></li>
                            <li><button type="button" class="dropdown-item">Autre demande</button></li>
                        </ul>
                    </div>

                    <!-- Message + compteur de caractères -->
                    <div class="form-field">
                        <label class="sr-only" for="contact-message">Votre message</label>
                        <div class="contact-form__message">
                            <textarea class="form-field__input" id="contact-message" name="message" placeholder="Comment pouvons-nous vous aider ?" maxlength="1000" required></textarea>
                            <span class="contact-form__compteur" aria-hidden="true">0/1000 caractères</span>
                        </div>
                    </div>

                    <!-- Pièce jointe -->
                    <label class="contact-form__fichier">
                        <input type="file" class="sr-only" name="fichier" accept=".jpg,.jpeg,.png,.pdf">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                        <span class="contact-form__fichier-nom">Joindre un fichier (JPG, PNG, PDF 5Mo max)</span>
                    </label>

                    <!-- Consentements -->
                    <label class="checkbox">
                        <input type="checkbox" name="consentement" required>
                        <span>J'accepte que mes données soient utilisées pour traiter ma demande. <a href="<?php echo esc_url(home_url('/mentions-legales/')); ?>">Politique de confidentialité</a></span>
                    </label>
                    <label class="checkbox">
                        <input type="checkbox" name="newsletter">
                        <span>Je souhaite recevoir les actualités de Pool Party</span>
                    </label>

                    <button type="submit" class="btn btn-secondary btn-medium contact-form__submit">Envoyer votre message</button>

                </form>
                <?php endif; ?>
            </section>

        </div>

        <!-- BLOC 4 : Bande de garanties (desktop) -->
        <section class="contact-garanties" aria-label="Nos garanties">
            <ul class="reassurance-list">
                <li class="reassurance-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    Réponse sous 24 heures garantie
                </li>
                <li class="reassurance-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                    Conformité RGPD
                </li>
                <li class="reassurance-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                    Service client basé en France
                </li>
            </ul>
        </section>

        <!-- BLOC 5 : Questions fréquentes -->
        <section class="contact-faq" aria-labelledby="faq-titre">
            <h2 id="faq-titre">Questions fréquentes</h2>

            <ul class="faq-liste">
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-reponse-1">Comment annuler ma réservation ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-reponse-1" hidden>
                        <p>Rendez-vous dans votre espace personnel, rubrique Mes réservations, puis choisissez Annuler sur la réservation concernée. Le remboursement dépend de la politique d'annulation choisie par l'hôte : avec la politique souple, la plus répandue, l'annulation est gratuite jusqu'à 48 heures avant le début du créneau.</p>
                    </div>
                </li>
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-reponse-2">La commission Pool Party est-elle incluse dans le prix affiché ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-reponse-2" hidden>
                        <p>Oui, le prix affiché sur chaque annonce comprend la commission de la plateforme. Aucun frais supplémentaire ne s'ajoute au moment du paiement.</p>
                    </div>
                </li>
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-reponse-3">Mes données personnelles sont-elles partagées ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-reponse-3" hidden>
                        <p>Non, vos données servent uniquement au traitement de vos réservations et de vos demandes. Elles ne sont jamais revendues à des tiers, conformément à notre politique de confidentialité.</p>
                    </div>
                </li>
            </ul>

            <p class="contact-faq__voir-tout">
                <a href="<?php echo esc_url(home_url('/faq/')); ?>" class="btn-link">Voir toutes les questions fréquentes</a>
            </p>
        </section>

        <!-- BLOC 6 : CTA final -->
        <section class="contact-cta" aria-labelledby="contact-cta-titre">
            <h2 id="contact-cta-titre">Prêt à plonger</h2>
            <p>Une envie ? On est là. Et pour ceux qui sont prêts à se lancer...</p>
            <div class="contact-cta__actions">
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="btn btn-tertiary">Explorer les espaces</a>
                <a href="<?php echo esc_url(home_url('/proposer/')); ?>" class="btn btn-secondary">Proposer mon espace</a>
            </div>
        </section>
    </main>

<?php get_footer(); ?>
