<?php
/**
 * Import automatique des Articles (Posts) du journal Actualités.
 * -------------------------------------------------------------
 * Crée les catégories (taxonomie native « category ») puis un
 * Article WordPress par entrée : titre, date, extrait, contenu,
 * image (post meta pp_image, comme les biens) et catégorie.
 * Chaque article a ainsi sa propre page (single.php) et un vrai
 * permalien ; la page Actualités (page-actualites.php) les liste
 * dynamiquement.
 *
 * Ne s'exécute qu'une fois (verrou par option). Anti-doublon par
 * contrôle d'existence sur le slug. Incrémenter
 * PP_ARTICLES_SEED_VERSION pour rejouer après ajout ici.
 *
 * Même précaution que le seed des biens : sur le hook init,
 * wp_insert_post peut ne pas conserver post_content / post_excerpt ;
 * on les réécrit directement via $wpdb après création.
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PP_ARTICLES_SEED_VERSION', '1');

/**
 * Catégories du journal : slug => nom affiché.
 * La classe de badge (composant .tag) est choisie par slug dans
 * pp_article_tag_class() (functions.php).
 */
function poolparty_g4_seed_categories_articles() {
    return array(
        'nouveautes' => 'Nouveautés',
        'conseils'   => 'Conseils',
        'idees'      => 'Idées',
        'communaute' => 'Communauté',
    );
}

/**
 * Les articles à importer (contenu court de démonstration).
 * image = chemin relatif au thème (rendu via pp_asset).
 */
