<?php
/**
 * Espace administrateur côté site (page /administration/).
 * -------------------------------------------------------------
 * Console de l'équipe Pool Party, servie par le thème avec le design
 * du site : tableau de bord, validation des annonces déposées, suivi
 * des réservations, membres et modération des avis. Elle double le
 * back-office WordPress pour une prise en main plus simple, mais s'appuie
 * exactement sur les mêmes données et les mêmes fonctions (inc/avis.php,
 * inc/reservations.php, transitions de inc/emails.php).
 *
 * Sécurité : CHAQUE écran et CHAQUE action exige current_user_can(
 * 'manage_options'). Aucun rôle nouveau n'est créé (modèle unifié Membre
 * + Administrateur natif). Les actions passent par un POST protégé par
 * nonce, puis une redirection (schéma Post/Redirect/Get) pour éviter le
 * rejeu au rafraîchissement.
 *
 * Confidentialité : jamais d'e-mail ni de téléphone d'un membre affiché
 * (règle du projet). Les listes n'exposent que des noms d'affichage.
 */

if (!defined('ABSPATH')) {
    exit;
}

/* =============================================================
   1. SECTIONS DE LA CONSOLE
   ============================================================= */

/** Sections disponibles : clé (slug ?section=) => libellé du menu. */
function poolparty_g4_admin_sections() {
    return array(
        'tableau-de-bord' => 'Tableau de bord',
        'annonces'        => 'Annonces',
        'reservations'    => 'Réservations',
        'membres'         => 'Membres',
        'avis'            => 'Avis',
        'reglages'        => 'Réglages',
    );
}

/** Section demandée et valide (défaut : tableau de bord). */
function poolparty_g4_admin_section_courante() {
    $sections = poolparty_g4_admin_sections();
    $demandee = isset($_GET['section']) ? sanitize_key($_GET['section']) : '';
    return isset($sections[$demandee]) ? $demandee : 'tableau-de-bord';
}

/** URL d'une section de la console. */
function poolparty_g4_admin_url($section = 'tableau-de-bord') {
    $base = home_url('/administration/');
    return $section === 'tableau-de-bord' ? $base : add_query_arg('section', $section, $base);
}

/* =============================================================
   2. REQUÊTES DE DONNÉES
   ============================================================= */

/** Annonces (biens) en attente de validation. */
function poolparty_g4_admin_biens_en_attente() {
    return get_posts(array(
        'post_type'      => 'bien',
        'post_status'    => 'pending',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));
}

