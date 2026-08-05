<?php
/**
 * Console admin : section « Incidents ».
 * Liste des signalements déposés par les membres, filtrable par statut,
 * avec la pop-up de traitement (description, photos, note interne,
 * changement de statut, message de clôture). Noms d'affichage
 * uniquement, jamais de coordonnées (règle du projet).
 */

if (!defined('ABSPATH')) {
    exit;
}

$pp_statuts_incident = poolparty_g4_incident_statuts();
$pp_motifs_incident  = poolparty_g4_incident_motifs();
$pp_filtre           = isset($_GET['statut']) ? sanitize_key($_GET['statut']) : '';
if (!array_key_exists($pp_filtre, $pp_statuts_incident)) {
    $pp_filtre = '';
}
$pp_incidents = poolparty_g4_incidents_liste($pp_filtre);
$pp_base      = poolparty_g4_admin_url('incidents');
?>

<section class="pp-admin__bloc">
    <div class="pp-admin__bloc-entete">
        <h2>Signalements</h2>
        <span>
            <a class="pp-admin__bloc-lien" href="<?php echo esc_url($pp_base); ?>"<?php echo $pp_filtre === '' ? ' aria-current="true"' : ''; ?>>Tous</a>
            <?php foreach ($pp_statuts_incident as $pp_cle => $pp_label) : ?>
                · <a class="pp-admin__bloc-lien" href="<?php echo esc_url(add_query_arg('statut', $pp_cle, $pp_base)); ?>"<?php echo $pp_filtre === $pp_cle ? ' aria-current="true"' : ''; ?>><?php echo esc_html($pp_label); ?></a>
            <?php endforeach; ?>
        </span>
    </div>

    <?php if (empty($pp_incidents)) : ?>
        <p class="pp-admin__vide">Aucun signalement<?php echo $pp_filtre !== '' ? ' avec ce statut' : ' pour le moment'; ?>.</p>
    <?php else : ?>
        <div class="pp-admin__table-wrap">
            <table class="pp-admin__table">
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th>Venue du</th>
                        <th>Déclarant</th>
                        <th>Membre concerné</th>
                        <th>Motif</th>
                        <th>Déposé le</th>
                        <th>Statut</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pp_incidents as $pp_incident) :
                        $pp_bien_id   = (int) get_post_meta($pp_incident->ID, 'pp_bien_id', true);
                        $pp_resa_id   = (int) get_post_meta($pp_incident->ID, 'pp_resa_id', true);
                        $pp_declarant = get_userdata((int) get_post_meta($pp_incident->ID, 'pp_declarant_id', true));
                        $pp_autre     = get_userdata((int) get_post_meta($pp_incident->ID, 'pp_autre_id', true));
                        $pp_motif     = get_post_meta($pp_incident->ID, 'pp_motif', true);
                        $pp_statut    = get_post_meta($pp_incident->ID, 'pp_statut', true);
                        $pp_badge     = poolparty_g4_admin_badge_incident($pp_statut);
                        $pp_photos    = array();
                        foreach (array_filter(array_map('absint', explode(',', (string) get_post_meta($pp_incident->ID, 'pp_photos', true)))) as $pp_photo_id) {
                            $pp_url = wp_get_attachment_image_url($pp_photo_id, 'medium');
                            if ($pp_url) {
                                $pp_photos[] = $pp_url;
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo esc_html($pp_bien_id ? get_the_title($pp_bien_id) : $pp_incident->post_title); ?></td>
                            <td><?php echo esc_html(get_post_meta($pp_resa_id, 'pp_date', true)); ?></td>
                            <td><?php echo esc_html($pp_declarant ? $pp_declarant->display_name : 'Membre'); ?></td>
                            <td><?php echo esc_html($pp_autre ? $pp_autre->display_name : 'Membre'); ?></td>
                            <td><?php echo esc_html(isset($pp_motifs_incident[$pp_motif]) ? $pp_motifs_incident[$pp_motif] : $pp_motif); ?></td>
                            <td><?php echo esc_html(get_the_date('d/m/Y', $pp_incident)); ?></td>
                            <td><span class="pp-admin__etat <?php echo esc_attr($pp_badge[0]); ?>"><?php echo esc_html($pp_badge[1]); ?></span></td>
                            <td>
                                <button type="button" class="btn btn-tertiary btn-small js-admin-incident"
                                    data-incident-id="<?php echo esc_attr($pp_incident->ID); ?>"
                                    data-nom="<?php echo esc_attr(($pp_bien_id ? get_the_title($pp_bien_id) : $pp_incident->post_title) . ', venue du ' . get_post_meta($pp_resa_id, 'pp_date', true)); ?>"
                                    data-contexte="<?php echo esc_attr('Déposé par ' . ($pp_declarant ? $pp_declarant->display_name : 'un membre') . ' le ' . get_the_date('d/m/Y', $pp_incident) . ', membre concerné : ' . ($pp_autre ? $pp_autre->display_name : 'un membre') . '.'); ?>"
                                    data-motif="<?php echo esc_attr(isset($pp_motifs_incident[$pp_motif]) ? $pp_motifs_incident[$pp_motif] : $pp_motif); ?>"
                                    data-description="<?php echo esc_attr($pp_incident->post_content); ?>"
                                    data-photos="<?php echo esc_attr(implode('|', $pp_photos)); ?>"
                                    data-note="<?php echo esc_attr(get_post_meta($pp_incident->ID, 'pp_note_interne', true)); ?>"
                                    data-statut="<?php echo esc_attr($pp_statut); ?>">Traiter</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<!-- Pop-up de traitement (une seule par page, remplie en JS) -->
<div class="pp-admin__modale" id="pp-admin-modale-incident" hidden>
    <div class="pp-admin__modale-carte" role="dialog" aria-modal="true" aria-labelledby="pp-admin-incident-titre">
        <button type="button" class="pp-admin__modale-croix js-admin-incident-fermer" aria-label="Fermer">✕</button>
        <h3 id="pp-admin-incident-titre">Traiter le signalement</h3>
        <p><strong class="js-admin-incident-nom"></strong></p>
        <p class="js-admin-incident-contexte"></p>
        <p><strong>Motif :</strong> <span class="js-admin-incident-motif"></span></p>
        <p class="js-admin-incident-description"></p>
        <div class="pp-admin__incident-photos js-admin-incident-photos"></div>
        <form method="post" action="">
            <input type="hidden" name="pp_admin_page" value="administration">
            <input type="hidden" name="pp_admin_action" value="incident_maj">
            <input type="hidden" name="section" value="incidents">
            <input type="hidden" name="incident_id" id="pp-admin-incident-id" value="">
            <?php wp_nonce_field('pp_admin_incident_maj'); ?>
            <label for="pp-admin-incident-note">Note interne (visible par l'équipe uniquement)</label>
            <textarea id="pp-admin-incident-note" name="note_interne" class="pp-admin__modale-champ"></textarea>
            <label for="pp-admin-incident-statut">Statut</label>
            <select id="pp-admin-incident-statut" name="pp_statut" class="pp-admin__modale-champ">
                <?php foreach ($pp_statuts_incident as $pp_cle => $pp_label) : ?>
                    <option value="<?php echo esc_attr($pp_cle); ?>"><?php echo esc_html($pp_label); ?></option>
                <?php endforeach; ?>
            </select>
            <div id="pp-admin-incident-cloture" hidden>
                <label for="pp-admin-incident-message">Message de clôture envoyé aux deux membres (facultatif)</label>
                <textarea id="pp-admin-incident-message" name="message_cloture" class="pp-admin__modale-champ" placeholder="Ce message accompagne l'e-mail de clôture."></textarea>
            </div>
            <div class="pp-admin__modale-actions">
                <button type="button" class="btn btn-tertiary btn-small js-admin-incident-fermer">Annuler</button>
                <button type="submit" class="btn btn-primary btn-small">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<script>
(function () {
    var modale = document.getElementById('pp-admin-modale-incident');
    if (!modale) { return; }
    var statut = document.getElementById('pp-admin-incident-statut');
    var cloture = document.getElementById('pp-admin-incident-cloture');
    function majCloture() { cloture.hidden = statut.value !== 'clos'; }
    function ouvrir(b) {
        document.getElementById('pp-admin-incident-id').value = b.dataset.incidentId;
        modale.querySelector('.js-admin-incident-nom').textContent = b.dataset.nom;
        modale.querySelector('.js-admin-incident-contexte').textContent = b.dataset.contexte;
        modale.querySelector('.js-admin-incident-motif').textContent = b.dataset.motif;
        modale.querySelector('.js-admin-incident-description').textContent = '« ' + b.dataset.description + ' »';
        var photos = modale.querySelector('.js-admin-incident-photos');
        photos.innerHTML = '';
        (b.dataset.photos || '').split('|').forEach(function (url) {
            if (!url) { return; }
            var lien = document.createElement('a');
            lien.href = url;
            lien.target = '_blank';
            lien.rel = 'noopener';
            var img = document.createElement('img');
            img.src = url;
            img.alt = 'Photo jointe au signalement';
            lien.appendChild(img);
            photos.appendChild(lien);
        });
        document.getElementById('pp-admin-incident-note').value = b.dataset.note || '';
        statut.value = b.dataset.statut || 'ouvert';
        document.getElementById('pp-admin-incident-message').value = '';
        majCloture();
        modale.hidden = false;
    }
    function fermer() { modale.hidden = true; }
    document.querySelectorAll('.js-admin-incident').forEach(function (b) {
        b.addEventListener('click', function () { ouvrir(b); });
    });
    modale.querySelectorAll('.js-admin-incident-fermer').forEach(function (b) {
        b.addEventListener('click', fermer);
    });
    modale.addEventListener('click', function (e) { if (e.target === modale) { fermer(); } });
    statut.addEventListener('change', majCloture);
})();
</script>
