<?php
/**
 * Fiche d'un bien (gabarit produit). Reprend la mise en page de
 * produit.html ; les données de l'en-tête et du panneau de réservation
 * viennent du CMS (CPT Bien). Le reste des sections (équipements,
 * règlement...) est un contenu de démonstration commun.
 */

get_header();

if (have_posts()) {
    the_post();
}

$pp_id      = get_the_ID();
$pp_titre   = get_the_title();
$pp_cat     = poolparty_g4_categorie_nom($pp_id);
$pp_ville   = poolparty_g4_meta($pp_id, 'ville');
$pp_prix    = poolparty_g4_meta($pp_id, 'prix_heure');
$pp_note    = poolparty_g4_meta($pp_id, 'note');
$pp_note_fr = str_replace('.', ',', $pp_note);
$pp_nb_avis = poolparty_g4_meta($pp_id, 'nb_avis');
$pp_nb_avis_int = (int) $pp_nb_avis;
$pp_cap     = poolparty_g4_capacite(poolparty_g4_meta($pp_id, 'capacite_min'), poolparty_g4_meta($pp_id, 'capacite_max'));
$pp_favori  = poolparty_g4_meta($pp_id, 'favori_id');
$pp_hote    = poolparty_g4_get_hote(poolparty_g4_meta($pp_id, 'id_hote'));

// Répartition des notes (5 à 1 étoile) cohérente avec la note et le
// nombre d'avis du bien, et liste d'autres biens pour le bloc « Espaces
// similaires » (remplace les cartes en dur pointant vers le catalogue).
$pp_rep        = poolparty_g4_repartition_avis($pp_note, $pp_nb_avis);
$pp_similaires = poolparty_g4_biens_similaires($pp_id, 5);

// Échantillon de commentaires (contenu de démonstration). On n'en affiche
// que « nb_avis » pour que la longueur de la liste colle au compteur du
// CMS, et le marqueur %HOTE% est remplacé par le prénom réel de l'hôte du
// bien (avant, tous les textes citaient « Julien »). Les quatre premiers
// sont visibles, le reste est dévoilé par le bouton « Afficher les avis ».
$pp_avis_demo = array(
    array('initiale' => 'M', 'nom' => 'Marie L.',   'depuis' => '9 mois', 'note' => 4, 'date' => 'Mai 2025',     'texte' => "Superbe après-midi passée dans la piscine de %HOTE%. Le cadre est magnifique, très calme et sans aucun vis-à-vis. L'eau était à une température parfaite. %HOTE% a été très accueillant tout en restant discret. Nous reviendrons !"),
    array('initiale' => 'T', 'nom' => 'Thomas R.',  'depuis' => '1 mois', 'note' => 4, 'date' => 'Juillet 2026', 'texte' => "Tout était nickel : portillon séparé donc aucune gêne, transats confortables, douche extérieure très pratique. On a passé la journée à six adultes, l'espace était largement suffisant. Mention spéciale pour le barbecue, top pour le déjeuner."),
    array('initiale' => 'S', 'nom' => 'Sophie B.',  'depuis' => '6 mois', 'note' => 3, 'date' => 'Juin 2026',    'texte' => "Très belle piscine, bien entretenue, et hôtes adorables. Le seul petit bémol c'est le bruit de la rue qu'on entend par moments, mais ça reste mineur. On a quand même passé un super moment, les enfants ont adoré."),
    array('initiale' => 'Y', 'nom' => 'Yoann D.',   'depuis' => '2 ans',  'note' => 4, 'date' => 'Juin 2026',    'texte' => "On voulait fêter notre anniversaire de mariage dans un endroit calme à dix minutes de Paris. C'était exactement ça. Bassin propre, jardin bien planté, accueil parfait. %HOTE% nous a même recommandé un restaurant à côté, on a adoré."),
    array('initiale' => 'C', 'nom' => 'Camille P.', 'depuis' => '4 mois', 'note' => 5, 'date' => 'Août 2026',    'texte' => "Journée parfaite entre amis, la piscine est spacieuse et l'eau limpide. %HOTE% répond vite aux messages et donne toutes les infos avant l'arrivée. Rien à redire."),
    array('initiale' => 'N', 'nom' => 'Nicolas F.', 'depuis' => '1 an',   'note' => 5, 'date' => 'Août 2026',    'texte' => "Endroit idéal pour couper de l'agitation parisienne sans partir loin. Le jardin est bien entretenu et très fleuri, on se croirait en vacances."),
    array('initiale' => 'A', 'nom' => 'Aurélie M.', 'depuis' => '7 mois', 'note' => 4, 'date' => 'Juillet 2026', 'texte' => "Super moment en famille, les enfants ne voulaient plus sortir de l'eau. Un peu de mal à se garer le samedi, mais l'accueil compense largement."),
    array('initiale' => 'K', 'nom' => 'Karim B.',   'depuis' => '2 ans',  'note' => 5, 'date' => 'Juillet 2026', 'texte' => "Le coin est très propre et le portillon indépendant assure une vraie intimité. Douche extérieure appréciable après la baignade. Je recommande sans hésiter."),
    array('initiale' => 'L', 'nom' => 'Léa V.',     'depuis' => '5 mois', 'note' => 5, 'date' => 'Juillet 2026', 'texte' => "On a réservé pour un anniversaire, tout était impeccable. Transats confortables, coin ombragé bienvenu en pleine chaleur. Merci %HOTE% pour ta disponibilité."),
    array('initiale' => 'A', 'nom' => 'Antoine G.', 'depuis' => '3 mois', 'note' => 4, 'date' => 'Juin 2026',    'texte' => "Belle piscine chauffée, cadre agréable et calme. Le seul bémol reste l'accès un peu étroit, mais une fois sur place on oublie tout."),
    array('initiale' => 'F', 'nom' => 'Fatou D.',   'depuis' => '8 mois', 'note' => 5, 'date' => 'Juin 2026',    'texte' => "Cadre reposant, on a passé un après-midi au calme à lire au bord de l'eau. %HOTE% nous a laissé profiter en toute tranquillité."),
    array('initiale' => 'M', 'nom' => 'Mathieu L.', 'depuis' => '1 an',   'note' => 5, 'date' => 'Juin 2026',    'texte' => "Parfait pour un déjeuner entre collègues, le barbecue est un vrai plus. Espace suffisant à sept adultes, on reviendra pour la rentrée."),
    array('initiale' => 'C', 'nom' => 'Chloé R.',   'depuis' => '2 mois', 'note' => 3, 'date' => 'Mai 2026',     'texte' => "Piscine agréable et hôte sympathique. On entend un peu la circulation aux heures de pointe, mais rien de rédhibitoire pour une baignade."),
    array('initiale' => 'S', 'nom' => 'Sarah K.',   'depuis' => '6 mois', 'note' => 5, 'date' => 'Mai 2026',     'texte' => "Un havre de paix à deux pas de Paris. Le jardin est superbe et très bien tenu. Nous avons adoré, l'adresse est notée pour l'été prochain."),
    array('initiale' => 'R', 'nom' => 'Romain C.',  'depuis' => '10 mois','note' => 4, 'date' => 'Avril 2026',   'texte' => "Très bon accueil et piscine nickel. Un peu frais en avril malgré le chauffage, mais l'endroit est vraiment charmant et bien pensé."),
    array('initiale' => 'I', 'nom' => 'Inès H.',    'depuis' => '3 mois', 'note' => 5, 'date' => 'Avril 2026',   'texte' => "Escapade parfaite pour se détendre. Le cadre verdoyant fait tout de suite baisser la pression. Communication au top avant et pendant le séjour."),
    array('initiale' => 'P', 'nom' => 'Paul E.',    'depuis' => '1 an',   'note' => 5, 'date' => 'Mars 2026',    'texte' => "Séance de nage matinale magnifique dans un jardin calme. %HOTE% pense à tout, serviettes et boissons fraîches mises à disposition. Bravo."),
    array('initiale' => 'M', 'nom' => 'Manon S.',   'depuis' => '5 mois', 'note' => 4, 'date' => 'Mars 2026',    'texte' => "Endroit agréable et propre, hôte aux petits soins. On aurait aimé un peu plus de rangements pour les affaires, mais rien de gênant."),
    array('initiale' => 'J', 'nom' => 'Julie A.',   'depuis' => '9 mois', 'note' => 5, 'date' => 'Février 2026', 'texte' => "Bassin impeccable et ambiance très reposante, même en hiver le coin est agréable. %HOTE% est arrangeant sur les horaires, un vrai plaisir."),
);
$pp_hote_prenom = $pp_hote ? $pp_hote['prenom'] : 'votre hôte';