/** Toutes les réservations, de la plus récente à la plus ancienne. */
function poolparty_g4_admin_reservations($statut = '') {
    $args = array(
        'post_type'      => 'reservation',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    if ($statut !== '') {
        $args['meta_query'] = array(array('key' => 'pp_statut', 'value' => $statut));
    }
    return get_posts($args);
}

/** Chiffres clés du tableau de bord. */
function poolparty_g4_admin_stats() {
    $biens_attente = count(poolparty_g4_admin_biens_en_attente());

    $q_resa = new WP_Query(array(
        'post_type'      => 'reservation',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => array(array('key' => 'pp_statut', 'value' => 'en-attente')),
    ));
    $resa_attente = (int) $q_resa->found_posts;

    $membres = count_users();
    $nb_membres = 0;
    foreach ($membres['avail_roles'] as $role => $n) {
        if ($role !== 'administrator') {
            $nb_membres += (int) $n;
        }
    }

    $avis_masques = (int) get_comments(array(
        'type__in' => array('pp_avis', 'pp_avis_locataire', 'pp_avis_reponse'),
        'status'   => 'hold',
        'count'    => true,
    ));

    return array(
        'biens_attente' => $biens_attente,
        'resa_attente'  => $resa_attente,
        'membres'       => $nb_membres,
        'avis_masques'  => $avis_masques,
    );
}

/** Membres du site (hors administrateurs), pour le tableau Membres. */
function poolparty_g4_admin_membres() {
    return get_users(array(
        'orderby'      => 'registered',
        'order'        => 'DESC',
        'role__not_in' => array('administrator'),
        'number'       => 200,
    ));
}

/** Nombre d'annonces publiées d'un membre. */
function poolparty_g4_admin_nb_biens($user_id) {
    $q = new WP_Query(array(
        'post_type'      => 'bien',
        'post_status'    => array('publish', 'pending', 'draft'),
        'author'         => (int) $user_id,
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ));
    return (int) $q->found_posts;
}

/** Nombre de réservations envoyées par un membre (en tant que locataire). */
function poolparty_g4_admin_nb_resas($user_id) {
    $q = new WP_Query(array(
        'post_type'      => 'reservation',
        'post_status'    => 'publish',
        'author'         => (int) $user_id,
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ));
    return (int) $q->found_posts;
}

/**
 * Le membre a-t-il des réservations actives (en attente ou confirmées),
 * comme locataire (post_author) OU comme hôte (méta pp_hote_id) ? Sert de
 * garde-fou avant toute suppression de compte : supprimer un membre qui a
 * des réservations en cours casserait des échanges avec d'autres membres.
 */
function poolparty_g4_membre_a_resas_actives($user_id) {
    $user_id = (int) $user_id;
    if (!$user_id) {
        return false;
    }
    $actifs = array('en-attente', 'acceptee');

    // Comme locataire : demandes qu'il a envoyées.
    $locataire = new WP_Query(array(
        'post_type'      => 'reservation',
        'post_status'    => 'publish',
        'author'         => $user_id,
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => array(array(
            'key'     => 'pp_statut',
            'value'   => $actifs,
            'compare' => 'IN',
        )),
    ));
    if ($locataire->found_posts > 0) {
        return true;
    }

    // Comme hôte : demandes reçues sur ses biens (donc avec d'autres membres).
    $hote = new WP_Query(array(
        'post_type'      => 'reservation',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_query'     => array(
            'relation' => 'AND',
            array('key' => 'pp_hote_id', 'value' => $user_id),
            array('key' => 'pp_statut', 'value' => $actifs, 'compare' => 'IN'),
        ),
    ));
    return $hote->found_posts > 0;
}

/** Toutes les annonces du site (modération), filtrables par statut. */
function poolparty_g4_admin_toutes_annonces($statut = '') {
    $statuts = array('publish', 'pending', 'draft');
    if (in_array($statut, $statuts, true)) {
        $statuts = array($statut);
    }
    return get_posts(array(
        'post_type'      => 'bien',
        'post_status'    => $statuts,
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));
}

/* =============================================================
   2 bis. COMPTES BLOQUÉS ET E-MAILS BANNIS
   ============================================================= */

/** Liste noire des adresses e-mail bannies (option du site). */
function poolparty_g4_emails_bannis() {
    $liste = get_option('pp_emails_bannis', array());
    return is_array($liste) ? $liste : array();
}

/** Cette adresse est-elle bannie ? */
function poolparty_g4_email_banni($email) {
    return in_array(strtolower(trim((string) $email)), poolparty_g4_emails_bannis(), true);
}

/** Ce membre est-il bloqué ? (connexion refusée, compte conservé) */
function poolparty_g4_membre_bloque($user_id) {
    return (bool) get_user_meta((int) $user_id, 'pp_bloque', true);
}

/**
 * Bouton flottant « Administration » affiché sur tout le site quand un
 * administrateur est connecté : permet de basculer vers la console à tout
 * moment (le chemin inverse passe par « Voir le site » dans son en-tête).
 * Style embarqué : la feuille administration.css ne charge que sur la
 * console, pas sur le reste du site.
 */
function poolparty_g4_admin_switch_flottant() {
    if (!current_user_can('manage_options') || is_page('administration')) {
        return;
    }
    ?>
    <style>
        .pp-admin-switch {
            position: fixed;
            bottom: 24px;
            left: 24px;
            z-index: 1100; /* au-dessus du bandeau cookies (1000) */
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: var(--color-dark-green-blue);
            color: var(--color-white);
            font-family: var(--font-body);
            font-size: var(--fs-p3);
            border-radius: var(--radius-pill);
            box-shadow: var(--shadow-btn);
            text-decoration: none;
        }
        .pp-admin-switch:hover {
            background: var(--color-dark-green-blue-2);
            color: var(--color-white);
        }
        .pp-admin-switch::before {
            content: "";
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: var(--color-corail);
            flex-shrink: 0;
        }
    </style>
    <a class="pp-admin-switch" href="<?php echo esc_url(home_url('/administration/')); ?>">Administration</a>
    <?php
}
// Pilule flottante DÉSACTIVÉE le 29-07-2026 (Audrey préfère la bascule
// « Mode admin / Mode membre » dans le header, façon Airbnb ; voir
// header.php et header-administration.php). Conservée au cas où.
// add_action('wp_footer', 'poolparty_g4_admin_switch_flottant');

