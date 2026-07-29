<?php
/**
 * En-tête allégé du tunnel « Proposer votre espace ».
 * Repris de proposer.html : pendant le parcours on retire la recherche
 * pour garder l'utilisateur concentré (modèle Airbnb), mais on conserve
 * le menu burger pour pouvoir circuler sur le site. Les actions du tunnel
 * (aide, enregistrer et quitter) sont descendues sur une ligne dédiée
 * sous l'en-tête. Appelé via get_header('proposer') depuis page-proposer.php.
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
    <a class="skip-link" href="#proposer-parcours">Aller au contenu</a>

    <!-- HEADER allégé : logo + menu burger (pour circuler sur le site) -->
    <header class="proposer-header">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="header-logo" aria-label="Accueil Pool Party">
            <img src="<?php echo esc_url(pp_asset('assets/images/logo/logo-full.png')); ?>" alt="Pool Party" class="header-logo-full" decoding="async" width="459" height="174">
            <img src="<?php echo esc_url(pp_asset('assets/images/logo/logo-icon.png')); ?>" alt="Pool Party" class="header-logo-icon" decoding="async" width="127" height="158">
        </a>

        <!-- Navigation : même menu déroulant que l'en-tête principal, pour
             pouvoir quitter le tunnel vers n'importe quelle page du site. -->
        <nav class="header-nav proposer-header__nav" aria-label="Navigation principale">
            <button type="button" class="btn btn-tertiary header-burger" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-menu">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <line x1="4" y1="7" x2="20" y2="7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="4" y1="12" x2="20" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="4" y1="17" x2="20" y2="17" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span>Menu</span>
            </button>

            <?php get_template_part('template-parts/menu-burger'); ?>
        </nav>
    </header>

    <!-- Actions du tunnel descendues sous l'en-tête. Simples liens au texte
         souligné (comme Partager / Favori, mais sans icône) pour les
         distinguer nettement du bouton Menu. -->
    <div class="proposer-actions-bar">
        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="proposer-action">Des questions ?</a>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="proposer-action js-quitter">Enregistrer et quitter</a>
    </div>
