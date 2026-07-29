<?php
/**
 * Confort du back-office WordPress pour l'équipe.
 * -------------------------------------------------------------
 * Trois ajouts, visibles uniquement dans l'administration :
 *  - fiche réservation : méta-box « Détails de la réservation » pour corriger
 *    la date, le créneau, le statut... directement depuis l'admin ;
 *  - liste des réservations : colonnes bien / locataire / date de venue /
 *    statut / total, et filtre par statut ;
 *  - écran « Avis » (sous Commentaires) : les avis du site avec leur note,
 *    le bien concerné, tri, filtres et actions de modération.
 */

if (!defined('ABSPATH')) {
    exit;
}

/* =============================================================
   1. FICHE RÉSERVATION : MÉTA-BOX DES DÉTAILS
   ============================================================= */

function poolparty_g4_resa_metabox() {
    add_meta_box(
        'pp-resa-details',
        'Détails de la réservation',
        'poolparty_g4_resa_metabox_html',
        'reservation',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'poolparty_g4_resa_metabox');

function poolparty_g4_resa_metabox_html($post) {
    wp_nonce_field('pp_resa_details', 'pp_resa_details_nonce');

    $bien_id = (int) get_post_meta($post->ID, 'pp_bien_id', true);
    $statut  = get_post_meta($post->ID, 'pp_statut', true);
    $statuts = poolparty_g4_statuts_reservation();
    $champs  = array(
        'pp_date'    => array('Date de venue (JJ/MM/AAAA)', get_post_meta($post->ID, 'pp_date', true)),
        'pp_creneau' => array('Créneau', get_post_meta($post->ID, 'pp_creneau', true)),
        'pp_invites' => array('Invités', get_post_meta($post->ID, 'pp_invites', true)),
        'pp_formule' => array('Formule', get_post_meta($post->ID, 'pp_formule', true)),
        'pp_total'   => array('Total', get_post_meta($post->ID, 'pp_total', true)),
    );
    $message = get_post_meta($post->ID, 'pp_message', true);
    ?>
    <table class="form-table">
        <?php if ($bien_id) : ?>
            <tr>
                <th scope="row">Bien réservé</th>
                <td><a href="<?php echo esc_url(get_edit_post_link($bien_id)); ?>"><?php echo esc_html(get_the_title($bien_id)); ?></a></td>
            </tr>
        <?php endif; ?>
        <tr>
            <th scope="row"><label for="pp_statut">Statut</label></th>
            <td>
                <select name="pp_statut" id="pp_statut">
                    <?php foreach ($statuts as $cle => $label) : ?>
                        <option value="<?php echo esc_attr($cle); ?>" <?php selected($statut, $cle); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <?php foreach ($champs as $cle => $infos) : ?>
            <tr>
                <th scope="row"><label for="<?php echo esc_attr($cle); ?>"><?php echo esc_html($infos[0]); ?></label></th>
                <td><input type="text" class="regular-text" name="<?php echo esc_attr($cle); ?>" id="<?php echo esc_attr($cle); ?>" value="<?php echo esc_attr($infos[1]); ?>"></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($message) : ?>
            <tr>
                <th scope="row">Message du locataire</th>
                <td><em><?php echo esc_html($message); ?></em></td>
            </tr>
        <?php endif; ?>
    </table>
    <?php
}

function poolparty_g4_resa_metabox_save($post_id) {
    if (!isset($_POST['pp_resa_details_nonce']) || !wp_verify_nonce(sanitize_key($_POST['pp_resa_details_nonce']), 'pp_resa_details')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['pp_statut'])) {
        $statut = sanitize_key(wp_unslash($_POST['pp_statut']));
        if (array_key_exists($statut, poolparty_g4_statuts_reservation())) {
            update_post_meta($post_id, 'pp_statut', $statut);
        }
    }
    foreach (array('pp_date', 'pp_creneau', 'pp_invites', 'pp_formule', 'pp_total') as $cle) {
        if (isset($_POST[$cle])) {
            update_post_meta($post_id, $cle, sanitize_text_field(wp_unslash($_POST[$cle])));
        }
    }
}
add_action('save_post_reservation', 'poolparty_g4_resa_metabox_save');

/* =============================================================
   2. LISTE DES RÉSERVATIONS : COLONNES ET FILTRE PAR STATUT
   ============================================================= */

function poolparty_g4_resa_colonnes($colonnes) {
    return array(
        'cb'        => isset($colonnes['cb']) ? $colonnes['cb'] : '<input type="checkbox">',
        'title'     => 'Réservation',
        'pp_bien'   => 'Bien',
        'author'    => 'Locataire',
        'pp_date'   => 'Date de venue',
        'pp_statut' => 'Statut',
        'pp_total'  => 'Total',
    );
}
add_filter('manage_reservation_posts_columns', 'poolparty_g4_resa_colonnes');