/**
 * Refuse la connexion d'un compte bloqué ou dont l'adresse est bannie.
 * Passe par le filtre d'authentification WordPress : vaut pour wp-login
 * comme pour la pop-up de connexion du site (wp_signon).
 */
function poolparty_g4_bloquer_connexion($user, $password) {
    if ($user instanceof WP_User
        && (poolparty_g4_membre_bloque($user->ID) || poolparty_g4_email_banni($user->user_email))
        && !user_can($user, 'manage_options')) {
        return new WP_Error('pp_bloque', 'Ce compte a été suspendu par l\'équipe Pool Party.');
    }
    return $user;
}
add_filter('wp_authenticate_user', 'poolparty_g4_bloquer_connexion', 10, 2);

/* =============================================================
   3. TRAITEMENT DES ACTIONS (Post / Redirect / Get)
   ============================================================= */

/**
 * Intercepte les soumissions de la console avant tout affichage. Chaque
 * action vérifie le nonce et la capacité manage_options, agit, puis
 * redirige vers la section d'origine avec un indicateur de message.
 */
function poolparty_g4_admin_traiter_action() {
    if (!isset($_POST['pp_admin_action'])) {
        return;
    }
    if (!is_page('administration') && !(isset($_POST['pp_admin_page']) && $_POST['pp_admin_page'] === 'administration')) {
        // Sécurité : on ne traite que les envois issus de la console.
        return;
    }
    if (!current_user_can('manage_options')) {
        wp_die('Action réservée à l\'administration.', 403);
    }

    $action  = sanitize_key($_POST['pp_admin_action']);
    $section = isset($_POST['section']) ? sanitize_key($_POST['section']) : 'tableau-de-bord';

    check_admin_referer('pp_admin_' . $action);

    $message = '';

    switch ($action) {

        // -- Valider une annonce en attente : pending -> publish.
        //    La transition déclenche l'e-mail « votre annonce est en ligne »
        //    déjà défini dans inc/emails.php.
        case 'valider_annonce':
            $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
            $bien    = $post_id ? get_post($post_id) : null;
            if ($bien && $bien->post_type === 'bien' && $bien->post_status === 'pending') {
                wp_update_post(array('ID' => $post_id, 'post_status' => 'publish'));
                delete_post_meta($post_id, 'pp_refus_motif');
                $message = 'annonce-validee';
            }
            break;

        // -- Refuser une annonce : pending -> draft (conservée, pas supprimée)
        //    + motif enregistré + e-mail au propriétaire avec le motif.
        case 'refuser_annonce':
            $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
            $motif   = isset($_POST['motif']) ? sanitize_textarea_field(wp_unslash($_POST['motif'])) : '';
            $bien    = $post_id ? get_post($post_id) : null;
            if ($bien && $bien->post_type === 'bien' && $bien->post_status === 'pending') {
                update_post_meta($post_id, 'pp_refus_motif', $motif);
                wp_update_post(array('ID' => $post_id, 'post_status' => 'draft'));
                poolparty_g4_email_bien_refuse($bien, $motif);
                $message = 'annonce-refusee';
            }
            break;

        // -- Modifier une réservation : date, créneau, invités, total, statut.
        //    Utile quand un membre contacte la plateforme (report, erreur...).
        case 'modifier_resa':
            $resa_id = isset($_POST['resa_id']) ? absint($_POST['resa_id']) : 0;
            $resa    = $resa_id ? get_post($resa_id) : null;
            if ($resa && $resa->post_type === 'reservation') {
                foreach (array('pp_date', 'pp_creneau', 'pp_invites', 'pp_total') as $cle) {
                    if (isset($_POST[$cle])) {
                        update_post_meta($resa_id, $cle, sanitize_text_field(wp_unslash($_POST[$cle])));
                    }
                }
                if (isset($_POST['pp_statut'])) {
                    $statut = sanitize_key(wp_unslash($_POST['pp_statut']));
                    if (array_key_exists($statut, poolparty_g4_statuts_reservation())) {
                        update_post_meta($resa_id, 'pp_statut', $statut);
                    }
                }
                $message = 'resa-modifiee';
            }
            break;

        // -- Supprimer une réservation : mise en corbeille (récupérable
        //    depuis l'écran Corbeille de la section), pas de suppression
        //    définitive à ce stade.
        case 'supprimer_resa':
            $resa_id = isset($_POST['resa_id']) ? absint($_POST['resa_id']) : 0;
            $resa    = $resa_id ? get_post($resa_id) : null;
            if ($resa && $resa->post_type === 'reservation') {
                wp_trash_post($resa_id);
                $message = 'resa-supprimee';
            }
            break;

        // -- Corbeille des réservations : restaurer, ou supprimer pour de bon.
        case 'restaurer_resa':
        case 'detruire_resa':
            $resa_id = isset($_POST['resa_id']) ? absint($_POST['resa_id']) : 0;
            $resa    = $resa_id ? get_post($resa_id) : null;
            if ($resa && $resa->post_type === 'reservation' && $resa->post_status === 'trash') {
                if ($action === 'restaurer_resa') {
                    wp_untrash_post($resa_id);
                    // wp_untrash_post remet en « draft » par défaut : une
                    // réservation vit en « publish », on la remet d'aplomb.
                    wp_update_post(array('ID' => $resa_id, 'post_status' => 'publish'));
                    $message = 'resa-restauree';
                } else {
                    wp_delete_post($resa_id, true);
                    $message = 'resa-detruite';
                }
            }
            break;

        // -- Modération d'une annonce en ligne : la retirer du site
        //    (conservée en brouillon), ou republier un brouillon.
        case 'retirer_annonce':
        case 'publier_annonce':
            $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
            $bien    = $post_id ? get_post($post_id) : null;
            if ($bien && $bien->post_type === 'bien') {
                if ($action === 'retirer_annonce' && $bien->post_status === 'publish') {
                    wp_update_post(array('ID' => $post_id, 'post_status' => 'draft'));
                    $message = 'annonce-retiree';
                } elseif ($action === 'publier_annonce' && $bien->post_status !== 'publish') {
                    wp_update_post(array('ID' => $post_id, 'post_status' => 'publish'));
                    delete_post_meta($post_id, 'pp_refus_motif');
                    $message = 'annonce-validee';
                }
            }
            break;

        // -- Modération d'un compte : bloquer (connexion refusée, compte
        //    conservé), débloquer, bannir (blocage + adresse en liste noire)
        //    ou supprimer définitivement. Jamais sur un administrateur.
        case 'bloquer_membre':
        case 'debloquer_membre':
        case 'bannir_membre':
        case 'supprimer_membre':
            $membre_id = isset($_POST['membre_id']) ? absint($_POST['membre_id']) : 0;
            $membre    = $membre_id ? get_userdata($membre_id) : null;
            if ($membre && !user_can($membre, 'manage_options')) {
                if ($action === 'debloquer_membre') {
                    delete_user_meta($membre_id, 'pp_bloque');
                    delete_user_meta($membre_id, 'pp_bloque_date');
                    $liste = array_values(array_diff(poolparty_g4_emails_bannis(), array(strtolower($membre->user_email))));
                    update_option('pp_emails_bannis', $liste);
                    $message = 'membre-debloque';
                } elseif ($action === 'supprimer_membre') {
                    if (poolparty_g4_membre_a_resas_actives($membre_id)) {
                        // Réservations en cours (avec lui ou sur ses annonces) :
                        // suppression refusée pour ne pas casser ces échanges.
                        $message = 'membre-resas-actives';
                    } else {
                        // Aucune réservation active : on réattribue ses contenus
                        // (annonces et réservations passées) à l'administrateur
                        // qui agit, au lieu de les détruire.
                        require_once ABSPATH . 'wp-admin/includes/user.php';
                        wp_delete_user($membre_id, get_current_user_id());
                        $message = 'membre-supprime';
                    }
                } else {
                    update_user_meta($membre_id, 'pp_bloque', 1);
                    update_user_meta($membre_id, 'pp_bloque_date', current_time('d/m/Y'));
                    if ($action === 'bannir_membre') {
                        $liste   = poolparty_g4_emails_bannis();
                        $liste[] = strtolower($membre->user_email);
                        update_option('pp_emails_bannis', array_values(array_unique($liste)));
                        $message = 'membre-banni';
                    } else {
                        $message = 'membre-bloque';
                    }
                }
            }
            break;

        // -- Modération d'un avis : masquer / rendre visible / corbeille.
        //    Réutilise le statut des commentaires WordPress natifs.
        case 'avis_masquer':
        case 'avis_visible':
        case 'avis_corbeille':
        case 'avis_restaurer':
        case 'avis_detruire':
            $comment_id = isset($_POST['comment_id']) ? absint($_POST['comment_id']) : 0;
            $avis       = $comment_id ? get_comment($comment_id) : null;
            $types      = array('pp_avis', 'pp_avis_locataire', 'pp_avis_reponse');
            if ($avis && in_array($avis->comment_type, $types, true)) {
                if ($action === 'avis_corbeille') {
                    wp_trash_comment($comment_id);
                    $message = 'avis-corbeille';
                } elseif ($action === 'avis_restaurer') {
                    wp_untrash_comment($comment_id);
                    $message = 'avis-restaure';
                } elseif ($action === 'avis_detruire') {
                    wp_delete_comment($comment_id, true);
                    $message = 'avis-detruit';
                } else {
                    wp_set_comment_status($comment_id, $action === 'avis_visible' ? 'approve' : 'hold');
                    $message = $action === 'avis_visible' ? 'avis-visible' : 'avis-masque';
                }
            }
            break;
    }

    $url = poolparty_g4_admin_url($section);
    if ($message !== '') {
        $url = add_query_arg('pp_msg', $message, $url);
    }
    wp_safe_redirect($url);
    exit;
}
add_action('template_redirect', 'poolparty_g4_admin_traiter_action');

