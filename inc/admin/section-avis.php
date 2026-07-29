<?php
/**
 * Console admin : section « Modération des avis ».
 * Les avis sont des commentaires WordPress natifs (pp_avis, pp_avis_reponse,
 * pp_avis_locataire). On réutilise leurs statuts : « Masquer » retire l'avis
 * du site sans le supprimer (statut hold), « Rendre visible » le republie,
 * « Corbeille » l'archive. Actions par POST protégé (voir inc/admin-site.php).
 */

if (!defined('ABSPATH')) {
    exit;
}

$pp_types  = poolparty_g4_types_avis();
$pp_type   = (isset($_GET['type']) && isset($pp_types[sanitize_key($_GET['type'])])) ? sanitize_key($_GET['type']) : '';
$pp_statut = isset($_GET['visibilite']) ? sanitize_key($_GET['visibilite']) : '';

$pp_args = array(
    'type__in' => $pp_type ? array($pp_type) : array_keys($pp_types),
    'status'   => 'all',
    'orderby'  => 'comment_date',
    'order'    => 'DESC',
    'number'   => 60,
);
if ($pp_statut === 'visible') {
    $pp_args['status'] = 'approve';
} elseif ($pp_statut === 'masque') {
    $pp_args['status'] = 'hold';
} elseif ($pp_statut === 'corbeille') {
    $pp_args['status'] = 'trash';
}
$pp_vue_corbeille = ($pp_statut === 'corbeille');
$pp_liste = get_comments($pp_args);
$pp_section = 'avis';
?>

<form method="get" action="<?php echo esc_url(poolparty_g4_admin_url('avis')); ?>" class="pp-admin__filtres">
    <input type="hidden" name="section" value="avis">
    <select name="type" onchange="this.form.submit()">
        <option value="">Tous les types</option>
        <?php foreach ($pp_types as $pp_cle => $pp_label) : ?>
            <option value="<?php echo esc_attr($pp_cle); ?>" <?php selected($pp_type, $pp_cle); ?>><?php echo esc_html($pp_label); ?></option>
        <?php endforeach; ?>
    </select>
    <select name="visibilite" onchange="this.form.submit()">
        <option value="">Toutes les visibilités</option>
        <option value="visible" <?php selected($pp_statut, 'visible'); ?>>Visible sur le site</option>
        <option value="masque" <?php selected($pp_statut, 'masque'); ?>>Masqué</option>
        <option value="corbeille" <?php selected($pp_statut, 'corbeille'); ?>>Corbeille</option>
    </select>
    <noscript><button type="submit" class="btn btn-tertiary btn-small">Filtrer</button></noscript>
</form>

<section class="pp-admin__bloc">
    <div class="pp-admin__bloc-entete">
        <h2><?php echo esc_html(count($pp_liste)); ?> avis</h2>
    </div>
    <?php if (empty($pp_liste)) : ?>
        <p class="pp-admin__vide">Aucun avis ne correspond à ces critères.</p>
    <?php else : ?>
        <div class="pp-admin__table-wrap">
            <table class="pp-admin__table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Auteur</th>
                        <th>Note</th>
                        <th>Contenu</th>
                        <th>Concerne</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pp_liste as $pp_avis) :
                        $pp_note    = (int) get_comment_meta($pp_avis->comment_ID, 'pp_note', true);
                        $pp_visible = wp_get_comment_status($pp_avis) === 'approved';
                        $pp_cible   = get_the_title((int) $pp_avis->comment_post_ID);
                        ?>
                        <tr>
                            <td><?php echo esc_html(isset($pp_types[$pp_avis->comment_type]) ? $pp_types[$pp_avis->comment_type] : $pp_avis->comment_type); ?></td>
                            <td><?php echo esc_html($pp_avis->comment_author ? $pp_avis->comment_author : 'Membre'); ?></td>
                            <td><?php echo $pp_note ? '<span class="pp-admin__note">' . esc_html(str_repeat('★', $pp_note)) . '</span>' : 'Sans note'; ?></td>
                            <td><?php echo esc_html(wp_trim_words($pp_avis->comment_content, 16, '…')); ?></td>
                            <td><?php echo esc_html($pp_cible ? $pp_cible : 'Non précisée'); ?></td>
                            <td>
                                <?php if ($pp_visible) : ?>
                                    <span class="pp-admin__etat pp-admin__etat--ok">Visible</span>
                                <?php else : ?>
                                    <span class="pp-admin__etat pp-admin__etat--neutre">Masqué</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="pp-admin__inline-actions">
                                    <?php if ($pp_vue_corbeille) : ?>
                                        <form method="post" action="">
                                            <input type="hidden" name="pp_admin_page" value="administration">
                                            <input type="hidden" name="pp_admin_action" value="avis_restaurer">
                                            <input type="hidden" name="section" value="avis">
                                            <input type="hidden" name="comment_id" value="<?php echo esc_attr($pp_avis->comment_ID); ?>">
                                            <?php wp_nonce_field('pp_admin_avis_restaurer'); ?>
                                            <button type="submit" class="pp-admin__lien-action">Restaurer</button>
                                        </form>
                                        <form method="post" action="" onsubmit="return confirm('Supprimer définitivement cet avis ? Cette action est irréversible.');">
                                            <input type="hidden" name="pp_admin_page" value="administration">
                                            <input type="hidden" name="pp_admin_action" value="avis_detruire">
                                            <input type="hidden" name="section" value="avis">
                                            <input type="hidden" name="comment_id" value="<?php echo esc_attr($pp_avis->comment_ID); ?>">
                                            <?php wp_nonce_field('pp_admin_avis_detruire'); ?>
                                            <button type="submit" class="pp-admin__lien-action pp-admin__lien-action--danger">Supprimer définitivement</button>
                                        </form>
                                    <?php else : ?>
                                        <form method="post" action="">
                                            <input type="hidden" name="pp_admin_page" value="administration">
                                            <input type="hidden" name="pp_admin_action" value="<?php echo $pp_visible ? 'avis_masquer' : 'avis_visible'; ?>">
                                            <input type="hidden" name="section" value="avis">
                                            <input type="hidden" name="comment_id" value="<?php echo esc_attr($pp_avis->comment_ID); ?>">
                                            <?php wp_nonce_field($pp_visible ? 'pp_admin_avis_masquer' : 'pp_admin_avis_visible'); ?>
                                            <button type="submit" class="pp-admin__lien-action"><?php echo $pp_visible ? 'Masquer' : 'Rendre visible'; ?></button>
                                        </form>
                                        <form method="post" action="" onsubmit="return confirm('Déplacer cet avis dans la corbeille ?');">
                                            <input type="hidden" name="pp_admin_page" value="administration">
                                            <input type="hidden" name="pp_admin_action" value="avis_corbeille">
                                            <input type="hidden" name="section" value="avis">
                                            <input type="hidden" name="comment_id" value="<?php echo esc_attr($pp_avis->comment_ID); ?>">
                                            <?php wp_nonce_field('pp_admin_avis_corbeille'); ?>
                                            <button type="submit" class="pp-admin__trash" aria-label="Mettre à la corbeille" title="Corbeille"><?php echo poolparty_g4_admin_icone_trash(); ?></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
