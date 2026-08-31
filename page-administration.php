<?php
/**
 * Gabarit de la console d'administration (page /administration/).
 * -------------------------------------------------------------
 * Espace de l'équipe Pool Party, aux couleurs du site mais distingué
 * par une dominante verte (bandeau vert bleuté foncé, fonds vert pâle).
 * Réservé à l'administrateur : chaque affichage et chaque action exige
 * current_user_can('manage_options') (voir inc/admin-site.php pour le
 * traitement des actions). Page privée : noindex (voir functions.php).
 *
 * Les données proviennent des mêmes sources que le reste du site :
 * annonces en attente (biens pending), réservations (inc/reservations.php),
 * avis (commentaires natifs, inc/avis.php), membres (utilisateurs WP).
 * Aucun e-mail ni téléphone de membre n'est affiché (règle du projet).
 */

get_header('administration');

$pp_connecte = is_user_logged_in();
$pp_admin    = current_user_can('manage_options');
$pp_section  = poolparty_g4_admin_section_courante();
$pp_sections = poolparty_g4_admin_sections();
$pp_flash    = poolparty_g4_admin_flash();
?>

<main id="contenu" class="pp-admin">
    <div class="pp-admin__inner">

        <?php if (!$pp_admin) : ?>

            <!-- Accès réservé : non connecté ou membre sans droits admin -->
            <div class="pp-admin__garde">
                <?php if (!$pp_connecte) : ?>
                    <h1>Espace réservé à l'administration</h1>
                    <p>Cette console est réservée à l'équipe Pool Party. Connectez-vous avec un compte administrateur pour y accéder.</p>
                    <button type="button" class="btn btn-primary js-open-login">Se connecter</button>
                <?php else : ?>
                    <h1>Accès non autorisé</h1>
                    <p>Votre compte n'a pas les droits d'administration. Si vous pensez qu'il s'agit d'une erreur, contactez l'équipe Pool Party.</p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">Retour à l'accueil</a>
                <?php endif; ?>
            </div>

        <?php else : ?>

            <?php
            $pp_titres = array(
                'tableau-de-bord' => array('Tableau de bord', 'Vue d\'ensemble de la plateforme : annonces à valider, dernières réservations, membres et avis à modérer.'),
                'annonces'        => array('Annonces', 'Validez les espaces déposés par les membres et modérez toutes les annonces du site.'),
                'reservations'    => array('Suivi des réservations', 'Toutes les demandes de réservation de la plateforme et leur statut.'),
                'incidents'       => array('Incidents', 'Les signalements déposés par les membres sur leurs réservations : traitement, note interne et clôture.'),
                'membres'         => array('Membres', 'Les personnes inscrites sur Pool Party : activité, annonces, réservations et modération des comptes.'),
                'avis'            => array('Modération des avis', 'Les avis déposés sur les espaces et les locataires. Masquer retire un avis du site sans le supprimer.'),
                'reglages'        => array('Réglages', 'Les réglages du site : référencement, statistiques, e-mails et menus.'),
            );
            $pp_titre = $pp_titres[$pp_section];
            $pp_stats = poolparty_g4_admin_stats();
            ?>

            <!-- Bandeau de page (variante B) -->
            <div class="pp-admin__hero">
                <p class="pp-admin__surtitre">Espace administrateur</p>
                <h1><?php echo esc_html($pp_titre[0]); ?></h1>
                <p class="pp-admin__hero-texte"><?php echo esc_html($pp_titre[1]); ?></p>
            </div>

            <?php if ($pp_flash) : ?>
                <div class="pp-admin__flash<?php echo $pp_flash[1] === 'refus' ? ' pp-admin__flash--refus' : ''; ?>" role="status">
                    <?php echo esc_html($pp_flash[0]); ?>
                </div>
            <?php endif; ?>

            <div class="pp-admin__layout">

                <!-- Menu latéral -->
                <nav class="pp-admin__nav" aria-label="Sections de l'administration">
                    <?php foreach ($pp_sections as $pp_slug => $pp_label) :
                        $pp_pastille = 0;
                        if ($pp_slug === 'annonces') {
                            $pp_pastille = $pp_stats['biens_attente'];
                        } elseif ($pp_slug === 'avis') {
                            $pp_pastille = $pp_stats['avis_masques'];
                        } elseif ($pp_slug === 'incidents') {
                            $pp_pastille = $pp_stats['incidents_ouverts'];
                        }
                        ?>
                        <a href="<?php echo esc_url(poolparty_g4_admin_url($pp_slug)); ?>"<?php echo $pp_slug === $pp_section ? ' class="is-active" aria-current="page"' : ''; ?>>
                            <span><?php echo esc_html($pp_label); ?></span>
                            <?php if ($pp_pastille > 0) : ?>
                                <span class="pp-admin__pastille"><?php echo esc_html($pp_pastille); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <!-- Contenu de la section -->
                <div class="pp-admin__content">
                    <?php
                    $pp_partiel = get_theme_file_path('inc/admin/section-' . $pp_section . '.php');
                    if (file_exists($pp_partiel)) {
                        include $pp_partiel;
                    }
                    ?>
                </div>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>