/* =============================================================
   4. AIDES D'AFFICHAGE
   ============================================================= */

/**
 * Icône corbeille (« trash-can », Font Awesome 6 Free, style Regular),
 * embarquée en SVG : pas besoin de charger la bibliothèque entière.
 * S'utilise dans les boutons d'action des tableaux de la console.
 */
function poolparty_g4_admin_icone_trash() {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" aria-hidden="true" focusable="false"><path fill="currentColor" d="M170.5 51.6L151.5 80l145 0-19-28.4c-1.5-2.2-4-3.6-6.7-3.6l-93.7 0c-2.7 0-5.2 1.3-6.7 3.6zm147-26.6L354.2 80 368 80l48 0 8 0c13.3 0 24 10.7 24 24s-10.7 24-24 24l-8 0 0 304c0 44.2-35.8 80-80 80l-224 0c-44.2 0-80-35.8-80-80l0-304-8 0c-13.3 0-24-10.7-24-24S10.7 80 24 80l8 0 48 0 13.8 0 36.7-55.1C140.9 9.4 158.4 0 177.1 0l93.7 0c18.7 0 36.2 9.4 46.6 24.9zM80 128l0 304c0 17.7 14.3 32 32 32l224 0c17.7 0 32-14.3 32-32l0-304L80 128zm80 64l0 208c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-208c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0l0 208c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-208c0-8.8 7.2-16 16-16s16 7.2 16 16zm80 0l0 208c0 8.8-7.2 16-16 16s-16-7.2-16-16l0-208c0-8.8 7.2-16 16-16s16 7.2 16 16z"/></svg>';
}

