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
    <?php if ( is_front_page() ) : ?>
    <!-- Preload de l'image hero (LCP) : version legere 96 Ko servie par defaut -->
    <link rel="preload" as="image" href="<?php echo esc_url(pp_asset('assets/images/hero/hero-original.webp')); ?>" fetchpriority="high">
    <?php endif; ?>
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
                <img src="<?php echo esc_url(pp_asset('assets/images/logo/logo-full.png')); ?>" alt="Pool Party" class="header-logo-full" decoding="async" width="459" height="174">
                <img src="<?php echo esc_url(pp_asset('assets/images/logo/logo-icon.png')); ?>" alt="Pool Party" class="header-logo-icon" decoding="async" width="127" height="158">
            </a>

            <!-- Barre de recherche (visible sur grand écran) -->
            <form class="header-search" action="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" role="search" aria-label="Rechercher un espace">
                <div class="search-field">
                    <label for="search-quoi">Quoi</label>
                    <input type="text" id="search-quoi" name="quoi" placeholder="Choisissez votre bien" value="<?php echo esc_attr(isset($_GET['quoi']) ? sanitize_text_field(wp_unslash($_GET['quoi'])) : ''); ?>">
                </div>
                <span class="search-divider" aria-hidden="true"></span>
                <div class="search-field">
                    <label for="search-adresse">Adresse</label>
                    <input type="text" id="search-adresse" name="adresse" placeholder="Où cherchez-vous ?" value="<?php echo esc_attr(isset($_GET['adresse']) ? sanitize_text_field(wp_unslash($_GET['adresse'])) : ''); ?>">
                </div>
                <span class="search-divider" aria-hidden="true"></span>
                <div class="search-field search-field--small">
                    <label for="search-date">Date</label>
                    <input type="text" id="search-date" name="date" placeholder="Quand ?" value="<?php echo esc_attr(isset($_GET['date']) ? sanitize_text_field(wp_unslash($_GET['date'])) : ''); ?>">
                </div>
                <span class="search-divider" aria-hidden="true"></span>
                <div class="search-field search-field--small">
                    <label for="search-invites">Invités</label>
                    <input type="text" id="search-invites" name="invites" placeholder="Combien ?" value="<?php echo esc_attr(isset($_GET['invites']) ? sanitize_text_field(wp_unslash($_GET['invites'])) : ''); ?>">
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
                <input type="search" name="recherche" placeholder="Trouver un espace" aria-label="Trouver un espace">
                <button type="submit" class="input-search__submit" aria-label="Lancer la recherche">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2"/>
                        <line x1="16.5" y1="16.5" x2="21" y2="21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </form>
            <?php
            // Pastilles de notification du menu : messages non lus et
            // demandes de réservation en attente côté hôte (calcul partagé
            // avec le menu déroulant). Tenues à jour par main.js.
            $pp_compteurs = pp_compteurs_menu();
            $pp_nonlus    = $pp_compteurs['nonlus'];
            $pp_demandes  = $pp_compteurs['demandes'];
            ?>
            <!-- Navigation -->
            <nav class="header-nav" aria-label="Navigation principale">
                <?php if (current_user_can('manage_options')) : ?>
                    <?php // Bascule façon Airbnb : l'admin n'a pas besoin d'« Explorer »
                          // (déjà dans le menu burger), il bascule vers sa console. ?>
                    <a href="<?php echo esc_url(home_url('/administration/')); ?>" class="header-explorer header-explorer--admin">Mode administrateur</a>
                <?php else : ?>
                    <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="header-explorer">Explorer</a>
                <?php endif; ?>
                <a href="<?php echo esc_url(home_url('/proposer/')); ?>" class="btn btn-secondary header-cta">Proposer votre espace</a>
                <button type="button" class="btn btn-tertiary header-burger" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-menu">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <line x1="4" y1="7" x2="20" y2="7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="4" y1="12" x2="20" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        <line x1="4" y1="17" x2="20" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    <span>Menu</span>
                    <?php
                    // Point de notification sur le bouton Menu retiré à la
                    // demande d'Audrey (29-07) : les compteurs du menu
                    // déroulant suffisent. Conservé désactivé au cas où :
                    // if ($pp_nonlus || $pp_demandes) {
                    //     echo '<span class="header-burger__pastille" data-pastille="menu" aria-hidden="true"></span>';
                    // }
                    ?>
                </button>

                <!-- Menu déroulant ancré dans header-nav pour rester collé
                     sous le bouton Menu et passer par-dessus la barre de
                     recherche en responsive. Markup mutualisé dans
                     template-parts/menu-burger.php (identique sur toutes
                     les pages). -->
                <?php get_template_part('template-parts/menu-burger'); ?>
            </nav>

        </div>
    </header>