function poolparty_g4_resa_contenu_colonne($colonne, $post_id) {
    switch ($colonne) {
        case 'pp_bien':
            $bien_id = (int) get_post_meta($post_id, 'pp_bien_id', true);
            if ($bien_id) {
                echo '<a href="' . esc_url(get_edit_post_link($bien_id)) . '">' . esc_html(get_the_title($bien_id)) . '</a>';
            }
            break;
        case 'pp_date':
            $creneau = get_post_meta($post_id, 'pp_creneau', true);
            echo esc_html(get_post_meta($post_id, 'pp_date', true) . ($creneau ? ' · ' . $creneau : ''));
            break;
        case 'pp_statut':
            $statuts = poolparty_g4_statuts_reservation();
            $statut  = get_post_meta($post_id, 'pp_statut', true);
            echo esc_html(isset($statuts[$statut]) ? $statuts[$statut] : $statut);
            break;
        case 'pp_total':
            echo esc_html(get_post_meta($post_id, 'pp_total', true));
            break;
    }
}
add_action('manage_reservation_posts_custom_column', 'poolparty_g4_resa_contenu_colonne', 10, 2);

function poolparty_g4_resa_filtre_statut($post_type) {
    if ($post_type !== 'reservation') {
        return;
    }
    $actuel = isset($_GET['pp_statut']) ? sanitize_key($_GET['pp_statut']) : '';
    echo '<select name="pp_statut">';
    echo '<option value="">Tous les statuts</option>';
    foreach (poolparty_g4_statuts_reservation() as $cle => $label) {
        printf('<option value="%s"%s>%s</option>', esc_attr($cle), selected($actuel, $cle, false), esc_html($label));
    }
    echo '</select>';
}
add_action('restrict_manage_posts', 'poolparty_g4_resa_filtre_statut');

function poolparty_g4_resa_appliquer_filtre($query) {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    if ($query->get('post_type') !== 'reservation' || empty($_GET['pp_statut'])) {
        return;
    }
    $query->set('meta_key', 'pp_statut');
    $query->set('meta_value', sanitize_key($_GET['pp_statut']));
}
add_action('pre_get_posts', 'poolparty_g4_resa_appliquer_filtre');

/* =============================================================
   3. ÉCRAN « AVIS » (SOUS COMMENTAIRES)
   ============================================================= */

function poolparty_g4_menu_avis() {
    add_submenu_page(
        'edit-comments.php',
        'Avis',
        'Avis',
        'moderate_comments',
        'pp-avis',
        'poolparty_g4_ecran_avis'
    );
}
add_action('admin_menu', 'poolparty_g4_menu_avis');

function poolparty_g4_types_avis() {
    return array(
        'pp_avis'           => 'Avis sur un espace',
        'pp_avis_locataire' => 'Avis sur un locataire',
        'pp_avis_reponse'   => 'Réponse à un avis',
    );
}