/**
 * Classe de badge et libellé d'un statut de réservation, pour les tableaux
 * de la console. S'appuie sur les libellés de inc/reservations.php.
 *
 * @param string $statut Clé de statut (en-attente, acceptee, refusee...).
 * @return array array(classe_css, libelle)
 */
function poolparty_g4_admin_badge_resa($statut) {
    $labels = poolparty_g4_statuts_reservation();
    $label  = isset($labels[$statut]) ? $labels[$statut] : 'En attente de confirmation';
    $classe = 'pp-admin__etat--attente';
    if ($statut === 'acceptee') {
        $classe = 'pp-admin__etat--ok';
    } elseif ($statut === 'refusee') {
        $classe = 'pp-admin__etat--refus';
    } elseif ($statut === 'annulee' || $statut === 'annulee-hote') {
        $classe = 'pp-admin__etat--neutre';
    }
    return array($classe, $label);
}

/**
 * Affiche une ligne « annonce à valider » (photo, titre, propriétaire, prix,
 * boutons Valider / Refuser). Le bouton Refuser ouvre la pop-up commune de
 * la section (remplie en JS avec l'identifiant et le titre de l'annonce).
 *
 * @param WP_Post $bien Annonce en attente.
 */
function poolparty_g4_admin_ligne_annonce($bien) {
    $auteur  = get_userdata((int) $bien->post_author);
    $ville   = get_post_meta($bien->ID, 'pp_ville', true);
    $prix    = get_post_meta($bien->ID, 'pp_prix_heure', true);
    $date    = get_the_date('d/m/Y', $bien);
    // Photo : image à la une (annonces déposées par le tunnel), sinon la
    // photo du bien servie par le thème (jeu de biens importé).
    $image   = get_the_post_thumbnail_url($bien->ID, 'medium');
    if (!$image && function_exists('poolparty_g4_image_url')) {
        $image = poolparty_g4_image_url($bien->ID);
    }
    ?>
    <div class="pp-admin__annonce">
        <div class="pp-admin__annonce-media">
            <?php if ($image) : ?>
                <img class="pp-admin__annonce-photo" src="<?php echo esc_url($image); ?>" alt="" loading="lazy" decoding="async">
            <?php else : ?>
                <span class="pp-admin__annonce-photo" aria-hidden="true"></span>
            <?php endif; ?>
            <div>
                <div class="pp-admin__annonce-titre"><?php echo esc_html(get_the_title($bien)); ?></div>
                <div class="pp-admin__annonce-infos">
                    Proposée par <strong><?php echo esc_html($auteur ? $auteur->display_name : 'un membre'); ?></strong>, le <?php echo esc_html($date); ?>
                    <?php if ($ville) : ?> · <?php echo esc_html($ville); ?><?php endif; ?>
                    <?php if ($prix) : ?> · <?php echo esc_html($prix); ?> € de l'heure<?php endif; ?>
                </div>
            </div>
        </div>
        <div class="pp-admin__annonce-actions">
            <a class="btn btn-tertiary btn-small" href="<?php echo esc_url(get_preview_post_link($bien)); ?>" target="_blank" rel="noopener">Aperçu</a>
            <form method="post" action="">
                <input type="hidden" name="pp_admin_page" value="administration">
                <input type="hidden" name="pp_admin_action" value="valider_annonce">
                <input type="hidden" name="section" value="<?php echo esc_attr(poolparty_g4_admin_section_courante()); ?>">
                <input type="hidden" name="post_id" value="<?php echo esc_attr($bien->ID); ?>">
                <?php wp_nonce_field('pp_admin_valider_annonce'); ?>
                <button type="submit" class="btn btn-primary btn-small">Valider</button>
            </form>
            <button type="button" class="btn btn-tertiary btn-small js-admin-refuser" data-post-id="<?php echo esc_attr($bien->ID); ?>" data-titre="<?php echo esc_attr(get_the_title($bien)); ?>">Refuser</button>
        </div>
    </div>
    <?php
}