// Image principale du bien (image à la une ou champ « image ») et son
// texte alternatif : sert de première photo de la galerie ci-dessous.
$pp_image   = poolparty_g4_image_url($pp_id);
$pp_alt     = poolparty_g4_meta($pp_id, 'alt');
if (!$pp_image) {
    $pp_image = get_template_directory_uri() . '/assets/images/piscines/annonce-pantin.jpg';
}

// Photo et avatar de l'hôte (chemins dans les assets du thème).
$pp_hote_photo = ($pp_hote && !empty($pp_hote['photo']))
    ? pp_asset($pp_hote['photo'])
    : get_template_directory_uri() . '/assets/images/temoignages/temoin-julien.jpg';

// Description : contenu de l'éditeur découpé en paragraphes. Le premier
// reste visible, les suivants sont dévoilés par « Lire la suite ».
$pp_desc   = trim(wp_strip_all_tags(get_the_content()));
$pp_paras  = preg_split('/\n\s*\n/', $pp_desc);
$pp_paras  = array_values(array_filter(array_map('trim', $pp_paras), 'strlen'));

// Tarifs des trois formules, dérivés du prix horaire du bien pour rester
// cohérents avec l'en-tête « Dès X€ » (avant, les montants étaient figés
// à 8/90/170 quel que soit le bien) :
//   - à l'heure : le prix horaire, facturé par personne ;
//   - demi-journée (4 h) : forfait privatif = 4 × le prix horaire ;
//   - journée (8 h) : forfait privatif = 7,5 × le prix horaire (remise).
$pp_prix_heure   = (float) $pp_prix;
$pp_prix_demi    = round($pp_prix_heure * 4);
$pp_prix_journee = round($pp_prix_heure * 7.5);
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane + en-tête de l'annonce -->
        <section class="produit-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>"><?php echo esc_html($pp_cat); ?>s</a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>"><?php echo esc_html($pp_ville); ?></a></li>
                    <li class="is-current" aria-current="page"><?php echo esc_html($pp_titre); ?></li>
                </ol>
            </nav>

            <div class="produit-entete"<?php if ($pp_favori) : ?> data-favori-id="<?php echo esc_attr($pp_favori); ?>"<?php endif; ?>>
                <h1><?php echo esc_html($pp_titre); ?></h1>
                <div class="produit-entete__ligne">
                    <span class="rating"><?php echo esc_html($pp_note_fr); ?> <a class="rating__link" href="#avis"><?php echo esc_html($pp_nb_avis); ?> commentaires</a></span>
                    <p class="produit-entete__info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <?php echo esc_html($pp_ville); ?>
                    </p>
                    <p class="produit-entete__info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>
                        Hôte vérifiée
                    </p>
                    <p class="produit-entete__info">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Jusqu'à <?php echo esc_html(poolparty_g4_meta($pp_id, 'capacite_max')); ?> personnes
                    </p>
                    <div class="produit-entete__actions">
                        <button type="button" class="produit-action js-partager">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.6" y1="13.5" x2="15.4" y2="17.5"/><line x1="15.4" y1="6.5" x2="8.6" y2="10.5"/></svg>
                            <span>Partager</span>
                        </button>
                        <button type="button" class="produit-action produit-action--fav" aria-pressed="false">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.51 4.04 3 5.5l7 7Z"/></svg>
                            <span>Favori</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- BLOC 2 : Galerie photos. Mobile : carrousel plein écran avec
             barre retour / partager / favori, points et compteur (maquette).
             Tablette et desktop : mosaïque avec vignettes. -->
        <section class="galerie" aria-label="Photos de l'espace">

            <!-- Barre d'actions mobile posée au-dessus de la photo -->
            <div class="galerie__barre">
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="galerie__retour" aria-label="Revenir à la liste des annonces">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                </a>
                <div class="galerie__barre-actions">
                    <button type="button" class="produit-action js-partager" aria-label="Partager l'annonce">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.6" y1="13.5" x2="15.4" y2="17.5"/><line x1="15.4" y1="6.5" x2="8.6" y2="10.5"/></svg>
                    </button>
                    <button type="button" class="produit-action produit-action--fav" aria-pressed="false" aria-label="Ajouter aux favoris">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.51 4.04 3 5.5l7 7Z"/></svg>
                    </button>
                </div>
            </div>

            <!-- Carrousel mobile : une photo par écran, défilement horizontal -->
            <div class="galerie__mobile">
                <div class="galerie__carrousel" role="group" aria-roledescription="carrousel" aria-label="Faites défiler les photos">
                    <button type="button" class="galerie__diapo" aria-haspopup="dialog" aria-controls="galerie-popup" aria-label="Agrandir : <?php echo esc_attr($pp_alt); ?>">
                        <img src="<?php echo esc_url($pp_image); ?>" alt="">
                    </button>
                    <button type="button" class="galerie__diapo" aria-haspopup="dialog" aria-controls="galerie-popup" aria-label="Agrandir : la vue dégagée depuis le bord du bassin">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-champs.jpg'); ?>" alt="">
                    </button>
                    <button type="button" class="galerie__diapo" aria-haspopup="dialog" aria-controls="galerie-popup" aria-label="Agrandir : le coin détente avec transats sur la terrasse en bois">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-croissy.jpg'); ?>" alt="">
                    </button>
                    <button type="button" class="galerie__diapo" aria-haspopup="dialog" aria-controls="galerie-popup" aria-label="Agrandir : le jardin planté de palmiers et de graminées vu de la maison">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-lagny.jpg'); ?>" alt="">
                    </button>
                    <button type="button" class="galerie__diapo" aria-haspopup="dialog" aria-controls="galerie-popup" aria-label="Agrandir : l'allée gravillonnée qui mène au portillon d'accès de la piscine">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-chelles.jpg'); ?>" alt="">
                    </button>
                </div>
                <div class="galerie__points" aria-hidden="true">
                    <span class="galerie__point is-active"></span>
                    <span class="galerie__point"></span>
                    <span class="galerie__point"></span>
                    <span class="galerie__point"></span>
                    <span class="galerie__point"></span>
                </div>
                <button type="button" class="galerie__compteur" aria-haspopup="dialog" aria-controls="galerie-popup">1 / 5</button>
            </div>

            <figure class="galerie__principale">
                <img src="<?php echo esc_url($pp_image); ?>" alt="<?php echo esc_attr($pp_alt); ?>" id="galerie-photo">
            </figure>
            <div class="galerie__vignettes" role="group" aria-label="Choisir la photo affichée en grand">
                <button type="button" class="galerie__vignette" aria-label="Afficher cette photo en grand">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-champs.jpg'); ?>" alt="La vue dégagée depuis le bord du bassin">
                </button>
                <button type="button" class="galerie__vignette" aria-label="Afficher cette photo en grand">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-croissy.jpg'); ?>" alt="Le coin détente avec transats sur la terrasse en bois">
                </button>
                <button type="button" class="galerie__vignette" aria-label="Afficher cette photo en grand">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-lagny.jpg'); ?>" alt="Le jardin planté de palmiers et de graminées vu de la maison">
                </button>
                <button type="button" class="galerie__vignette" aria-label="Afficher cette photo en grand">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-chelles.jpg'); ?>" alt="L'allée gravillonnée qui mène au portillon d'accès de la piscine">
                </button>
            </div>
            <button type="button" class="galerie__toutes" aria-haspopup="dialog" aria-controls="galerie-popup">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><rect x="4" y="4" width="7" height="7" rx="1"/><rect x="13" y="4" width="7" height="7" rx="1"/><rect x="4" y="13" width="7" height="7" rx="1"/><rect x="13" y="13" width="7" height="7" rx="1"/></svg>
                Afficher toutes les photos
            </button>
        </section>

        <!-- Pop-up de la galerie complète -->
        <div class="popup-overlay" id="galerie-popup" hidden>
            <div class="popup galerie-popup" role="dialog" aria-modal="true" aria-labelledby="galerie-popup-titre">
                <button type="button" class="popup__close" aria-label="Fermer la galerie"></button>
                <h2 class="popup__title" id="galerie-popup-titre">Toutes les photos</h2>
                <div class="popup__body galerie-popup__liste">
                    <img src="<?php echo esc_url($pp_image); ?>" alt="<?php echo esc_attr($pp_alt); ?>">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-champs.jpg'); ?>" alt="La vue dégagée depuis le bord du bassin">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-croissy.jpg'); ?>" alt="Le coin détente avec transats sur la terrasse en bois">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-lagny.jpg'); ?>" alt="Le jardin planté de palmiers et de graminées vu de la maison">
                    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/piscines/annonce-chelles.jpg'); ?>" alt="L'allée gravillonnée qui mène au portillon d'accès de la piscine">
                </div>
            </div>
        </div>

        <!-- BLOC 3 : contenu de l'annonce + panneau de réservation -->
        <div class="produit-layout">

            <div class="produit-contenu">

                <!-- Hôte -->
                <div class="hote">
                    <img class="hote__avatar" src="<?php echo esc_url($pp_hote_photo); ?>" alt="">
                    <div>
                        <p class="hote__nom">Hôte : <?php echo esc_html($pp_hote ? $pp_hote['prenom'] : ''); ?></p>
                        <p class="hote__infos">8 mois d'expérience en tant qu'hôte</p>
                    </div>
                </div>

                <!-- Description (contenu de l'éditeur, propre à chaque bien) -->
                <section class="produit-section" aria-labelledby="description-titre">
                    <h2 id="description-titre">Description</h2>
                    <?php if (!empty($pp_paras)) : ?>
                        <p><?php echo esc_html($pp_paras[0]); ?></p>
                        <?php if (count($pp_paras) > 1) : ?>
                            <div class="description__more" id="description-suite" hidden>
                                <?php for ($i = 1; $i < count($pp_paras); $i++) : ?>
                                    <p><?php echo esc_html($pp_paras[$i]); ?></p>
                                <?php endfor; ?>
                            </div>
                            <button type="button" class="description__toggle" aria-expanded="false" aria-controls="description-suite">Lire la suite</button>
                        <?php endif; ?>
                    <?php endif; ?>
                </section>

                <!-- Caractéristiques de la piscine -->
                <section class="produit-section" aria-labelledby="caracs-titre">
                    <h2 id="caracs-titre">Caractéristiques de la piscine</h2>
                    <div class="caracs" id="caracs-cartes">
                        <div class="card-carac">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3 4 7l4 4"/><path d="M4 7h16"/><path d="m16 21 4-4-4-4"/><path d="M20 17H4"/></svg>
                            <span class="card-carac__label">Dimensions</span>
                            <span class="card-carac__value">8 x 4 m</span>
                        </div>
                        <div class="card-carac">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/></svg>
                            <span class="card-carac__label">Profondeur</span>
                            <span class="card-carac__value">1,20 - 2,10 m</span>
                        </div>
                        <div class="card-carac">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <span class="card-carac__label">Capacité</span>
                            <span class="card-carac__value">6 pers. max</span>
                        </div>
                        <div class="card-carac">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3v13M17 3v13"/><path d="M7 7h10M7 11h10"/><path d="M2 19c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/></svg>
                            <span class="card-carac__label">Type</span>
                            <span class="card-carac__value">Enterrée</span>
                        </div>
                        <div class="card-carac">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 14.76V5a2 2 0 0 0-4 0v9.76a4.5 4.5 0 1 0 4 0z"/></svg>
                            <span class="card-carac__label">Température</span>
                            <span class="card-carac__value">Chauffée à 28°C</span>
                        </div>
                        <div class="card-carac">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3c-3 4-6 7-6 10.5a6 6 0 0 0 12 0C18 10 15 7 12 3z"/></svg>
                            <span class="card-carac__label">Traitement</span>
                            <span class="card-carac__value">Au sel</span>
                        </div>
                    </div>
                    <button type="button" class="caracs__toggle" aria-expanded="false" aria-controls="caracs-cartes">Voir plus</button>
                </section>

                <!-- Équipements -->
                <section class="produit-section" aria-labelledby="equipements-titre">
                    <h2 id="equipements-titre">Équipements disponibles</h2>
                    <ul class="equipements">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Transats (4)
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Bouées / Frites de piscine
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Jardin
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Toilette
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Douche extérieure
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Parasol
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Table et chaise de jardin
                        </li>
                    </ul>
                </section>

                <!-- Sécurité du bassin -->
                <section class="produit-section" aria-labelledby="securite-titre">
                    <div class="securite">
                        <div class="securite__entete">
                            <span class="securite__badge" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5l-8-3z"/><path d="m8.5 12 2.5 2.5 4.5-4.5"/></svg>
                            </span>
                            <h2 class="securite__titre" id="securite-titre">Sécurité de la piscine</h2>
                        </div>
                        <ul class="securite__liste">
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                                Barrière piscine (NF P90-306)
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                                Abri de piscine
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                                Alarme immergée
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                                Couverture sécurité (NF P90-308)
                            </li>
                        </ul>
                        <p class="securite__texte">Cette piscine est équipée d'un système de sécurité conforme à la norme NF P90-306 (barrière de protection avec portillon à verrouillage automatique). La baignade des enfants reste sous la responsabilité exclusive des adultes accompagnants.</p>
                    </div>
                </section>

                <!-- Règlement intérieur -->
                <section class="produit-section" aria-labelledby="reglement-titre">
                    <h2 id="reglement-titre">Règlement intérieur</h2>
                    <div class="reglement">
                        <div class="reglement__colonne">
                            <p class="reglement__titre reglement__titre--oui">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 10v12"/><path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z"/></svg>
                                Autorisés
                            </p>
                            <ul>
                                <li>Pique-nique froid</li>
                                <li>Alcool</li>
                                <li>Musique à un volume modéré</li>
                                <li>Bouées et jeux d'eau</li>
                                <li>Événements</li>
                            </ul>
                        </div>
                        <div class="reglement__colonne">
                            <p class="reglement__titre reglement__titre--non">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M8 12h8"/></svg>
                                Interdits
                            </p>
                            <ul>
                                <li>Cigarette</li>
                                <li>Verre à proximité du bassin</li>
                                <li>Animaux</li>
                                <li>Musique après 20h</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Avis -->
                <section class="produit-section produit-section--filet" id="avis" aria-labelledby="avis-titre">
                    <h2 class="sr-only" id="avis-titre">Les avis des baigneurs</h2>

                    <?php
                    // Critères d'évaluation (démonstration, mêmes 6 que la
                    // maquette) et mots-clés récurrents : réutilisés sur la
                    // fiche ET dans la pop-up via des closures pour éviter
                    // toute duplication de balisage.
                    $pp_criteres = array(
                        array('proprete',    '4,8', 'Propreté'),
                        array('precision',   '4,7', 'Précision'),
                        array('arrivee',     '4,6', 'Arrivée'),
                        array('contact',     '4,9', 'Contact'),
                        array('emplacement', '4,4', 'Emplacement'),
                        array('qualite',     '4,5', 'Qualité-prix'),
                    );
                    $pp_termes = array('Emplacement', 'Propreté', 'Cadre calme', 'Hôte accueillant', 'Baignade', 'Barbecue');

                    // Barres de répartition des notes (5 à 1 étoile).
                    $pp_render_repartition = function () use ($pp_rep, $pp_nb_avis_int) {
                        foreach (array(5, 4, 3, 2, 1) as $pp_etoiles) :
                            $pp_compte       = $pp_rep[$pp_etoiles];
                            $pp_pct          = $pp_nb_avis_int > 0 ? round($pp_compte / $pp_nb_avis_int * 100) : 0;
                            $pp_label_etoile = $pp_etoiles > 1 ? $pp_etoiles . ' étoiles' : '1 étoile';
                            $pp_aria         = ($pp_compte === 0 ? 'Aucun avis à ' : $pp_compte . ' avis à ') . $pp_label_etoile;
                            ?>
                            <li>
                                <span class="avis-note__label"><?php echo esc_html($pp_label_etoile); ?></span>
                                <div class="progress" data-value="<?php echo esc_attr($pp_pct); ?>" role="progressbar" aria-valuenow="<?php echo esc_attr($pp_compte); ?>" aria-valuemin="0" aria-valuemax="<?php echo esc_attr($pp_nb_avis_int); ?>" aria-label="<?php echo esc_attr($pp_aria); ?>"><span class="progress__bar"></span></div>
                                <span class="avis-note__compte">(<?php echo esc_html($pp_compte); ?>)</span>
                            </li>
                            <?php
                        endforeach;
                    };

                    // Rangée des six critères (icône + score + libellé).
                    $pp_render_criteres = function () use ($pp_criteres) {
                        foreach ($pp_criteres as $pp_cr) : ?>
                            <div class="rating-critere">
                                <span class="rating-critere__icon rating-critere__icon--<?php echo esc_attr($pp_cr[0]); ?>" aria-hidden="true"></span>
                                <p class="rating-critere__score"><?php echo esc_html($pp_cr[1]); ?></p>
                                <p class="rating-critere__label"><?php echo esc_html($pp_cr[2]); ?></p>
                            </div>
                            <?php
                        endforeach;
                    };
                    ?>
                    <div class="avis-stats">
                        <div class="avis-note">
                            <p class="avis-note__score"><?php echo esc_html($pp_note_fr); ?>/5</p>
                            <p class="avis-note__total"><?php echo esc_html($pp_nb_avis_int); ?> commentaire<?php echo $pp_nb_avis_int > 1 ? 's' : ''; ?></p>
                            <ul class="avis-note__repartition">
                                <?php $pp_render_repartition(); ?>
                            </ul>
                        </div>

                        <div class="avis-criteres">
                            <?php $pp_render_criteres(); ?>
                        </div>
                    </div>

                    <?php
                    // Rend un avis (article). Réutilisé pour l'aperçu de la
                    // fiche (4 premiers) et pour la pop-up qui liste tout.
                    // Le marqueur %HOTE% laisse place au prénom réel de l'hôte.
                    // $pp_cache = true masque l'avis au départ (dévoilé par le
                    // bouton de la pop-up). Le bouton « Lire la suite » est posé
                    // masqué : le JS ne l'affiche que si le texte est tronqué
                    // (uniquement dans la pop-up, où les textes sont écrêtés).
                    $pp_render_avis = function ($pp_c, $pp_cache = false) use ($pp_hote_prenom) {
                        $pp_note_c = (int) $pp_c['note'];
                        $pp_texte  = str_replace('%HOTE%', $pp_hote_prenom, $pp_c['texte']);
                        ?>
                        <article class="commentaire<?php echo $pp_cache ? ' commentaire--extra' : ''; ?>"<?php echo $pp_cache ? ' hidden' : ''; ?>>
                            <header class="commentaire__entete">
                                <span class="commentaire__avatar" aria-hidden="true"><?php echo esc_html($pp_c['initiale']); ?></span>
                                <div class="commentaire__auteur">
                                    <p class="commentaire__nom"><?php echo esc_html($pp_c['nom']); ?></p>
                                    <p class="commentaire__depuis"><?php echo esc_html($pp_c['depuis']); ?> sur Pool Party</p>
                                </div>
                                <div class="commentaire__note">
                                    <span class="commentaire__stars" role="img" aria-label="Note de <?php echo esc_attr($pp_note_c); ?> sur 5"><?php for ($pp_s = 1; $pp_s <= 5; $pp_s++) : ?><span<?php echo $pp_s > $pp_note_c ? ' class="is-off"' : ''; ?>></span><?php endfor; ?></span>
                                    <span class="commentaire__date"><?php echo esc_html($pp_c['date']); ?></span>
                                </div>
                            </header>
                            <p class="commentaire__texte">« <?php echo esc_html($pp_texte); ?> »</p>
                            <button type="button" class="commentaire__lire" hidden>Lire la suite</button>
                        </article>
                        <?php
                    };

                    // On n'affiche que « nb_avis » commentaires pour coller au
                    // compteur du CMS ; l'aperçu montre les 4 premiers.
                    $pp_avis_visibles = min($pp_nb_avis_int, count($pp_avis_demo));
                    $pp_avis_apercu   = min(4, $pp_avis_visibles);
                    ?>
                    <div class="commentaires" id="avis-liste">
                        <?php for ($pp_i = 0; $pp_i < $pp_avis_apercu; $pp_i++) : ?>
                            <?php $pp_render_avis($pp_avis_demo[$pp_i]); ?>
                        <?php endfor; ?>
                    </div>

                    <?php if ($pp_avis_visibles > $pp_avis_apercu) : ?>
                    <button type="button" class="btn btn-tertiary avis-plus" aria-haspopup="dialog" aria-controls="avis-popup">Afficher les <?php echo esc_html($pp_avis_visibles); ?> commentaire<?php echo $pp_avis_visibles > 1 ? 's' : ''; ?></button>

                    <!-- Pop-up « tous les avis » (maquette Figma) : carte blanche
                         arrondie, en-tête centré (note + jauges), rangée de
                         critères, filet, tri, avis sur une seule colonne, puis
                         le bouton qui dévoile le reste des commentaires. -->
                    <div class="popup-overlay" id="avis-popup" hidden>
                        <div class="avis-popup">
                            <div class="login-popup__card avis-popup__card" role="dialog" aria-modal="true" aria-labelledby="avis-popup-titre">
                                <div class="avis-popup__head">
                                    <h2 id="avis-popup-titre" class="sr-only">Tous les commentaires</h2>
                                    <button type="button" class="login-popup__close avis-popup__close" aria-label="Fermer les commentaires">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <!-- En-tête centré : note globale + nombre + jauges -->
                                <div class="avis-popup__resume">
                                    <p class="avis-note__score avis-popup__score"><?php echo esc_html($pp_note_fr); ?>/5</p>
                                    <p class="avis-popup__total"><?php echo esc_html($pp_avis_visibles); ?> commentaire<?php echo $pp_avis_visibles > 1 ? 's' : ''; ?></p>
                                    <ul class="avis-note__repartition avis-popup__repartition">
                                        <?php $pp_render_repartition(); ?>
                                    </ul>
                                </div>

                                <!-- Rangée des critères -->
                                <div class="avis-criteres avis-popup__criteres">
                                    <?php $pp_render_criteres(); ?>
                                </div>

                                <hr class="avis-popup__filet">

                                <!-- Tri des avis -->
                                <div class="avis-popup__barre">
                                    <button type="button" class="avis-tri" aria-label="Trier les commentaires">
                                        Les plus pertinents
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                                    </button>
                                </div>

                                <!-- Avis sur une seule colonne (les 4 premiers, le
                                     reste dévoilé par le bouton du bas) -->
                                <div class="commentaires avis-popup__liste">
                                    <?php for ($pp_i = 0; $pp_i < $pp_avis_visibles; $pp_i++) : ?>
                                        <?php $pp_render_avis($pp_avis_demo[$pp_i], $pp_i >= $pp_avis_apercu); ?>
                                    <?php endfor; ?>
                                </div>

                                <?php if ($pp_avis_visibles > $pp_avis_apercu) : ?>
                                <button type="button" class="btn btn-tertiary avis-popup__plus">Afficher les <?php echo esc_html($pp_avis_visibles - $pp_avis_apercu); ?> commentaire<?php echo ($pp_avis_visibles - $pp_avis_apercu) > 1 ? 's' : ''; ?></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </section>

                <!-- Localisation -->
                <section class="produit-section produit-section--filet" aria-labelledby="localisation-titre">
                    <h2 id="localisation-titre">Où se situe l'espace</h2>
                    <p class="localisation__adresse">Boulogne-Billancourt, Paris, France</p>
                    <div class="localisation__carte">
                        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/produit/map.png'); ?>" alt="Plan du quartier de l'annonce à Boulogne-Billancourt">
                        <img class="map-pin localisation__pin" src="<?php echo esc_url(get_template_directory_uri() . '/assets/icons/pin-map.svg'); ?>" alt="">
                    </div>
                </section>

                <!-- Accès et stationnement -->
                <section class="produit-section produit-section--filet" aria-labelledby="acces-titre">
                    <h2 id="acces-titre">Accès et stationnement</h2>
                    <div class="acces-carte">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="3" width="16" height="16" rx="2"/><path d="M4 11h16"/><path d="M12 3v8"/><path d="m8 19-2 3"/><path d="m18 22-2-3"/><circle cx="8" cy="15" r="1" fill="currentColor" stroke="none"/><circle cx="16" cy="15" r="1" fill="currentColor" stroke="none"/></svg>
                        <div>
                            <p class="acces-carte__ligne">Métro 10 : Gare de Boulogne Jean Jaurès</p>
                            <p class="acces-carte__detail">À 12 min à pied de la piscine</p>
                        </div>
                    </div>
                    <p>Stationnement gratuit dans la rue.</p>
                    <p>Accès indépendant via une porte de jardin sécurisée par un code transmis 24h avant.</p>
                </section>

                <!-- Faites connaissance avec votre hôte -->
                <section class="produit-section produit-section--filet" aria-labelledby="hote-titre">
                    <div class="hote-carte">
                        <div class="hote-carte__profil">
                            <h2 class="hote-carte__titre" id="hote-titre">Faites connaissance avec votre hôte</h2>
                            <div class="hote-carte__identite">
                                <img class="hote-carte__avatar" src="<?php echo esc_url($pp_hote_photo); ?>" alt="">
                                <div>
                                    <p class="hote-carte__nom"><?php echo esc_html($pp_hote ? $pp_hote['prenom'] : ''); ?></p>
                                    <p class="hote-carte__depuis">Hôte depuis 8 mois</p>
                                    <span class="rating rating--small"><?php echo esc_html($pp_note_fr); ?> <a class="rating__link" href="#avis"><?php echo esc_html($pp_nb_avis_int); ?> commentaire<?php echo $pp_nb_avis_int > 1 ? 's' : ''; ?></a></span>
                                </div>
                            </div>
                            <p class="hote-carte__sous-titre">Information sur l'hôte</p>
                            <ul class="hote-carte__infos">
                                <li>Répond sous 2h</li>
                                <li>Taux de réponse : 100 %</li>
                            </ul>
                            <p class="hote-carte__bio"><?php echo esc_html($pp_hote && !empty($pp_hote['bio']) ? $pp_hote['bio'] : "J'ouvre mon espace aux visiteurs en quête de calme et de détente. Présent pour l'accueil, je vous laisse ensuite profiter en toute intimité."); ?></p>
                        </div>
                        <div class="hote-carte__extras">
                            <p class="hote-carte__sous-titre">Extras proposés sur demande</p>
                            <ul class="hote-carte__extras-liste">
                                <li>Barbecue</li>
                                <li>Prêt de serviettes</li>
                                <li>Jeux de jardin</li>
                                <li>Parasol supplémentaire</li>
                                <li>Pack apéro</li>
                            </ul>
                            <p class="hote-carte__mention">Les conditions et tarifs sont à convenir directement avec votre hôte, Pool Party n'intervient pas dans ces échanges.</p>
                            <button type="button" class="btn btn-secondary hote-carte__cta js-message-hote" aria-haspopup="dialog" aria-controls="message-hote-popup">Envoyer un message à l'hôte</button>
                            <p class="hote-carte__mention">Afin de protéger votre paiement, utilisez toujours Pool Party pour envoyer de l'argent et communiquer avec les hôtes.</p>
                        </div>
                    </div>
                </section>

                <!-- Pop-up : écrire un message à l'hôte. Reprend le fond de la
                     pop-up de connexion (login-popup) ; la soumission envoie
                     réellement un e-mail à l'hôte (service FormSubmit, sans
                     backend), comme la demande de réservation. -->
                <div class="popup-overlay" id="message-hote-popup" hidden>
                    <div class="login-popup">
                        <div class="login-popup__card" role="dialog" aria-modal="true" aria-labelledby="message-hote-titre">
                            <div class="login-popup__head">
                                <h2 id="message-hote-titre" class="login-popup__title">Écrire à <?php echo esc_html($pp_hote_prenom); ?></h2>
                                <button type="button" class="login-popup__close" aria-label="Fermer la fenêtre">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <form class="login-popup__form" data-hote="<?php echo esc_attr($pp_hote_prenom); ?>" data-annonce="<?php echo esc_attr($pp_titre); ?>" novalidate>
                                <div class="login-popup__fields">
                                    <div class="form-field">
                                        <label class="form-field__label" for="message-hote-prenom">Votre prénom</label>
                                        <input class="form-field__input" type="text" id="message-hote-prenom" name="prenom" placeholder="Tapez votre prénom" autocomplete="given-name">
                                    </div>
                                    <div class="form-field">
                                        <label class="form-field__label" for="message-hote-email">Votre adresse e-mail</label>
                                        <input class="form-field__input" type="email" id="message-hote-email" name="email" placeholder="Pour recevoir la réponse de l'hôte" autocomplete="email">
                                    </div>
                                    <div class="form-field">
                                        <label class="form-field__label" for="message-hote-texte">Votre message</label>
                                        <textarea class="form-field__input" id="message-hote-texte" name="message" rows="5" placeholder="Bonjour, votre espace est-il disponible le..." required></textarea>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary login-popup__submit" disabled>Envoyer le message</button>

                                <p class="login-popup__signup" id="message-hote-statut" role="status" hidden></p>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- À savoir -->
                <section class="produit-section produit-section--filet" aria-labelledby="savoir-titre">
                    <h2 id="savoir-titre">À savoir</h2>
                    <div class="savoir-grille">
                        <div class="card-savoir">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 9h18M10 13l4 4M14 13l-4 4"/></svg>
                            <p class="card-savoir__title">Conditions d'annulation</p>
                            <p class="card-savoir__text">Cet hôte applique la politique souple : remboursement intégral jusqu'à 48 heures avant le créneau. Consultez les conditions complètes avant de réserver.</p>
                            <a class="link-signal link-signal--simple" href="<?php echo esc_url(home_url('/cgv/')); ?>">En savoir plus</a>
                        </div>
                        <div class="card-savoir">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                            <p class="card-savoir__title">Dépôt de garantie</p>
                            <p class="card-savoir__text">Empreinte bancaire de 200 € bloquée à la réservation, libérée sous 7 jours après votre passage si aucun dommage n'est signalé.</p>
                            <a class="link-signal link-signal--simple" href="<?php echo esc_url(home_url('/cgv/')); ?>">En savoir plus</a>
                        </div>
                        <div class="card-savoir">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5l-8-3z"/><path d="m8.5 12 2.5 2.5 4.5-4.5"/></svg>
                            <p class="card-savoir__title">Paiement sécurisé</p>
                            <p class="card-savoir__text">Transaction gérée par Stripe, certifiée PCI-DSS. Vos données bancaires ne transitent jamais par les serveurs Pool Party.</p>
                            <a class="link-signal link-signal--simple" href="<?php echo esc_url(home_url('/cgv/')); ?>">En savoir plus</a>
                        </div>
                        <div class="card-savoir">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                            <p class="card-savoir__title">Confidentialité et données</p>
                            <p class="card-savoir__text">Vos coordonnées ne sont communiquées à l'hôte qu'après confirmation de la réservation, conformément au RGPD.</p>
                            <a class="link-signal link-signal--simple" href="<?php echo esc_url(home_url('/mentions-legales/')); ?>">En savoir plus</a>
                        </div>
                    </div>
                </section>

            </div>

            <!-- Panneau de réservation -->
            <aside class="resa-panel" aria-labelledby="resa-titre">
                <h2 class="sr-only" id="resa-titre">Panneau de réservation</h2>
                <form class="resa" data-bien-id="<?php echo esc_attr($pp_id); ?>">

                    <div class="resa__entete">
                        <p class="resa__prix">Dès <?php echo esc_html($pp_prix); ?>€</p>
                        <p class="resa__prix-note">(Montant de réservation minimum)</p>
                    </div>

                    <div class="resa__formules" role="group" aria-label="Choisir une formule">
                        <button type="button" class="resa-formule" data-formule="heure" data-prix="<?php echo esc_attr($pp_prix_heure); ?>" aria-pressed="false">
                            <span class="resa-formule__nom">À l'heure</span>
                            <span class="resa-formule__detail">1 heure :</span>
                            <span class="resa-formule__prix"><?php echo esc_html($pp_prix_heure); ?>€/pers</span>
                        </button>
                        <button type="button" class="resa-formule is-active" data-formule="demi-journee" data-prix="<?php echo esc_attr($pp_prix_demi); ?>" aria-pressed="true">
                            <span class="resa-formule__badge">Populaire</span>
                            <span class="resa-formule__nom">Demi-journée</span>
                            <span class="resa-formule__detail">4 heures :</span>
                            <span class="resa-formule__prix"><?php echo esc_html($pp_prix_demi); ?>€</span>
                        </button>
                        <button type="button" class="resa-formule" data-formule="journee" data-prix="<?php echo esc_attr($pp_prix_journee); ?>" aria-pressed="false">
                            <span class="resa-formule__nom">Journée</span>
                            <span class="resa-formule__detail">8 heures :</span>
                            <span class="resa-formule__prix"><?php echo esc_html($pp_prix_journee); ?>€</span>
                        </button>
                    </div>

                    <div class="resa__champ resa-field">
                        <label class="resa__champ-label" for="resa-date">Date</label>
                        <input class="resa__champ-input" id="resa-date" name="date" type="text" value="15/07/2026" readonly>
                        <svg class="resa__champ-icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 9h18"/></svg>
                    </div>

                    <div class="resa__champ resa__creneau">
                        <label class="resa__champ-label" for="resa-creneau">Créneau</label>
                        <input class="resa__champ-input" id="resa-creneau" name="creneau" type="text" value="Après-midi 14h-18h" readonly>
                        <svg class="resa__champ-icone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
                        <ul class="resa__creneau-liste" id="resa-creneau-liste" hidden>
                            <li><button type="button" class="dropdown-item">Matin 9h-13h</button></li>
                            <li><button type="button" class="dropdown-item is-active">Après-midi 14h-18h</button></li>
                            <li><button type="button" class="dropdown-item">Soirée 18h-22h</button></li>
                        </ul>
                    </div>

                    <div class="resa__voyageurs">
                        <div class="resa-stepper" data-compte="adultes" data-min="1" data-max="6">
                            <div class="resa-stepper__libelle">
                                <span class="resa-stepper__nom">Adultes</span>
                                <span class="resa-stepper__detail">13 ans et +</span>
                            </div>
                            <div class="resa-stepper__controles">
                                <button type="button" class="resa-stepper__btn" data-action="moins" aria-label="Retirer un adulte">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>
                                </button>
                                <span class="resa-stepper__valeur" aria-live="polite">2</span>
                                <button type="button" class="resa-stepper__btn" data-action="plus" aria-label="Ajouter un adulte">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M12 5v14"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="resa-stepper" data-compte="enfants" data-min="0" data-max="6">
                            <div class="resa-stepper__libelle">
                                <span class="resa-stepper__nom">Enfants</span>
                                <span class="resa-stepper__detail">De 2 à 12 ans</span>
                            </div>
                            <div class="resa-stepper__controles">
                                <button type="button" class="resa-stepper__btn" data-action="moins" aria-label="Retirer un enfant">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>
                                </button>
                                <span class="resa-stepper__valeur" aria-live="polite">2</span>
                                <button type="button" class="resa-stepper__btn" data-action="plus" aria-label="Ajouter un enfant">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M12 5v14"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="resa-stepper" data-compte="bebes" data-min="0" data-max="4">
                            <div class="resa-stepper__libelle">
                                <span class="resa-stepper__nom">Bébé</span>
                                <span class="resa-stepper__detail">Moins de 3 ans</span>
                            </div>
                            <div class="resa-stepper__controles">
                                <button type="button" class="resa-stepper__btn" data-action="moins" aria-label="Retirer un bébé">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>
                                </button>
                                <span class="resa-stepper__valeur" aria-live="polite">0</span>
                                <button type="button" class="resa-stepper__btn" data-action="plus" aria-label="Ajouter un bébé">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M12 5v14"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <dl class="resa__recap">
                        <div class="resa__ligne">
                            <dt data-resa="libelle">Demi-journée privative</dt>
                            <dd data-resa="sous-total">90,00€</dd>
                        </div>
                        <div class="resa__ligne">
                            <dt>Frais de service (15%)</dt>
                            <dd data-resa="frais">13,50€</dd>
                        </div>
                        <div class="resa__ligne resa__ligne--total">
                            <dt>Total TTC</dt>
                            <dd data-resa="total">103,50€</dd>
                        </div>
                    </dl>
                    <p class="resa__ecolo">Dont <span data-resa="ecolo">0,90€</span> est reversé pour l'écologie</p>

                    <button type="submit" class="btn btn-primary btn-medium resa__submit">Réserver</button>
                    <p class="resa__note">Vous ne serez débité qu'après confirmation de l'hôte</p>
                </form>

                <a class="resa__signaler" href="<?php echo esc_url(home_url('/contact/')); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                    Signaler cette annonce
                </a>
            </aside>
        </div>

        <!-- Barre de réservation mobile (maquette mobile) : total TTC et
             bouton Réserver fixés en bas de l'écran pendant tout le
             défilement, comme le panneau collant des grands écrans.
             Masquée dès la tablette. -->
        <div class="resa-barre">
            <p class="resa-barre__total">Total TTC <strong data-resa="total-barre">103,50€</strong></p>
            <button type="button" class="btn btn-primary btn-medium resa-barre__btn">Réserver</button>
        </div>

        <!-- BLOC 4 : espaces similaires -->
        <section class="similaires" aria-labelledby="similaires-titre">
            <div class="similaires__inner">
                <h2 id="similaires-titre">Espaces similaires à proximité</h2>
                <div class="similaires-grille">
                    <?php if (!empty($pp_similaires)) : ?>
                        <?php foreach ($pp_similaires as $pp_sim_id) : ?>
                            <?php poolparty_g4_carte_bien($pp_sim_id); ?>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="similaires__vide">Aucun autre espace à proposer pour le moment.</p>
                    <?php endif; ?>
                </div>
                <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="btn btn-tertiary similaires__explorer">Explorez tous les espaces</a>
            </div>
        </section>
    </main>

<?php get_footer(); ?>
