<?php
/**
 * Console admin : section « Réglages ».
 * Raccourcis vers les réglages qui vivent dans le back-office WordPress
 * (référencement, statistiques, e-mails, menus, contenus). L'administrateur
 * y a accès ; ces cartes ne font que pointer vers le bon écran.
 */

if (!defined('ABSPATH')) {
    exit;
}

$pp_reglages = array(
    array(
        'titre' => 'Référencement (SEO)',
        'texte' => 'Titres, métadescriptions et aperçu Google, gérés par Yoast SEO.',
        'url'   => admin_url('admin.php?page=wpseo_dashboard'),
    ),
    array(
        'titre' => 'Statistiques (Google Analytics)',
        'texte' => 'Le suivi GA4 est actif sur le site en ligne (propriété G-6YNBKZHM0Y). Consultez les audiences dans Google Analytics.',
        'url'   => 'https://analytics.google.com/',
    ),
    array(
        'titre' => 'E-mails du site',
        'texte' => 'Envoi des e-mails transactionnels (contact, annonces, réservations) via WP Mail SMTP.',
        'url'   => admin_url('admin.php?page=wp-mail-smtp'),
    ),
    array(
        'titre' => 'Menus de navigation',
        'texte' => 'Liens du menu principal et du pied de page (Apparence > Menus).',
        'url'   => admin_url('nav-menus.php'),
    ),
    array(
        'titre' => 'Contenus de l\'accueil',
        'texte' => 'Textes modifiables de la page d\'accueil (Apparence > Personnaliser).',
        'url'   => admin_url('customize.php'),
    ),
    array(
        'titre' => 'Back-office complet',
        'texte' => 'Tous les réglages avancés de WordPress (extensions, utilisateurs, réglages généraux).',
        'url'   => admin_url(),
    ),
);
?>

<section class="pp-admin__bloc">
    <div class="pp-admin__bloc-entete">
        <h2>Réglages du site</h2>
    </div>
    <p class="pp-admin__vide">Ces réglages s'ouvrent dans le back-office WordPress, réservé à l'administration.</p>
    <div class="pp-admin__reglages">
        <?php foreach ($pp_reglages as $pp_r) :
            $pp_externe = strpos($pp_r['url'], 'http') === 0 && strpos($pp_r['url'], admin_url()) !== 0;
            ?>
            <a class="pp-admin__reglage" href="<?php echo esc_url($pp_r['url']); ?>"<?php echo $pp_externe ? ' target="_blank" rel="noopener"' : ''; ?>>
                <h3><?php echo esc_html($pp_r['titre']); ?></h3>
                <p><?php echo esc_html($pp_r['texte']); ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>