/**
 * Pop-up commune de refus d'annonce (une seule par page). Le formulaire
 * poste vers le traitement (nonce pp_admin_refuser_annonce) ; l'identifiant
 * et le titre sont injectés par le petit script de la section Annonces.
 */
function poolparty_g4_admin_modale_refus() {
    ?>
    <div class="pp-admin__modale" id="pp-admin-modale-refus" hidden>
        <div class="pp-admin__modale-carte" role="dialog" aria-modal="true" aria-labelledby="pp-admin-refus-titre">
            <button type="button" class="pp-admin__modale-croix js-admin-refus-fermer" aria-label="Fermer">✕</button>
            <h3 id="pp-admin-refus-titre">Refuser cette annonce</h3>
            <p>L'annonce <strong class="js-admin-refus-nom"></strong> sera retirée du site mais conservée. Le propriétaire recevra un e-mail avec le motif ci-dessous.</p>
            <form method="post" action="">
                <input type="hidden" name="pp_admin_page" value="administration">
                <input type="hidden" name="pp_admin_action" value="refuser_annonce">
                <input type="hidden" name="section" value="annonces">
                <input type="hidden" name="post_id" id="pp-admin-refus-id" value="">
                <?php wp_nonce_field('pp_admin_refuser_annonce'); ?>
                <label for="pp-admin-refus-motif">Motif du refus</label>
                <textarea id="pp-admin-refus-motif" name="motif" required placeholder="Expliquez au propriétaire ce qui doit être corrigé (photos, description, adresse...)."></textarea>
                <div class="pp-admin__modale-actions">
                    <button type="button" class="btn btn-tertiary btn-small js-admin-refus-fermer">Annuler</button>
                    <button type="submit" class="btn btn-secondary btn-small">Refuser et prévenir</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    (function () {
        var modale = document.getElementById('pp-admin-modale-refus');
        if (!modale) { return; }
        var champId = document.getElementById('pp-admin-refus-id');
        var champNom = modale.querySelector('.js-admin-refus-nom');
        function ouvrir(id, titre) {
            champId.value = id;
            champNom.textContent = '« ' + titre + ' »';
            modale.hidden = false;
        }
        function fermer() { modale.hidden = true; }
        document.querySelectorAll('.js-admin-refuser').forEach(function (b) {
            b.addEventListener('click', function () { ouvrir(b.dataset.postId, b.dataset.titre); });
        });
        modale.querySelectorAll('.js-admin-refus-fermer').forEach(function (b) {
            b.addEventListener('click', fermer);
        });
        modale.addEventListener('click', function (e) { if (e.target === modale) { fermer(); } });
    })();
    </script>
    <?php
}

