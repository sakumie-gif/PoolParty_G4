<?php
/**
 * Console admin : section « Membres ».
 * Les personnes inscrites (hors administrateurs) : depuis quand le compte
 * est actif (ou bloqué), nombre d'annonces, nombre de réservations, et la
 * modération des comptes : bloquer (connexion refusée, compte conservé),
 * supprimer. Filtre par état du compte. L'action « Bannir » a été retirée
 * de l'interface le 29-07-2026 (demande d'Audrey : redondante avec
 * Bloquer) ; son circuit reste en place dans inc/admin-site.php.
 * Conformément à la règle du projet, AUCUNE adresse e-mail n'est
 * affichée : seuls le nom et l'activité apparaissent.
 */

if (!defined('ABSPATH')) {
    exit;
}

$pp_filtre  = isset($_GET['etat']) ? sanitize_key($_GET['etat']) : '';
$pp_membres = poolparty_g4_admin_membres();

if ($pp_filtre === 'actifs') {
    $pp_membres = array_values(array_filter($pp_membres, function ($m) {
        return !poolparty_g4_membre_bloque($m->ID);
    }));
} elseif ($pp_filtre === 'bloques') {
    $pp_membres = array_values(array_filter($pp_membres, function ($m) {
        return poolparty_g4_membre_bloque($m->ID);
    }));
}
?>

<form method="get" action="<?php echo esc_url(poolparty_g4_admin_url('membres')); ?>" class="pp-admin__filtres">
    <input type="hidden" name="section" value="membres">
    <label for="pp-filtre-etat" class="screen-reader-text">Filtrer par état du compte</label>
    <select name="etat" id="pp-filtre-etat" onchange="this.form.submit()">
        <option value="">Tous les comptes</option>
        <option value="actifs" <?php selected($pp_filtre, 'actifs'); ?>>Actifs</option>
        <option value="bloques" <?php selected($pp_filtre, 'bloques'); ?>>Bloqués ou bannis</option>
    </select>
    <noscript><button type="submit" class="btn btn-tertiary btn-small">Filtrer</button></noscript>
</form>

<section class="pp-admin__bloc">
    <div class="pp-admin__bloc-entete">
        <h2><?php echo esc_html(count($pp_membres)); ?> membre<?php echo count($pp_membres) > 1 ? 's' : ''; ?></h2>
    </div>
    <?php if (empty($pp_membres)) : ?>
        <p class="pp-admin__vide">Aucun membre ne correspond à ce filtre.</p>
    <?php else : ?>
        <div class="pp-admin__table-wrap">
            <table class="pp-admin__table">
                <thead>
                    <tr>
                        <th>Membre</th>
                        <th>État du compte</th>
                        <th>Annonces</th>
                        <th>Réservations</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pp_membres as $pp_membre) :
                        $pp_bloque = poolparty_g4_membre_bloque($pp_membre->ID);
                        $pp_banni  = poolparty_g4_email_banni($pp_membre->user_email);
                        $pp_inscr  = $pp_membre->user_registered ? mysql2date('d/m/Y', $pp_membre->user_registered) : '';
                        $pp_depuis = get_user_meta($pp_membre->ID, 'pp_bloque_date', true);
                        ?>
                        <tr>
                            <td><?php echo esc_html($pp_membre->display_name); ?></td>
                            <td>
                                <?php if ($pp_banni) : ?>
                                    <span class="pp-admin__etat pp-admin__etat--refus">Banni<?php echo $pp_depuis ? ' depuis le ' . esc_html($pp_depuis) : ''; ?></span>
                                <?php elseif ($pp_bloque) : ?>
                                    <span class="pp-admin__etat pp-admin__etat--refus">Bloqué<?php echo $pp_depuis ? ' depuis le ' . esc_html($pp_depuis) : ''; ?></span>
                                <?php else : ?>
                                    <span class="pp-admin__etat pp-admin__etat--ok">Actif<?php echo $pp_inscr ? ' depuis le ' . esc_html($pp_inscr) : ''; ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html(poolparty_g4_admin_nb_biens($pp_membre->ID)); ?></td>
                            <td><?php echo esc_html(poolparty_g4_admin_nb_resas($pp_membre->ID)); ?></td>
                            <td>
                                <div class="pp-admin__inline-actions">
                                    <?php if ($pp_bloque || $pp_banni) : ?>
                                        <form method="post" action="">
                                            <input type="hidden" name="pp_admin_page" value="administration">
                                            <input type="hidden" name="pp_admin_action" value="debloquer_membre">
                                            <input type="hidden" name="section" value="membres">
                                            <input type="hidden" name="membre_id" value="<?php echo esc_attr($pp_membre->ID); ?>">
                                            <?php wp_nonce_field('pp_admin_debloquer_membre'); ?>
                                            <button type="submit" class="pp-admin__lien-action">Débloquer</button>
                                        </form>
                                    <?php else : ?>
                                        <form method="post" action="" onsubmit="return confirm('Bloquer ce compte ? Le membre ne pourra plus se connecter, son compte est conservé.');">
                                            <input type="hidden" name="pp_admin_page" value="administration">
                                            <input type="hidden" name="pp_admin_action" value="bloquer_membre">
                                            <input type="hidden" name="section" value="membres">
                                            <input type="hidden" name="membre_id" value="<?php echo esc_attr($pp_membre->ID); ?>">
                                            <?php wp_nonce_field('pp_admin_bloquer_membre'); ?>
                                            <button type="submit" class="pp-admin__lien-action">Bloquer</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="post" action="" onsubmit="return confirm('Supprimer ce compte ? Ses annonces et réservations passées seront conservées et réattribuées à l\'administration. La suppression est refusée s\'il a des réservations en cours.');">
                                        <input type="hidden" name="pp_admin_page" value="administration">
                                        <input type="hidden" name="pp_admin_action" value="supprimer_membre">
                                        <input type="hidden" name="section" value="membres">
                                        <input type="hidden" name="membre_id" value="<?php echo esc_attr($pp_membre->ID); ?>">
                                        <?php wp_nonce_field('pp_admin_supprimer_membre'); ?>
                                        <button type="submit" class="pp-admin__trash" aria-label="Supprimer le compte" title="Supprimer le compte"><?php echo poolparty_g4_admin_icone_trash(); ?></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