function poolparty_g4_ecran_avis() {
    $types  = poolparty_g4_types_avis();
    $type   = (isset($_GET['type']) && isset($types[$_GET['type']])) ? sanitize_key($_GET['type']) : '';
    $statut = isset($_GET['statut']) ? sanitize_key($_GET['statut']) : '';
    $note   = isset($_GET['note']) ? absint($_GET['note']) : 0;
    $tri    = (isset($_GET['tri']) && $_GET['tri'] === 'note') ? 'note' : 'date';
    $ordre  = (isset($_GET['ordre']) && $_GET['ordre'] === 'asc') ? 'ASC' : 'DESC';

    $args = array(
        'type__in' => $type ? array($type) : array_keys($types),
        'status'   => 'all',
        'orderby'  => 'comment_date',
        'order'    => $ordre,
    );
    if ($statut === 'approuve') {
        $args['status'] = 'approve';
    } elseif ($statut === 'attente') {
        $args['status'] = 'hold';
    }
    if ($note) {
        $args['meta_key']   = 'pp_note';
        $args['meta_value'] = $note;
    }
    if ($tri === 'note') {
        $args['meta_key'] = 'pp_note';
        $args['orderby']  = 'meta_value_num';
    }
    // Pagination : le jeu de départ du catalogue représente plusieurs
    // centaines d'avis.
    $par_page      = 40;
    $page_en_cours = isset($_GET['pagina']) ? max(1, absint($_GET['pagina'])) : 1;
    $total         = (int) get_comments(array_merge($args, array('count' => true)));
    $nb_pages      = max(1, (int) ceil($total / $par_page));
    $args['number'] = $par_page;
    $args['offset'] = ($page_en_cours - 1) * $par_page;
    $liste = get_comments($args);
    ?>
    <div class="wrap">
        <h1>Avis</h1>
        <p>Tous les avis déposés sur le site : avis des locataires sur les espaces, avis des hôtes sur leurs locataires, et réponses publiques. « Masquer » retire l'avis du site sans le supprimer.</p>

        <form method="get" action="<?php echo esc_url(admin_url('edit-comments.php')); ?>" style="margin: 12px 0;">
            <input type="hidden" name="page" value="pp-avis">
            <select name="type">
                <option value="">Tous les types</option>
                <?php foreach ($types as $cle => $label) : ?>
                    <option value="<?php echo esc_attr($cle); ?>" <?php selected($type, $cle); ?>><?php echo esc_html($label); ?></option>
                <?php endforeach; ?>
            </select>
            <select name="statut">
                <option value="">Tous les statuts</option>
                <option value="approuve" <?php selected($statut, 'approuve'); ?>>Visible sur le site</option>
                <option value="attente" <?php selected($statut, 'attente'); ?>>Masqué</option>
            </select>
            <select name="note">
                <option value="">Toutes les notes</option>
                <?php for ($i = 5; $i >= 1; $i--) : ?>
                    <option value="<?php echo esc_attr($i); ?>" <?php selected($note, $i); ?>><?php echo esc_html($i); ?> étoile<?php echo $i > 1 ? 's' : ''; ?></option>
                <?php endfor; ?>
            </select>
            <select name="tri">
                <option value="date" <?php selected($tri, 'date'); ?>>Trier par date</option>
                <option value="note" <?php selected($tri, 'note'); ?>>Trier par note</option>
            </select>
            <select name="ordre">
                <option value="desc" <?php selected($ordre, 'DESC'); ?>>Décroissant</option>
                <option value="asc" <?php selected($ordre, 'ASC'); ?>>Croissant</option>
            </select>
            <button type="submit" class="button">Filtrer</button>
        </form>

        <p><strong><?php echo $total; ?></strong> avis au total, page <?php echo $page_en_cours; ?> sur <?php echo $nb_pages; ?>.</p>

        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th style="width:13%;">Type</th>
                    <th style="width:11%;">Auteur</th>
                    <th style="width:8%;">Note</th>
                    <th style="width:30%;">Contenu</th>
                    <th style="width:15%;">Concerne</th>
                    <th style="width:8%;">Date</th>
                    <th style="width:7%;">Statut</th>
                    <th style="width:12%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$liste) : ?>
                    <tr><td colspan="8">Aucun avis ne correspond à ces critères.</td></tr>
                <?php endif; ?>
                <?php foreach ($liste as $a) :
                    $note_a  = (int) get_comment_meta($a->comment_ID, 'pp_note', true);
                    $visible = wp_get_comment_status($a) === 'approved';

                    // Cible : le bien pour un avis d'espace ou une réponse,
                    // la réservation pour un avis sur un locataire.
                    if ($a->comment_type === 'pp_avis_locataire') {
                        $cible_txt  = get_the_title((int) $a->comment_post_ID);
                        $cible_lien = get_edit_post_link((int) $a->comment_post_ID);
                    } else {
                        $cible_txt  = get_the_title((int) $a->comment_post_ID);
                        $cible_lien = get_permalink((int) $a->comment_post_ID);
                    }

                    $lien_bascule = $visible
                        ? wp_nonce_url(admin_url('comment.php?action=unapprovecomment&c=' . $a->comment_ID), 'approve-comment_' . $a->comment_ID)
                        : wp_nonce_url(admin_url('comment.php?action=approvecomment&c=' . $a->comment_ID), 'approve-comment_' . $a->comment_ID);
                    $lien_corbeille = wp_nonce_url(admin_url('comment.php?action=trashcomment&c=' . $a->comment_ID), 'delete-comment_' . $a->comment_ID);
                    ?>
                    <tr>
                        <td><?php echo esc_html($types[$a->comment_type]); ?></td>
                        <td><?php echo esc_html($a->comment_author ?: 'Membre'); ?></td>
                        <td><?php echo $note_a ? esc_html(str_repeat('★', $note_a) . str_repeat('☆', 5 - $note_a)) : '·'; ?></td>
                        <td><?php echo esc_html(wp_trim_words($a->comment_content, 18, '...')); ?></td>
                        <td><?php if ($cible_lien) : ?><a href="<?php echo esc_url($cible_lien); ?>"><?php echo esc_html($cible_txt); ?></a><?php else : echo esc_html($cible_txt); endif; ?></td>
                        <td><?php echo esc_html(mysql2date('d/m/Y', $a->comment_date)); ?></td>
                        <td><?php echo $visible ? 'Visible' : 'Masqué'; ?></td>
                        <td>
                            <a href="<?php echo esc_url($lien_bascule); ?>"><?php echo $visible ? 'Masquer' : 'Rendre visible'; ?></a>
                            | <a href="<?php echo esc_url($lien_corbeille); ?>" style="color:#b32d2e;">Corbeille</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($nb_pages > 1) : ?>
            <p style="margin-top:12px;">
                <?php for ($p = 1; $p <= $nb_pages; $p++) :
                    $url = add_query_arg(array(
                        'page'   => 'pp-avis',
                        'type'   => $type,
                        'statut' => $statut,
                        'note'   => $note ?: '',
                        'tri'    => $tri,
                        'ordre'  => strtolower($ordre),
                        'pagina' => $p,
                    ), admin_url('edit-comments.php'));
                    ?>
                    <?php if ($p === $page_en_cours) : ?>
                        <strong style="padding:0 6px;"><?php echo $p; ?></strong>
                    <?php else : ?>
                        <a style="padding:0 6px;" href="<?php echo esc_url($url); ?>"><?php echo $p; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </p>
        <?php endif; ?>
    </div>
    <?php
}