/**
 * Pop-up commune de modification d'une réservation (une seule par page).
 * Les champs sont préremplis en JS depuis les data-* du bouton Modifier
 * de la ligne concernée (voir section-reservations.php).
 */
function poolparty_g4_admin_modale_resa() {
    $statuts = poolparty_g4_statuts_reservation();
    ?>
    <div class="pp-admin__modale" id="pp-admin-modale-resa" hidden>
        <div class="pp-admin__modale-carte" role="dialog" aria-modal="true" aria-labelledby="pp-admin-resa-titre">
            <button type="button" class="pp-admin__modale-croix js-admin-resa-fermer" aria-label="Fermer">✕</button>
            <h3 id="pp-admin-resa-titre">Modifier la réservation</h3>
            <p><strong class="js-admin-resa-nom"></strong></p>
            <form method="post" action="">
                <input type="hidden" name="pp_admin_page" value="administration">
                <input type="hidden" name="pp_admin_action" value="modifier_resa">
                <input type="hidden" name="section" value="reservations">
                <input type="hidden" name="resa_id" id="pp-admin-resa-id" value="">
                <?php wp_nonce_field('pp_admin_modifier_resa'); ?>
                <label for="pp-admin-resa-date">Date de venue (JJ/MM/AAAA)</label>
                <input type="text" id="pp-admin-resa-date" name="pp_date" class="pp-admin__modale-champ" value="">
                <label for="pp-admin-resa-creneau">Créneau</label>
                <input type="text" id="pp-admin-resa-creneau" name="pp_creneau" class="pp-admin__modale-champ" value="">
                <label for="pp-admin-resa-invites">Invités</label>
                <input type="text" id="pp-admin-resa-invites" name="pp_invites" class="pp-admin__modale-champ" value="">
                <label for="pp-admin-resa-total">Total</label>
                <input type="text" id="pp-admin-resa-total" name="pp_total" class="pp-admin__modale-champ" value="">
                <label for="pp-admin-resa-statut">Statut</label>
                <select id="pp-admin-resa-statut" name="pp_statut" class="pp-admin__modale-champ">
                    <?php foreach ($statuts as $cle => $label) : ?>
                        <option value="<?php echo esc_attr($cle); ?>"><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="pp-admin__modale-actions">
                    <button type="button" class="btn btn-tertiary btn-small js-admin-resa-fermer">Annuler</button>
                    <button type="submit" class="btn btn-primary btn-small">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
    <script>
    (function () {
        var modale = document.getElementById('pp-admin-modale-resa');
        if (!modale) { return; }
        function ouvrir(b) {
            document.getElementById('pp-admin-resa-id').value = b.dataset.resaId;
            modale.querySelector('.js-admin-resa-nom').textContent = b.dataset.nom;
            document.getElementById('pp-admin-resa-date').value = b.dataset.date || '';
            document.getElementById('pp-admin-resa-creneau').value = b.dataset.creneau || '';
            document.getElementById('pp-admin-resa-invites').value = b.dataset.invites || '';
            document.getElementById('pp-admin-resa-total').value = b.dataset.total || '';
            document.getElementById('pp-admin-resa-statut').value = b.dataset.statut || 'en-attente';
            modale.hidden = false;
        }
        function fermer() { modale.hidden = true; }
        document.querySelectorAll('.js-admin-resa-modifier').forEach(function (b) {
            b.addEventListener('click', function () { ouvrir(b); });
        });
        modale.querySelectorAll('.js-admin-resa-fermer').forEach(function (b) {
            b.addEventListener('click', fermer);
        });
        modale.addEventListener('click', function (e) { if (e.target === modale) { fermer(); } });
    })();
    </script>
    <?php
}

