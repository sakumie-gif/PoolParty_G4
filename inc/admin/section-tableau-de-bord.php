<?php
/**
 * Console admin : section « Tableau de bord ».
 * Chiffres clés, aperçu des annonces à valider et des dernières
 * réservations. Variables héritées du gabarit : $pp_stats.
 */

if (!defined('ABSPATH')) {
    exit;
}

$pp_attente     = poolparty_g4_admin_biens_en_attente();
$pp_apercu      = array_slice($pp_attente, 0, 3);
$pp_reservations = array_slice(poolparty_g4_admin_reservations(), 0, 6);
?>

<div class="pp-admin__stats">
    <div class="pp-admin__stat<?php echo $pp_stats['biens_attente'] > 0 ? ' pp-admin__stat--alerte' : ''; ?>">
        <div class="pp-admin__stat-valeur"><?php echo esc_html($pp_stats['biens_attente']); ?></div>
        <div class="pp-admin__stat-libelle">Annonces en attente</div>
    </div>
    <div class="pp-admin__stat">
        <div class="pp-admin__stat-valeur"><?php echo esc_html($pp_stats['resa_attente']); ?></div>
        <div class="pp-admin__stat-libelle">Réservations en attente</div>
    </div>
    <div class="pp-admin__stat">
        <div class="pp-admin__stat-valeur"><?php echo esc_html($pp_stats['membres']); ?></div>
        <div class="pp-admin__stat-libelle">Membres inscrits</div>
    </div>
    <div class="pp-admin__stat<?php echo $pp_stats['avis_masques'] > 0 ? ' pp-admin__stat--alerte' : ''; ?>">
        <div class="pp-admin__stat-valeur"><?php echo esc_html($pp_stats['avis_masques']); ?></div>
        <div class="pp-admin__stat-libelle">Avis masqués</div>
    </div>
    <div class="pp-admin__stat<?php echo $pp_stats['incidents_ouverts'] > 0 ? ' pp-admin__stat--alerte' : ''; ?>">
        <div class="pp-admin__stat-valeur"><?php echo esc_html($pp_stats['incidents_ouverts']); ?></div>
        <div class="pp-admin__stat-libelle">Incidents ouverts</div>
    </div>
</div>

<section class="pp-admin__bloc">
    <div class="pp-admin__bloc-entete">
        <h2>Annonces à valider</h2>
        <a class="pp-admin__bloc-lien" href="<?php echo esc_url(poolparty_g4_admin_url('annonces')); ?>">Toutes les annonces</a>
    </div>
    <?php if (empty($pp_apercu)) : ?>
        <p class="pp-admin__vide">Aucune annonce en attente pour le moment.</p>
    <?php else : ?>
        <?php foreach ($pp_apercu as $pp_bien) {
            poolparty_g4_admin_ligne_annonce($pp_bien);
        } ?>
    <?php endif; ?>
</section>

<section class="pp-admin__bloc">
    <div class="pp-admin__bloc-entete">
        <h2>Dernières réservations</h2>
        <a class="pp-admin__bloc-lien" href="<?php echo esc_url(poolparty_g4_admin_url('reservations')); ?>">Tout le suivi</a>
    </div>
    <?php if (empty($pp_reservations)) : ?>
        <p class="pp-admin__vide">Aucune réservation enregistrée pour l'instant.</p>
    <?php else : ?>
        <div class="pp-admin__table-wrap">
            <table class="pp-admin__table">
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Date de venue</th>
                        <th>Total</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pp_reservations as $pp_resa) :
                        $pp_bien_id = (int) get_post_meta($pp_resa->ID, 'pp_bien_id', true);
                        $pp_loc     = get_userdata((int) $pp_resa->post_author);
                        $pp_creneau = get_post_meta($pp_resa->ID, 'pp_creneau', true);
                        $pp_badge   = poolparty_g4_admin_badge_resa(get_post_meta($pp_resa->ID, 'pp_statut', true));
                        ?>
                        <tr>
                            <td><?php echo esc_html($pp_bien_id ? get_the_title($pp_bien_id) : $pp_resa->post_title); ?></td>
                            <td><?php echo esc_html($pp_loc ? $pp_loc->display_name : 'Membre'); ?></td>
                            <td><?php echo esc_html(get_post_meta($pp_resa->ID, 'pp_date', true) . ($pp_creneau ? ' · ' . $pp_creneau : '')); ?></td>
                            <td><?php echo esc_html(get_post_meta($pp_resa->ID, 'pp_total', true)); ?></td>
                            <td><span class="pp-admin__etat <?php echo esc_attr($pp_badge[0]); ?>"><?php echo esc_html($pp_badge[1]); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<?php poolparty_g4_admin_modale_refus(); ?>
