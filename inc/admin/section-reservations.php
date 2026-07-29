<?php
/**
 * Console admin : section « Suivi des réservations ».
 * Tableau de toutes les demandes avec filtre par statut. L'acceptation et
 * le refus restent gérés par l'hôte depuis son espace, mais l'équipe peut
 * corriger une réservation (date, créneau, invités, total, statut) ou la
 * supprimer (corbeille) quand un membre contacte la plateforme. Jamais
 * d'e-mail affiché (contact via la messagerie interne).
 */

if (!defined('ABSPATH')) {
    exit;
}

$pp_statuts = poolparty_g4_statuts_reservation();
$pp_filtre  = isset($_GET['statut']) && isset($pp_statuts[sanitize_key($_GET['statut'])]) ? sanitize_key($_GET['statut']) : '';

// Vue Corbeille : les réservations supprimées, restaurables ou
// supprimables définitivement.
$pp_corbeille = !empty($_GET['corbeille']);
$pp_supprimees = get_posts(array(
    'post_type'      => 'reservation',
    'post_status'    => 'trash',
    'posts_per_page' => -1,
    'orderby'        => 'modified',
    'order'          => 'DESC',
));
$pp_liste = $pp_corbeille ? $pp_supprimees : poolparty_g4_admin_reservations($pp_filtre);
?>

<?php if ($pp_corbeille) : ?>

<div class="pp-admin__filtres">
    <a class="pp-admin__bloc-lien" href="<?php echo esc_url(poolparty_g4_admin_url('reservations')); ?>">&larr; Retour au suivi</a>
</div>

<section class="pp-admin__bloc">
    <div class="pp-admin__bloc-entete">
        <h2>Corbeille : <?php echo esc_html(count($pp_liste)); ?> réservation<?php echo count($pp_liste) > 1 ? 's' : ''; ?></h2>
    </div>
    <?php if (empty($pp_liste)) : ?>
        <p class="pp-admin__vide">La corbeille est vide.</p>
    <?php else : ?>
        <div class="pp-admin__table-wrap">
            <table class="pp-admin__table">
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Date de venue</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pp_liste as $pp_resa) :
                        $pp_bien_id = (int) get_post_meta($pp_resa->ID, 'pp_bien_id', true);
                        $pp_loc     = get_userdata((int) $pp_resa->post_author);
                        $pp_creneau = get_post_meta($pp_resa->ID, 'pp_creneau', true);
                        ?>
                        <tr>
                            <td><?php echo esc_html($pp_bien_id ? get_the_title($pp_bien_id) : $pp_resa->post_title); ?></td>
                            <td><?php echo esc_html($pp_loc ? $pp_loc->display_name : 'Membre'); ?></td>
                            <td><?php echo esc_html(get_post_meta($pp_resa->ID, 'pp_date', true) . ($pp_creneau ? ' · ' . $pp_creneau : '')); ?></td>
                            <td><?php echo esc_html(get_post_meta($pp_resa->ID, 'pp_total', true)); ?></td>
                            <td>
                                <div class="pp-admin__inline-actions">
                                    <form method="post" action="">
                                        <input type="hidden" name="pp_admin_page" value="administration">
                                        <input type="hidden" name="pp_admin_action" value="restaurer_resa">
                                        <input type="hidden" name="section" value="reservations">
                                        <input type="hidden" name="resa_id" value="<?php echo esc_attr($pp_resa->ID); ?>">
                                        <?php wp_nonce_field('pp_admin_restaurer_resa'); ?>
                                        <button type="submit" class="pp-admin__lien-action">Restaurer</button>
                                    </form>
                                    <form method="post" action="" onsubmit="return confirm('Supprimer définitivement cette réservation ? Cette action est irréversible.');">
                                        <input type="hidden" name="pp_admin_page" value="administration">
                                        <input type="hidden" name="pp_admin_action" value="detruire_resa">
                                        <input type="hidden" name="section" value="reservations">
                                        <input type="hidden" name="resa_id" value="<?php echo esc_attr($pp_resa->ID); ?>">
                                        <?php wp_nonce_field('pp_admin_detruire_resa'); ?>
                                        <button type="submit" class="pp-admin__lien-action pp-admin__lien-action--danger">Supprimer définitivement</button>
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

<?php return; ?>
<?php endif; ?>

