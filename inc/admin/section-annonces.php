<?php
/**
 * Console admin : section « Annonces ».
 * En haut : les annonces en attente de validation (Valider / Refuser).
 * En bas : toutes les annonces du site pour la modération, avec filtre
 * par statut et actions : Voir, Modifier (le formulaire de « Mes
 * annonces », accessible à l'admin pour aider un membre), Retirer du
 * site (conservée en brouillon) ou Republier.
 */

if (!defined('ABSPATH')) {
    exit;
}

$pp_attente = poolparty_g4_admin_biens_en_attente();

$pp_filtre = isset($_GET['statut']) ? sanitize_key($_GET['statut']) : '';
$pp_toutes = poolparty_g4_admin_toutes_annonces($pp_filtre);

$pp_libelles_statut = array(
    'publish' => array('pp-admin__etat--ok', 'En ligne'),
    'pending' => array('pp-admin__etat--attente', 'En attente'),
    'draft'   => array('pp-admin__etat--neutre', 'Retirée'),
);
?>

<section class="pp-admin__bloc">
    <div class="pp-admin__bloc-entete">
        <h2>En attente de validation</h2>
    </div>
    <?php if (empty($pp_attente)) : ?>
        <p class="pp-admin__vide">Aucune annonce en attente. Tout est à jour.</p>
    <?php else : ?>
        <?php foreach ($pp_attente as $pp_bien) {
            poolparty_g4_admin_ligne_annonce($pp_bien);
        } ?>
    <?php endif; ?>
</section>

<section class="pp-admin__bloc">
    <div class="pp-admin__bloc-entete">
        <h2>Toutes les annonces du site</h2>
    </div>

    <form method="get" action="<?php echo esc_url(poolparty_g4_admin_url('annonces')); ?>" class="pp-admin__filtres">
        <input type="hidden" name="section" value="annonces">
        <label for="pp-filtre-annonces" class="screen-reader-text">Filtrer par statut</label>
        <select name="statut" id="pp-filtre-annonces" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            <option value="publish" <?php selected($pp_filtre, 'publish'); ?>>En ligne</option>
            <option value="pending" <?php selected($pp_filtre, 'pending'); ?>>En attente</option>
            <option value="draft" <?php selected($pp_filtre, 'draft'); ?>>Retirées ou refusées</option>
        </select>
        <noscript><button type="submit" class="btn btn-tertiary btn-small">Filtrer</button></noscript>
    </form>

    <?php if (empty($pp_toutes)) : ?>
        <p class="pp-admin__vide">Aucune annonce ne correspond à ce filtre.</p>
    <?php else : ?>
        <div class="pp-admin__table-wrap">
            <table class="pp-admin__table">
                <thead>
                    <tr>
                        <th>Annonce</th>
                        <th>Propriétaire</th>
                        <th>Prix</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pp_toutes as $pp_bien) :
                        $pp_auteur = get_userdata((int) $pp_bien->post_author);
                        $pp_prix   = get_post_meta($pp_bien->ID, 'pp_prix_heure', true);
                        $pp_motif  = get_post_meta($pp_bien->ID, 'pp_refus_motif', true);
                        $pp_badge  = isset($pp_libelles_statut[$pp_bien->post_status]) ? $pp_libelles_statut[$pp_bien->post_status] : $pp_libelles_statut['draft'];
                        ?>
                        <tr>
                            <td>
                                <?php echo esc_html(get_the_title($pp_bien)); ?>
                                <?php if ($pp_motif) : ?>
                                    <br><small>Motif du refus : <?php echo esc_html($pp_motif); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($pp_auteur ? $pp_auteur->display_name : 'Membre'); ?></td>
                            <td><?php echo $pp_prix ? esc_html($pp_prix) . ' €/h' : 'Non renseigné'; ?></td>
                            <td><span class="pp-admin__etat <?php echo esc_attr($pp_badge[0]); ?>"><?php echo esc_html($pp_badge[1]); ?></span></td>
                            <td>
                                <div class="pp-admin__inline-actions">
                                    <?php if ($pp_bien->post_status === 'publish') : ?>
                                        <a class="pp-admin__lien-action" href="<?php echo esc_url(get_permalink($pp_bien)); ?>" target="_blank" rel="noopener">Voir</a>
                                    <?php endif; ?>
                                    <a class="pp-admin__lien-action" href="<?php echo esc_url(add_query_arg('annonce', $pp_bien->ID, home_url('/mes-annonces/'))); ?>">Modifier</a>
                                    <?php if ($pp_bien->post_status === 'publish') : ?>
                                        <form method="post" action="" onsubmit="return confirm('Retirer cette annonce du site ? Elle est conservée et pourra être republiée.');">
                                            <input type="hidden" name="pp_admin_page" value="administration">
                                            <input type="hidden" name="pp_admin_action" value="retirer_annonce">
                                            <input type="hidden" name="section" value="annonces">
                                            <input type="hidden" name="post_id" value="<?php echo esc_attr($pp_bien->ID); ?>">
                                            <?php wp_nonce_field('pp_admin_retirer_annonce'); ?>
                                            <button type="submit" class="pp-admin__lien-action pp-admin__lien-action--danger">Retirer</button>
                                        </form>
                                    <?php elseif ($pp_bien->post_status === 'draft') : ?>
                                        <form method="post" action="">
                                            <input type="hidden" name="pp_admin_page" value="administration">
                                            <input type="hidden" name="pp_admin_action" value="publier_annonce">
                                            <input type="hidden" name="section" value="annonces">
                                            <input type="hidden" name="post_id" value="<?php echo esc_attr($pp_bien->ID); ?>">
                                            <?php wp_nonce_field('pp_admin_publier_annonce'); ?>
                                            <button type="submit" class="pp-admin__lien-action">Republier</button>
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

<?php poolparty_g4_admin_modale_refus(); ?>
