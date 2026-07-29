<?php
/**
 * En-tête épuré de la console d'administration (/administration/).
 * Pas de barre de recherche ni de menu burger : le logo, le badge
 * Administration et la ligne de session (Connecté, Voir le site,
 * Se déconnecter). Même principe que header-proposer.php pour le
 * tunnel Proposer.
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class('pp-admin-body'); ?>>
<?php wp_body_open(); ?>

    <!-- Lien d'évitement pour la navigation au clavier -->
    <a class="skip-link" href="#contenu">Aller au contenu</a>

    <header class="site-header pp-admin-entete">
        <div class="header-inner pp-admin-entete__inner">

            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="header-logo" aria-label="Accueil Pool Party">
                <img src="<?php echo esc_url(pp_asset('assets/images/logo/logo-full.png')); ?>" alt="Pool Party" class="header-logo-full" decoding="async" width="459" height="174">
                <img src="<?php echo esc_url(pp_asset('assets/images/logo/logo-icon.png')); ?>" alt="Pool Party" class="header-logo-icon" decoding="async" width="127" height="158">
            </a>

            <!-- Ligne d'administration : badge + session -->
            <div class="pp-admin__topbar pp-admin__topbar--entete">
                <span class="pp-admin__badge">Administration</span>
                <?php if (current_user_can('manage_options')) :
                    $pp_entete_user = wp_get_current_user();
                    ?>
                    <div class="pp-admin__session">
                        <span>Connecté&nbsp;: <strong><?php echo esc_html($pp_entete_user->display_name); ?></strong></span>
                        <a href="<?php echo esc_url(home_url('/')); ?>">Mode membre</a>
                        <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>">Se déconnecter</a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </header>
