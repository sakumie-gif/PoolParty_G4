<?php
/**
 * Gabarit de la page « Devenir partenaire » (contenu repris de devenir-partenaire.html).
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 */
get_header();

// Formulaire de candidature fictif (projet étudiant, aucun e-mail réel),
// mais dégradation propre sans JavaScript : si la page reçoit la
// soumission (POST) avec un jeton valide, on affiche la confirmation côté
// serveur au lieu de recharger un formulaire vide. Avec JS activé, main.js
// intercepte l'envoi et affiche la même confirmation sans recharger.
$pp_partenaire_envoye = (
    (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST')
    && isset($_POST['pp_partenaire_nonce'])
    && wp_verify_nonce($_POST['pp_partenaire_nonce'], 'pp_partenaire_envoi')
);
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane -->
        <div class="partenaire-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><span class="is-current" aria-current="page">Devenir partenaire</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="partenaire-hero">
            <div class="partenaire-hero__inner">
                <div class="partenaire-hero__texte">
                    <h1>Faites grandir votre activité avec Pool Party</h1>
                    <p>Coachs aquatiques, traiteurs, animateurs, piscinistes : notre communauté de baigneurs et d'hôtes a besoin de vos services. Rejoignez le réseau de partenaires Pool Party et développez votre clientèle en Île-de-France.</p>
                    <div class="partenaire-hero__actions">
                        <a href="#candidature" class="btn btn-secondary btn-medium">Proposer un partenariat</a>
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-tertiary btn-medium">Poser une question</a>
                    </div>
                </div>
                <img class="partenaire-hero__photo" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/evenements/team-building.jpg'); ?>" alt="Mains d'une équipe réunies au centre d'un cercle">
            </div>
        </section>

        <!-- BLOC 3 : Chiffres clés -->
        <section class="partenaire-chiffres" aria-label="Pool Party en chiffres">
            <ul class="partenaire-chiffres__liste">
                <li class="chiffre-cle">
                    <span class="chiffre-cle__valeur">350</span>
                    <span class="chiffre-cle__label">espaces référencés en Île-de-France</span>
                </li>
                <li class="chiffre-cle">
                    <span class="chiffre-cle__valeur">18 000</span>
                    <span class="chiffre-cle__label">baignades réservées depuis le lancement</span>
                </li>
                <li class="chiffre-cle">
                    <span class="chiffre-cle__valeur">4,8/5</span>
                    <span class="chiffre-cle__label">de satisfaction moyenne des clients</span>
                </li>
                <li class="chiffre-cle">
                    <span class="chiffre-cle__valeur">48 h</span>
                    <span class="chiffre-cle__label">pour recevoir une première réponse</span>
                </li>
            </ul>
        </section>

        <!-- BLOC 4 : Avantages du partenariat -->
        <section class="partenaire-atouts" aria-labelledby="atouts-titre">
            <h2 id="atouts-titre">Pourquoi rejoindre le réseau ?</h2>
            <p class="partenaire-atouts__sous-titre">Un partenariat pensé gagnant-gagnant : vous apportez votre savoir-faire, nous vous apportons des clients.</p>

            <div class="partenaire-atouts__grille">
                <div class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 15 4 4"/><path d="M2.352 10.648a1.205 1.205 0 0 0 0 1.704l2.296 2.296a1.205 1.205 0 0 0 1.704 0l6.029-6.029a1 1 0 1 1 3 3l-6.029 6.029a1.205 1.205 0 0 0 0 1.704l2.296 2.296a1.205 1.205 0 0 0 1.704 0l6.365-6.367A1 1 0 0 0 8.716 4.282z"/><path d="m5 8 4 4"/></svg>
                    <h3 class="card-protection__title">Une visibilité locale</h3>
                    <p class="card-protection__text">Votre offre est mise en avant sur les annonces et les pages événement de votre secteur, auprès d'un public déjà prêt à réserver.</p>
                </div>
                <div class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/></svg>
                    <h3 class="card-protection__title">Des revenus complémentaires</h3>
                    <p class="card-protection__text">Chaque prestation réservée via la plateforme vous est reversée, sans abonnement ni frais d'entrée : la commission ne s'applique qu'aux ventes.</p>
                </div>
                <div class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5Zm0 0a9 9 0 1 1 18 0m0 0v5a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3Z"/><path d="M21 16v2a4 4 0 0 1-4 4h-5"/></svg>
                    <h3 class="card-protection__title">Un interlocuteur dédié</h3>
                    <p class="card-protection__text">Un membre de l'équipe suit votre compte, vous aide à construire vos offres et répond à vos questions du lundi au samedi.</p>
                </div>
                <div class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                    <h3 class="card-protection__title">Un label de confiance</h3>
                    <p class="card-protection__text">Le badge Partenaire vérifié rassure les clients : profil contrôlé, assurances à jour et avis authentiques collectés après chaque prestation.</p>
                </div>
            </div>
        </section>

        <!-- BLOC 5 : Profils de partenaires -->
        <section class="partenaire-profils" aria-labelledby="profils-titre">
            <h2 id="profils-titre">Des partenariats pour chaque métier</h2>
            <p class="partenaire-profils__sous-titre">Trois familles de partenaires font vivre l'expérience Pool Party. Vous vous reconnaissez ? Parlons-en.</p>

            <div class="partenaire-profils__grille">
                <article class="card-profil">
                    <img class="card-profil__photo" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/evenements/detente.jpg'); ?>" alt="Femme se relaxant dans un sauna">
                    <div class="card-profil__corps">
                        <h3 class="card-profil__titre">Professionnels du bien-être</h3>
                        <p class="card-profil__texte">Proposez vos prestations en option des réservations et transformez chaque baignade en parenthèse de détente.</p>
                        <ul class="card-profil__exemples">
                            <li>Coachs d'aquagym et de natation</li>
                            <li>Masseurs et praticiens spa</li>
                            <li>Professeurs de yoga aquatique</li>
                        </ul>
                    </div>
                </article>
                <article class="card-profil">
                    <img class="card-profil__photo" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/evenements/anniversaire.jpg'); ?>" alt="Ananas à lunettes de soleil entouré de ballons de fête">
                    <div class="card-profil__corps">
                        <h3 class="card-profil__titre">Prestataires d'événements</h3>
                        <p class="card-profil__texte">Anniversaires, team buildings, pool parties : équipez les fêtes organisées dans les espaces de nos hôtes.</p>
                        <ul class="card-profil__exemples">
                            <li>Traiteurs et food trucks</li>
                            <li>Animateurs et DJ</li>
                            <li>Photographes et décorateurs</li>
                        </ul>
                    </div>
                </article>
                <article class="card-profil">
                    <img class="card-profil__photo" src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-torcy.jpg'); ?>" alt="Piscine extérieure entretenue dans un jardin">
                    <div class="card-profil__corps">
                        <h3 class="card-profil__titre">Experts de la piscine</h3>
                        <p class="card-profil__texte">Accompagnez nos hôtes dans l'entretien et la mise aux normes de leurs bassins, avec des offres réservées à la communauté.</p>
                        <ul class="card-profil__exemples">
                            <li>Piscinistes et techniciens d'entretien</li>
                            <li>Vendeurs d'équipement et d'accessoires</li>
                            <li>Assureurs et diagnostiqueurs</li>
                        </ul>
                    </div>
                </article>
            </div>
        </section>

        <!-- BLOC 6 : Étapes du partenariat -->
        <section class="partenaire-etapes" aria-labelledby="etapes-titre">
            <h2 id="etapes-titre">Devenez partenaire en trois étapes</h2>

            <div class="partenaire-etapes__grille">
                <article class="card-parcours">
                    <div class="card-parcours__badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>
                    </div>
                    <p class="card-parcours__step">1. Candidatez</p>
                    <p class="card-parcours__text">Remplissez le formulaire en bas de page en décrivant votre activité et le partenariat que vous imaginez.</p>
                </article>
                <article class="card-parcours">
                    <div class="card-parcours__badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"/></svg>
                    </div>
                    <p class="card-parcours__step">2. Échangeons</p>
                    <p class="card-parcours__text">Sous 48 heures, votre interlocuteur dédié vous rappelle pour définir ensemble les contours de la collaboration.</p>
                </article>
                <article class="card-parcours">
                    <div class="card-parcours__badge">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 9.536V7a4 4 0 0 1 4-4h1.5a.5.5 0 0 1 .5.5V5a4 4 0 0 1-4 4 4 4 0 0 0-4 4c0 2 1 3 1 5a5 5 0 0 1-1 3"/><path d="M4 9a5 5 0 0 1 8 4 5 5 0 0 1-8-4"/><path d="M5 21h14"/></svg>
                    </div>
                    <p class="card-parcours__step">3. Lancez-vous</p>
                    <p class="card-parcours__text">Votre offre est publiée avec le badge Partenaire vérifié et les premières demandes arrivent directement dans votre boîte mail.</p>
                </article>
            </div>
        </section>

        <!-- BLOC 7 : Témoignages de partenaires -->
        <section class="partenaire-temoignages" aria-labelledby="temoignages-titre">
            <h2 id="temoignages-titre">Ils travaillent déjà avec nous</h2>

            <div class="partenaire-temoignages__grille">
                <article class="card-avis">
                    <div class="card-avis__stars" aria-label="Note de 5 sur 5">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                    <blockquote class="card-avis__quote">"Mes cours d'aquagym affichent complet depuis que je suis référencée sur les annonces du secteur de Torcy. La mise en relation est simple et les clients arrivent déjà convaincus."</blockquote>
                    <footer class="card-avis__author">
                        <span class="card-avis__avatar">N</span>
                        <div>
                            <p class="card-avis__name">Nadia Belkacem</p>
                            <p class="card-avis__role">Coach aquatique, partenaire depuis 2025</p>
                        </div>
                    </footer>
                </article>
                <article class="card-avis">
                    <div class="card-avis__stars" aria-label="Note de 5 sur 5">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                    <blockquote class="card-avis__quote">"Les anniversaires au bord des piscines représentent maintenant un tiers de mon chiffre d'affaires d'été. L'équipe m'a aidé à calibrer une offre spéciale pool party."</blockquote>
                    <footer class="card-avis__author">
                        <span class="card-avis__avatar">M</span>
                        <div>
                            <p class="card-avis__name">Marc Lefebvre</p>
                            <p class="card-avis__role">Traiteur événementiel, partenaire depuis 2026</p>
                        </div>
                    </footer>
                </article>
                <article class="card-avis">
                    <div class="card-avis__stars" aria-label="Note de 5 sur 5">
                        <span></span><span></span><span></span><span></span><span></span>
                    </div>
                    <blockquote class="card-avis__quote">"Les hôtes Pool Party sont des clients réguliers et attentifs à la qualité de leur bassin. Le partenariat m'apporte une vingtaine de contrats d'entretien par an."</blockquote>
                    <footer class="card-avis__author">
                        <span class="card-avis__avatar">S</span>
                        <div>
                            <p class="card-avis__name">Sylvain Dumas</p>
                            <p class="card-avis__role">Pisciniste, partenaire depuis 2025</p>
                        </div>
                    </footer>
                </article>
            </div>
        </section>

        <!-- BLOC 8 : Questions fréquentes des partenaires -->
        <section class="partenaire-faq" aria-labelledby="faq-titre">
            <h2 id="faq-titre">Vos questions sur le partenariat</h2>

            <ul class="faq-liste">
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-partenaire-1">Combien coûte le partenariat ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-partenaire-1" hidden>
                        <p>L'inscription au réseau est gratuite, sans abonnement ni engagement de durée. Pool Party prélève uniquement une commission sur les prestations réservées via la plateforme ; son taux est précisé dans la convention de partenariat.</p>
                    </div>
                </li>
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-partenaire-2">Quelles conditions faut-il remplir pour candidater ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-partenaire-2" hidden>
                        <p>Vous devez exercer une activité déclarée (micro-entreprise acceptée), disposer d'une assurance responsabilité civile professionnelle à jour et intervenir en Île-de-France. Les diplômes sont demandés pour les activités encadrées comme les cours de natation.</p>
                    </div>
                </li>
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-partenaire-3">Comment mes prestations sont-elles mises en avant ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-partenaire-3" hidden>
                        <p>Vos offres apparaissent en option lors de la réservation des espaces de votre secteur, sur les pages événement correspondant à votre activité et dans la newsletter mensuelle envoyée à la communauté.</p>
                    </div>
                </li>
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-partenaire-4">Puis-je arrêter le partenariat quand je le souhaite ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-partenaire-4" hidden>
                        <p>Oui, un simple message à votre interlocuteur dédié suffit. Vos offres sont retirées de la plateforme sous 7 jours ; seules les prestations déjà réservées doivent être honorées ou remboursées.</p>
                    </div>
                </li>
                <li class="faq-item">
                    <h3 class="faq-item__question">
                        <button type="button" aria-expanded="false" aria-controls="faq-partenaire-5">Je loue un espace : est-ce le bon formulaire ?</button>
                    </h3>
                    <div class="faq-item__reponse" id="faq-partenaire-5" hidden>
                        <p>Non, cette page s'adresse aux professionnels qui proposent des services. Pour mettre votre piscine, spa ou sauna en location, passez par le parcours <a href="<?php echo esc_url(home_url('/proposer/')); ?>">Proposer mon espace</a>.</p>
                    </div>
                </li>
            </ul>
        </section>

        <!-- BLOC 9 : Formulaire de candidature -->
        <section class="partenaire-candidature" id="candidature" aria-labelledby="candidature-titre">
            <div class="partenaire-candidature__carte">
                <h2 id="candidature-titre">Proposez votre partenariat</h2>
                <p class="partenaire-candidature__sous-titre">Cinq minutes suffisent. Votre interlocuteur dédié vous recontacte sous 48 heures ouvrées.</p>

                <?php if ($pp_partenaire_envoye) : ?>
                <p class="partenaire-form__confirmation" role="status">Merci pour votre candidature ! Votre interlocuteur dédié vous recontacte sous 48 heures ouvrées.</p>
                <?php else : ?>
                <form class="partenaire-form" action="<?php echo esc_url(get_permalink()); ?>" method="post">
                    <?php wp_nonce_field('pp_partenaire_envoi', 'pp_partenaire_nonce'); ?>

                    <div class="partenaire-form__row">
                        <div class="form-field">
                            <label class="form-field__label" for="partenaire-societe">Entreprise</label>
                            <input class="form-field__input" id="partenaire-societe" name="societe" type="text" placeholder="Nom de votre entreprise" autocomplete="organization" required>
                        </div>
                        <div class="form-field">
                            <label class="form-field__label" for="partenaire-contact">Nom du contact</label>
                            <input class="form-field__input" id="partenaire-contact" name="contact" type="text" placeholder="Prénom et nom" autocomplete="name" required>
                        </div>
                    </div>

                    <div class="partenaire-form__row">
                        <div class="form-field">
                            <label class="form-field__label" for="partenaire-email">Email</label>
                            <input class="form-field__input" id="partenaire-email" name="email" type="email" placeholder="Votre@email.com" autocomplete="email" required>
                        </div>
                        <div class="form-field">
                            <label class="form-field__label" for="partenaire-telephone">Téléphone</label>
                            <input class="form-field__input" id="partenaire-telephone" name="telephone" type="tel" placeholder="+33 1 00 00 00 00" autocomplete="tel">
                        </div>
                    </div>

                    <!-- Sélecteur de type de partenariat (pilule select, composant Input Chevron) -->
                    <div class="form-field contact-select">
                        <label class="form-field__label" for="partenaire-type">Votre activité</label>
                        <div class="input-search contact-select__pill">
                            <input type="text" id="partenaire-type" name="type" placeholder="Sélectionner ..." readonly>
                            <button type="button" class="input-search__chevron" aria-label="Choisir votre type d'activité" aria-expanded="false" aria-controls="partenaire-type-liste"></button>
                        </div>
                        <ul class="contact-select__liste" id="partenaire-type-liste" hidden>
                            <li><button type="button" class="dropdown-item">Bien-être et sport aquatique</button></li>
                            <li><button type="button" class="dropdown-item">Événementiel et animation</button></li>
                            <li><button type="button" class="dropdown-item">Entretien et équipement de piscine</button></li>
                            <li><button type="button" class="dropdown-item">Autre activité</button></li>
                        </ul>
                    </div>

                    <div class="form-field">
                        <label class="form-field__label" for="partenaire-message">Votre projet</label>
                        <textarea class="form-field__input" id="partenaire-message" name="message" placeholder="Présentez votre activité, votre zone d'intervention et le partenariat que vous imaginez" maxlength="1000" required></textarea>
                    </div>

                    <label class="checkbox">
                        <input type="checkbox" name="consentement" required>
                        <span>J'accepte que mes données soient utilisées pour traiter ma candidature. <a href="<?php echo esc_url(home_url('/mentions-legales/')); ?>">Politique de confidentialité</a></span>
                    </label>

                    <button type="submit" class="btn btn-secondary btn-medium partenaire-form__submit">Envoyer ma candidature</button>

                </form>
                <?php endif; ?>
            </div>
        </section>
    </main>

<?php get_footer(); ?>
