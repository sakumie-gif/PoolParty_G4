<?php
/**
 * En-tête commun du site : head WordPress, lien d'évitement,
 * header (logo, recherches, navigation) et menu déroulant.
 * Markup repris tel quel de index.html, seules les URL passent
 * par WordPress. La nav en dur sera remplacée par wp_nav_menu
 * à l'étape 3.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

    <!-- Lien d'évitement pour la navigation au clavier -->
    <a class="skip-link" href="#contenu">Aller au contenu</a>

    <!-- ============================================================
         HEADER commun a toutes les pages ; le modificateur hero
         n'est ajoute que sur la page d'accueil, comme sur le statique
         ============================================================ -->
    <header class="site-header<?php echo is_front_page() ? ' site-header--hero' : ''; ?>">
        <div class="header-inner">

            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="header-logo" aria-label="Accueil Pool Party">
                <img src="<?php echo esc_url(pp_asset('assets/images/logo/logo-full.png')); ?>" alt="Pool Party" class="header-logo-full">
                <img src="<?php echo esc_url(pp_asset('assets/images/logo/logo-icon.png')); ?>" alt="Pool Party" class="header-logo-icon">
            </a>

            <!-- Barre de recherche (visible sur grand écran) -->
            <form class="header-search" action="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" role="search" aria-label="Rechercher un espace">
                <div class="search-field">
                    <label for="search-quoi">Quoi</label>
                    <input type="text" id="search-quoi" name="quoi" placeholder="Choisissez votre bien" value="<?php echo esc_attr(isset($_GET['quoi']) ? wp_unslash($_GET['quoi']) : ''); ?>">
                </div>
                <span class="search-divider" aria-hidden="true"></span>
                <div class="search-field">
                    <label for="search-adresse">Adresse</label>
                    <input type="text" id="search-adresse" name="adresse" placeholder="Où cherchez-vous ?" value="<?php echo esc_attr(isset($_GET['adresse']) ? wp_unslash($_GET['adresse']) : ''); ?>">
                </div>
                <span class="search-divider" aria-hidden="true"></span>
                <div class="search-field search-field--small">
                    <label for="search-date">Date</label>
                    <input type="text" id="search-date" name="date" placeholder="Quand ?" value="<?php echo esc_attr(isset($_GET['date']) ? wp_unslash($_GET['date']) : ''); ?>">
                </div>
                <span class="search-divider" aria-hidden="true"></span>
                <div class="search-field search-field--small">
                    <label for="search-invites">Invités</label>
                    <input type="text" id="search-invites" name="invites" placeholder="Combien ?" value="<?php echo esc_attr(isset($_GET['invites']) ? wp_unslash($_GET['invites']) : ''); ?>">
                </div>
                <button type="submit" class="search-submit" aria-label="Lancer la recherche">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                        <line x1="16.5" y1="16.5" x2="21" y2="21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>

            <!-- Recherche simple (mobile et tablette), composant Input Search -->
            <form class="input-search header-search-simple" action="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" role="search" aria-label="Rechercher un espace">
                <input type="search" name="recherche" placeholder="Trouver un espace">
                <button type="submit" class="input-search__submit" aria-label="Lancer la recherche">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                        <line x1="16.5" y1="16.5" x2="21" y2="21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>
            <!-- Navigation -->
            <nav class="header-nav" aria-label="Navigation principale">
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="header-explorer">Explorer</a>
                <a href="<?php echo esc_url(home_url('/proposer/')); ?>" class="btn btn-secondary header-cta">Proposer votre espace</a>
                <button type="button" class="btn btn-tertiary header-burger" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-menu">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <line x1="4" y1="7" x2="20" y2="7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="4" y1="12" x2="20" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="4" y1="17" x2="20" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>Menu</span>
                </button>

                <!-- Menu déroulant ancré dans header-nav pour rester collé
                     sous le bouton Menu et passer par-dessus la barre de
                     recherche en responsive. Sections visiteur / connecté
                     basculées par la classe is-connected posée sur body. -->
                <nav class="main-menu" id="main-menu" aria-label="Menu" hidden>
            <?php if (has_nav_menu('principal')) : ?>
                <?php
                // Menu géré depuis Apparence > Menus ; le walker rend
                // des liens nus pour conserver le markup du statique
                wp_nav_menu(array(
                    'theme_location' => 'principal',
                    'container'       => 'div',
                    'container_class' => 'main-menu__section',
                    'items_wrap'      => '%3$s',
                    'walker'          => new PoolParty_G4_Walker_Liens(),
                ));
                ?>
            <?php else : ?>
            <div class="main-menu__section">
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>">Explorer</a>
                <a href="<?php echo esc_url(home_url('/actualites/')); ?>">Actualités</a>
                <a href="<?php echo esc_url(home_url('/proposer/')); ?>" class="main-menu__strong">Proposer votre espace</a>
            </div>
            <?php endif; ?>
            <hr class="main-menu__sep">
            <div class="main-menu__section main-menu__section--visiteur">
                <a href="#" class="js-open-login">Connexion</a>
                <a href="<?php echo esc_url(home_url('/inscription/')); ?>">Inscription</a>
            </div>
            <div class="main-menu__section main-menu__section--connecte">
                <a href="<?php echo esc_url(home_url('/mes-reservations/')); ?>">Mes réservations</a>
                <a href="<?php echo esc_url(home_url('/favoris/')); ?>">Mes favoris</a>
                <a href="<?php echo esc_url(home_url('/messages/')); ?>">Messagerie</a>
            </div>
            <hr class="main-menu__sep">
            <div class="main-menu__section">
                <a href="<?php echo esc_url(home_url('/faq/')); ?>" class="main-menu__aide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                    Aide
                </a>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="main-menu__aide">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 6 10-6"/></svg>
                    Contact
                </a>
                <a href="#" class="main-menu__deconnexion js-logout">Déconnexion</a>
            </div>
                </nav>
            </nav>

        </div>
    </header>