function poolparty_g4_seed_articles() {
    return array(
        array(
            'slug' => 'reservation-instantanee-ete-2026', 'categorie' => 'nouveautes',
            'date' => '2026-07-02 09:00:00',
            'titre' => "La réservation instantanée arrive pour l'été 2026",
            'image' => 'assets/images/evenements/pool-party.jpg',
            'alt' => "Groupe d'amis profitant d'une piscine louée par une belle journée d'été",
            'extrait' => "Fini l'attente : sur plus de la moitié des annonces, vous réservez votre créneau de baignade en quelques secondes, sans validation préalable de l'hôte.",
            'contenu' => "Jusqu'ici, chaque demande de réservation attendait le feu vert de l'hôte avant d'être confirmée. Avec la réservation instantanée, les espaces éligibles se réservent directement : vous choisissez votre créneau, vous payez, c'est confirmé.\n\nPour repérer ces annonces, un badge Réservation instantanée apparaît sur la fiche et dans les résultats de recherche. Les autres espaces restent en demande classique, avec une réponse de l'hôte sous 24 heures.\n\nCette nouveauté couvre déjà plus de la moitié du catalogue et s'étendra tout au long de l'été. De quoi organiser une baignade de dernière minute sans mauvaise surprise.",
        ),
        array(
            'slug' => 'cinq-conseils-apres-midi-baignade', 'categorie' => 'conseils',
            'date' => '2026-06-24 09:00:00',
            'titre' => "Cinq conseils pour réussir votre après-midi baignade entre amis",
            'image' => 'assets/images/evenements/detente.jpg',
            'alt' => "Personne allongée au bord d'une piscine, un livre à la main",
            'extrait' => "Bien choisir le créneau, prévoir de quoi grignoter, respecter les lieux : nos astuces pour une journée réussie du premier plongeon au dernier rangement.",
            'contenu' => "Commencez par choisir le bon créneau : en pleine journée pour les familles, en fin d'après-midi pour profiter d'une lumière plus douce. Pensez à vérifier la capacité de l'espace pour rester à l'aise.\n\nCôté logistique, prévoyez serviettes, crème solaire et de quoi grignoter. Beaucoup d'hôtes mettent à disposition un coin ombragé ou un barbecue : renseignez-vous avant de venir.\n\nEnfin, respectez les lieux comme s'ils étaient les vôtres. Un espace rendu propre, c'est un hôte content et un meilleur avis pour tout le monde.",
        ),
        array(
            'slug' => 'pool-party-seine-et-marne', 'categorie' => 'nouveautes',
            'date' => '2026-06-12 09:00:00',
            'titre' => "Pool Party s'étend en Seine-et-Marne : 40 nouveaux espaces",
            'image' => 'assets/images/piscines/annonce-lagny.jpg',
            'alt' => "Piscine de jardin récemment référencée en Seine-et-Marne",
            'extrait' => "Lagny, Torcy, Chelles : de nouveaux hôtes rejoignent la plateforme à l'est de Paris. Tour d'horizon des espaces désormais disponibles près de chez vous.",
            'contenu' => "L'est francilien monte en puissance. En quelques semaines, une quarantaine d'espaces ont rejoint Pool Party en Seine-et-Marne, des piscines de jardin aux spas privatifs.\n\nCette arrivée élargit le choix pour les habitants de Marne-la-Vallée et des environs, souvent contraints de s'éloigner pour trouver un bassin disponible. Les tarifs restent dans la moyenne de la plateforme.\n\nD'autres communes suivront dans les prochaines semaines. Si vous possédez un espace aquatique dans le secteur, c'est le bon moment pour le proposer.",
        ),
        array(
            'slug' => 'team-building-au-bord-de-l-eau', 'categorie' => 'idees',
            'date' => '2026-06-03 09:00:00',
            'titre' => "Organiser un team building au bord de l'eau",
            'image' => 'assets/images/evenements/team-building.jpg',
            'alt' => "Équipe réunie autour d'une piscine lors d'un séminaire d'entreprise",
            'extrait' => "Un cadre qui change de la salle de réunion, des activités qui rassemblent : la location d'un espace privé est une idée simple pour souder une équipe.",
            'contenu' => "Rien de tel qu'un changement de décor pour resserrer les liens. Une piscine ou un spa privatisé le temps d'une journée offre un cadre détendu, loin des open spaces.\n\nAu programme : jeux d'eau, déjeuner partagé, moments d'échange informels. L'idée n'est pas de tout organiser à la minute, mais de laisser de la place à la convivialité.\n\nPensez à réserver un espace adapté à la taille du groupe et à prévenir l'hôte du contexte. Certains proposent même tables et transats pour accueillir confortablement toute l'équipe.",
        ),
        array(
            'slug' => 'anniversaire-au-bord-de-la-piscine', 'categorie' => 'idees',
            'date' => '2026-05-21 09:00:00',
            'titre' => "Anniversaire au bord de la piscine : le guide complet",
            'image' => 'assets/images/evenements/anniversaire.jpg',
            'alt' => "Table décorée pour un anniversaire à côté d'une piscine",
            'extrait' => "Nombre d'invités, sécurité des enfants, animations et checklist du matériel : tout ce qu'il faut penser pour fêter l'événement sans stress.",
            'contenu' => "Un anniversaire au bord de l'eau, c'est la promesse d'une fête mémorable. Commencez par estimer le nombre d'invités pour choisir un espace à la bonne capacité.\n\nSi des enfants sont présents, vérifiez les points de sécurité avec l'hôte : profondeur du bassin, barrière, surveillance. Prévoyez aussi de quoi les occuper entre deux baignades.\n\nCôté matériel, une checklist simple évite les oublis : gâteau, boissons, musique, serviettes et sacs pour repartir. Le reste, c'est de la bonne humeur.",
        ),
        array(
            'slug' => 'hotes-photographier-votre-piscine', 'categorie' => 'communaute',
            'date' => '2026-05-09 09:00:00',
            'titre' => "Hôtes : comment bien photographier votre piscine",
            'image' => 'assets/images/piscines/annonce-croissy.jpg',
            'alt' => "Belle piscine de jardin bien mise en valeur par la lumière",
            'extrait' => "La photo fait la première impression. Lumière, angles, rangement : les réflexes des annonces qui se réservent le plus vite, expliqués pas à pas.",
            'contenu' => "La première photo décide souvent du clic. Privilégiez la lumière naturelle, en début de matinée ou en fin d'après-midi, quand elle est douce et flatteuse.\n\nRangez avant de photographier : un espace dégagé paraît plus grand et plus accueillant. Variez les angles pour montrer le bassin, les abords et les équipements.\n\nUne dizaine de photos nettes et bien cadrées suffisent. Inutile de retoucher à l'excès : les locataires apprécient une image fidèle à la réalité.",
        ),
        array(
            'slug' => 'aquagym-longueurs-farniente', 'categorie' => 'conseils',
            'date' => '2026-04-28 09:00:00',
            'titre' => "Aquagym, longueurs, farniente : chacun sa baignade",
            'image' => 'assets/images/evenements/sport.jpg',
            'alt' => "Personne nageant des longueurs dans une piscine privée",
            'extrait' => "Louer une piscine à l'heure, ce n'est pas que pour faire la fête. Petit tour des usages plus calmes plébiscités par la communauté.",
            'contenu' => "On imagine souvent la location d'une piscine réservée aux grandes tablées. Pourtant, une part croissante des réservations vise le calme : quelques longueurs, une séance d'aquagym, une sieste au soleil.\n\nLes créneaux en semaine, plus tranquilles, s'y prêtent particulièrement. Certains hôtes proposent des bassins chauffés parfaits pour un moment de récupération.\n\nQuel que soit votre usage, l'idée reste la même : profiter d'un espace privé, à son rythme, sans la foule des lieux publics.",
        ),
    );
}