<form method="get" action="<?php echo esc_url(poolparty_g4_admin_url('reservations')); ?>" class="pp-admin__filtres">
    <input type="hidden" name="section" value="reservations">
    <label for="pp-filtre-statut" class="screen-reader-text">Filtrer par statut</label>
    <select name="statut" id="pp-filtre-statut" onchange="this.form.submit()">
        <option value="">Tous les statuts</option>
        <?php foreach ($pp_statuts as $pp_cle => $pp_label) : ?>
            <option value="<?php echo esc_attr($pp_cle); ?>" <?php selected($pp_filtre, $pp_cle); ?>><?php echo esc_html($pp_label); ?></option>
        <?php endforeach; ?>
    </select>
    <noscript><button type="submit" class="btn btn-tertiary btn-small">Filtrer</button></noscript>
    <a class="pp-admin__bloc-lien" href="<?php echo esc_url(add_query_arg(array('section' => 'reservations', 'corbeille' => 1), poolparty_g4_admin_url('reservations'))); ?>">Corbeille (<?php echo esc_html(count($pp_supprimees)); ?>)</a>
</form>

<section class="pp-admin__bloc">
    <div class="pp-admin__bloc-entete">
        <h2><?php echo esc_html(count($pp_liste)); ?> réservation<?php echo count($pp_liste) > 1 ? 's' : ''; ?></h2>
    </div>
    <?php if (empty($pp_liste)) : ?>
        <p class="pp-admin__vide">Aucune réservation ne correspond à ce filtre.</p>
    <?php else : ?>
        <div class="pp-admin__table-wrap">
            <table class="pp-admin__table">
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Date de venue</th>
                        <th>Invités</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pp_liste as $pp_resa) :
                        $pp_bien_id = (int) get_post_meta($pp_resa->ID, 'pp_bien_id', true);
                        $pp_loc     = get_userdata((int) $pp_resa->post_author);
                        $pp_creneau = get_post_meta($pp_resa->ID, 'pp_creneau', true);
                        $pp_badge   = poolparty_g4_admin_badge_resa(get_post_meta($pp_resa->ID, 'pp_statut', true));
                        ?>
                        <tr>
                            <td>
                                <?php if ($pp_bien_id) : ?>
                                    <a href="<?php echo esc_url(get_permalink($pp_bien_id)); ?>" target="_blank" rel="noopener"><?php echo esc_html(get_the_title($pp_bien_id)); ?></a>
                                <?php else : ?>
                                    <?php echo esc_html($pp_resa->post_title); ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($pp_loc ? $pp_loc->display_name : 'Membre'); ?></td>
                            <td><?php echo esc_html(get_post_meta($pp_resa->ID, 'pp_date', true) . ($pp_creneau ? ' · ' . $pp_creneau : '')); ?></td>
                            <td><?php echo esc_html(get_post_meta($pp_resa->ID, 'pp_invites', true)); ?></td>
                            <td><?php echo esc_html(get_post_meta($pp_resa->ID, 'pp_total', true)); ?></td>
                            <td><span class="pp-admin__etat <?php echo esc_attr($pp_badge[0]); ?>"><?php echo esc_html($pp_badge[1]); ?></span></td>
                            <td>
                                <div class="pp-admin__inline-actions">
                                    <button type="button"
                                        class="pp-admin__lien-action js-admin-resa-modifier"
                                        data-resa-id="<?php echo esc_attr($pp_resa->ID); ?>"
                                        data-nom="<?php echo esc_attr(($pp_bien_id ? get_the_title($pp_bien_id) : $pp_resa->post_title) . ', ' . ($pp_loc ? $pp_loc->display_name : 'Membre')); ?>"
                                        data-date="<?php echo esc_attr(get_post_meta($pp_resa->ID, 'pp_date', true)); ?>"
                                        data-creneau="<?php echo esc_attr($pp_creneau); ?>"
                                        data-invites="<?php echo esc_attr(get_post_meta($pp_resa->ID, 'pp_invites', true)); ?>"
                                        data-total="<?php echo esc_attr(get_post_meta($pp_resa->ID, 'pp_total', true)); ?>"
                                        data-statut="<?php echo esc_attr(get_post_meta($pp_resa->ID, 'pp_statut', true)); ?>">Modifier</button>
                                    <form method="post" action="" onsubmit="return confirm('Déplacer cette réservation dans la corbeille ?');">
                                        <input type="hidden" name="pp_admin_page" value="administration">
                                        <input type="hidden" name="pp_admin_action" value="supprimer_resa">
                                        <input type="hidden" name="section" value="reservations">
                                        <input type="hidden" name="resa_id" value="<?php echo esc_attr($pp_resa->ID); ?>">
                                        <?php wp_nonce_field('pp_admin_supprimer_resa'); ?>
                                        <button type="submit" class="pp-admin__trash" aria-label="Mettre à la corbeille" title="Corbeille"><?php echo poolparty_g4_admin_icone_trash(); ?></button>
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

<?php poolparty_g4_admin_modale_resa(); ?>