/**
 * Libellé lisible d'un message de confirmation (indicateur ?pp_msg=).
 * Renvoie array(texte, type) ; type « refus » colore le bandeau en corail.
 */
function poolparty_g4_admin_flash() {
    $cle = isset($_GET['pp_msg']) ? sanitize_key($_GET['pp_msg']) : '';
    $map = array(
        'annonce-validee' => array('L\'annonce a été validée et publiée. Le propriétaire en est informé par e-mail.', 'ok'),
        'annonce-refusee' => array('L\'annonce a été refusée. Elle est retirée du site mais conservée, et le propriétaire a reçu le motif par e-mail.', 'refus'),
        'avis-masque'     => array('L\'avis a été masqué du site.', 'ok'),
        'avis-visible'    => array('L\'avis est de nouveau visible sur le site.', 'ok'),
        'avis-corbeille'  => array('L\'avis a été déplacé dans la corbeille.', 'refus'),
        'resa-modifiee'   => array('La réservation a été mise à jour.', 'ok'),
        'resa-supprimee'  => array('La réservation a été déplacée dans la corbeille. Vous pouvez la restaurer depuis l\'écran Corbeille.', 'refus'),
        'annonce-retiree' => array('L\'annonce a été retirée du site. Elle est conservée et peut être republiée à tout moment.', 'refus'),
        'resa-restauree'  => array('La réservation a été restaurée.', 'ok'),
        'resa-detruite'   => array('La réservation a été supprimée définitivement.', 'refus'),
        'avis-restaure'   => array('L\'avis a été restauré (masqué, à rendre visible si besoin).', 'ok'),
        'avis-detruit'    => array('L\'avis a été supprimé définitivement.', 'refus'),
        'membre-bloque'   => array('Le compte a été bloqué : ce membre ne peut plus se connecter. Son compte et ses contenus sont conservés.', 'refus'),
        'membre-debloque' => array('Le compte a été débloqué : ce membre peut de nouveau se connecter.', 'ok'),
        'membre-banni'    => array('Le compte a été banni : connexion refusée et adresse e-mail en liste noire (aucune réinscription possible).', 'refus'),
        'membre-supprime' => array('Le compte a été supprimé. Ses annonces et réservations passées ont été conservées et réattribuées à l\'administration.', 'refus'),
        'membre-resas-actives' => array('Suppression impossible : ce membre a des réservations en cours (en attente ou confirmées), avec lui ou sur ses annonces. Bloquez plutôt son compte, ou attendez la fin de ces réservations pour supprimer.', 'refus'),
    );
    return isset($map[$cle]) ? $map[$cle] : null;
}