/**
 * Exécute l'import : catégories puis articles manquants. Idempotent.
 */
function poolparty_g4_importer_articles() {
    if (get_option('pp_articles_seed_version') === PP_ARTICLES_SEED_VERSION) {
        return;
    }

    // 1. Catégories (taxonomie native)
    foreach (poolparty_g4_seed_categories_articles() as $slug => $nom) {
        if (!term_exists($slug, 'category')) {
            wp_insert_term($nom, 'category', array('slug' => $slug));
        }
    }

    // 2. Retire l'article d'exemple « Hello world! » pour ne pas
    //    polluer la grille des actualités.
    $exemple = get_page_by_path('hello-world', OBJECT, 'post');
    if ($exemple) {
        wp_trash_post($exemple->ID);
    }

    // 3. Articles
    foreach (poolparty_g4_seed_articles() as $article) {
        if (get_page_by_path($article['slug'], OBJECT, 'post')) {
            continue;
        }

        $post_id = wp_insert_post(array(
            'post_type'    => 'post',
            'post_status'  => 'publish',
            'post_title'   => $article['titre'],
            'post_name'    => $article['slug'],
            'post_content' => $article['contenu'],
            'post_excerpt' => $article['extrait'],
            'post_date'    => $article['date'],
        ));

        if (!$post_id || is_wp_error($post_id)) {
            continue;
        }

        // Image (post meta, comme les biens)
        update_post_meta($post_id, 'pp_image', $article['image']);
        update_post_meta($post_id, 'pp_alt', $article['alt']);

        // Catégorie : on résout le terme par slug et on assigne par ID
        $terme = get_term_by('slug', $article['categorie'], 'category');
        if ($terme) {
            wp_set_object_terms($post_id, intval($terme->term_id), 'category', false);
        }

        // Garantie post_content / post_excerpt (voir en-tête) : sur init,
        // wp_insert_post peut les perdre, on réécrit via $wpdb au besoin.
        $cree = get_post($post_id);
        if ($cree && (trim($cree->post_content) === '' || trim($cree->post_excerpt) === '')) {
            global $wpdb;
            $wpdb->update(
                $wpdb->posts,
                array(
                    'post_content' => $article['contenu'],
                    'post_excerpt' => $article['extrait'],
                ),
                array('ID' => $post_id)
            );
            clean_post_cache($post_id);
        }
    }

    update_option('pp_articles_seed_version', PP_ARTICLES_SEED_VERSION);
}
add_action('init', 'poolparty_g4_importer_articles', 20);
