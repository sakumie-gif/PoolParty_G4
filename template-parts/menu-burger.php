<?php
/**
 * Menu déroulant du bouton « Menu » (burger), commun à l'en-tête principal
 * et au tunnel « Proposer votre espace ». Un seul fichier pour toutes les
 * pages afin d'éviter toute divergence entre en-têtes.
 *
 * Ordre des blocs validé par Audrey :
 *   1. Explorer, Proposer votre espace (mis en avant : semi-gras, taille
 *      au-dessus du reste du menu)
 *   2. Espace membre (visible une fois connecté) : Messagerie, Réservations,
 *      Mes annonces, Mes favoris
 *   3. Actualités (icône journal), Aide, Contact
 *   4. Administration (équipe), Connexion / Déconnexion
 *
 * La page affichée n'est plus signalée en gras mais par une petite barre
 * verticale qui grandit à gauche du lien (repère .is-current, façon onglet).
 */
$pp_compteurs = pp_compteurs_menu();
$pp_nonlus    = $pp_compteurs['nonlus'];
$pp_demandes  = $pp_compteurs['demandes'];
?>
<nav class="main-menu" id="main-menu" aria-label="Menu" hidden>

    <!-- Bloc 1 : navigation principale mise en avant -->
    <div class="main-menu__section">
        <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>"<?php echo pp_menu_lien_attr(is_post_type_archive('bien') || is_singular('bien') || is_tax('categorie_bien'), 'main-menu__vedette'); ?>>Explorer</a>
        <a href="<?php echo esc_url(home_url('/proposer/')); ?>"<?php echo pp_menu_lien_attr(is_page('proposer'), 'main-menu__vedette'); ?>>Proposer votre espace</a>
    </div>

    <!-- Bloc 2 : espace membre (basculé par la classe is-connected du body) -->
    <hr class="main-menu__sep main-menu__sep--connecte">
    <div class="main-menu__section main-menu__section--connecte">
        <a href="<?php echo esc_url(home_url('/messages/')); ?>"<?php echo pp_menu_lien_attr(is_page('messages')); ?>>Messagerie
            <span class="menu-pastille" data-pastille="messages"<?php echo $pp_nonlus ? '' : ' hidden'; ?>><?php echo esc_html($pp_nonlus > 9 ? '9+' : (string) $pp_nonlus); ?></span>
        </a>
        <a href="<?php echo esc_url(home_url('/mes-reservations/')); ?>"<?php echo pp_menu_lien_attr(is_page('mes-reservations')); ?>>Réservations
            <span class="menu-pastille" data-pastille="demandes"<?php echo $pp_demandes ? '' : ' hidden'; ?>><?php echo esc_html($pp_demandes > 9 ? '9+' : (string) $pp_demandes); ?></span>
        </a>
        <?php if (function_exists('poolparty_g4_membre_a_des_annonces') && poolparty_g4_membre_a_des_annonces()) : ?>
            <a href="<?php echo esc_url(home_url('/mes-annonces/')); ?>"<?php echo pp_menu_lien_attr(is_page('mes-annonces')); ?>>Mes annonces</a>
        <?php endif; ?>
        <a href="<?php echo esc_url(home_url('/favoris/')); ?>"<?php echo pp_menu_lien_attr(is_page('favoris')); ?>>Mes favoris</a>
        <a href="<?php echo esc_url(home_url('/mon-compte/')); ?>"<?php echo pp_menu_lien_attr(is_page('mon-compte')); ?>>Mon compte</a>
    </div>

    <!-- Bloc 3 : contenus et aide -->
    <hr class="main-menu__sep">
    <div class="main-menu__section">
        <a href="<?php echo esc_url(home_url('/actualites/')); ?>"<?php echo pp_menu_lien_attr(is_page('actualites'), 'main-menu__ico'); ?>>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
            Actualités
        </a>
        <a href="<?php echo esc_url(home_url('/faq/')); ?>"<?php echo pp_menu_lien_attr(is_page('faq'), 'main-menu__ico'); ?>>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
            Aide
        </a>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>"<?php echo pp_menu_lien_attr(is_page('contact'), 'main-menu__ico'); ?>>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 6 10-6"/></svg>
            Contact
        </a>
    </div>

    <!-- Bloc 4 : compte et administration -->
    <hr class="main-menu__sep">
    <div class="main-menu__section">
        <?php // Repli de la bascule « Mode administrateur » du header : ne
              // s'affiche dans le burger que sous 768px (CSS .main-menu__admin),
              // quand le bouton du header n'a plus la place. Sans gras. ?>
        <?php if (current_user_can('manage_options')) : ?>
            <a href="<?php echo esc_url(home_url('/administration/')); ?>"<?php echo pp_menu_lien_attr(is_page('administration'), 'main-menu__admin'); ?>>Mode administrateur</a>
        <?php endif; ?>
        <a href="#" class="js-open-login main-menu__visiteur">Connexion</a>
        <a href="<?php echo esc_url(home_url('/inscription/')); ?>" class="main-menu__visiteur"<?php echo is_page('inscription') ? ' aria-current="page"' : ''; ?>>Inscription</a>
        <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="main-menu__deconnexion">Déconnexion</a>
    </div>

</nav>
