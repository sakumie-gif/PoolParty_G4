<?php
/**
 * Gabarit de la page « Mes annonces » (espace membre).
 * -------------------------------------------------------------
 * L'hôte retrouve ses annonces sous forme de tuiles (photo, statut,
 * prix) et peut modifier celles qui n'ont pas de réservation en cours :
 * description, prix, capacité, équipements, jours d'ouverture, dates
 * d'indisponibilité, ajout de photos. Une annonce refusée affiche le
 * motif transmis par l'équipe et repart en validation après correction.
 * Page privée : noindex (voir functions.php). Logique dans
 * inc/mes-annonces.php.
 */

get_header();

$pp_connecte = is_user_logged_in();
$pp_flash    = poolparty_g4_mes_annonces_flash();
$pp_annonces = $pp_connecte ? poolparty_g4_mes_annonces(get_current_user_id()) : array();

// Vue édition : ?annonce=ID, seulement si l'annonce appartient au membre.
// (Le paramètre ne peut pas s'appeler « bien » : c'est le nom du type de
// contenu, WordPress l'interprète et renvoie une page introuvable.)
$pp_edition = null;
if ($pp_connecte && isset($_GET['annonce'])) {
    $pp_cible = get_post(absint($_GET['annonce']));
    if ($pp_cible && $pp_cible->post_type === 'bien'
        && ((int) $pp_cible->post_author === get_current_user_id() || current_user_can('manage_options'))) {
        $pp_edition = $pp_cible;
    }
}
?>

    <main id="contenu">
        <section class="mes-annonces-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li class="is-current" aria-current="page">Mes annonces</li>
                </ol>
            </nav>
        </section>

        <div class="hero-page">
            <p class="hero-page__surtitre">Espace hôte</p>
            <h1><?php echo $pp_edition ? 'Modifier mon annonce' : 'Mes annonces'; ?></h1>
            <p class="hero-page__texte">
                <?php if ($pp_edition) : ?>
                    Mettez à jour votre annonce : description, prix, équipements, disponibilités ou nouvelles photos.
                <?php else : ?>
                    Retrouvez ici les espaces que vous proposez, leur statut de validation, et modifiez-les quand aucune réservation n'est en cours.
                <?php endif; ?>
            </p>
            <?php if (!$pp_edition && $pp_connecte) : ?>
                <p class="hero-page__compteur"><?php echo count($pp_annonces); ?> annonce<?php echo count($pp_annonces) > 1 ? 's' : ''; ?></p>
            <?php endif; ?>
        </div>

        <section class="mes-annonces">
            <?php if ($pp_flash) : ?>
                <div class="mes-annonces__flash<?php echo $pp_flash[1] === 'alerte' ? ' mes-annonces__flash--alerte' : ''; ?>" role="status">
                    <?php echo esc_html($pp_flash[0]); ?>
                </div>
            <?php endif; ?>

            <?php if (!$pp_connecte) : ?>

                <div class="mes-annonces__etat-vide">
                    <h2>Connectez-vous à votre compte</h2>
                    <p>Vos annonces sont rattachées à votre compte. Connectez-vous pour les retrouver et les modifier.</p>
                    <button type="button" class="btn btn-primary js-open-login">Connexion</button>
                </div>

            <?php elseif ($pp_edition) : ?>

                <?php
                // L'admin garde la main même avec une réservation en cours
                // (aide aux membres qui contactent la plateforme).
                $pp_bloquee = poolparty_g4_bien_a_resa_en_cours($pp_edition->ID) && !current_user_can('manage_options');
                $pp_motif   = get_post_meta($pp_edition->ID, 'pp_refus_motif', true);
                ?>

                <?php if ($pp_motif) : ?>
                    <div class="mes-annonces__flash mes-annonces__flash--alerte">
                        Motif du refus transmis par l'équipe : <?php echo esc_html($pp_motif); ?>
                    </div>
                <?php endif; ?>

                <?php if ($pp_bloquee) : ?>

                    <div class="mes-annonces__etat-vide">
                        <h2>Modification indisponible</h2>
                        <p>Une réservation est en cours sur cette annonce (demande en attente ou venue confirmée à venir). Vous pourrez la modifier ensuite.</p>
                        <a href="<?php echo esc_url(home_url('/mes-annonces/')); ?>" class="btn btn-primary">Retour à mes annonces</a>
                    </div>

                <?php else : ?>

                    <form class="mes-annonces__form" method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="pp_annonce_action" value="modifier">
                        <input type="hidden" name="bien_id" value="<?php echo esc_attr($pp_edition->ID); ?>">
                        <?php wp_nonce_field('pp_annonce_modifier_' . $pp_edition->ID); ?>

                        <div class="mes-annonces__champ">
                            <label for="pp-annonce-titre">Titre de l'annonce</label>
                            <input type="text" id="pp-annonce-titre" name="titre" required value="<?php echo esc_attr(get_the_title($pp_edition)); ?>">
                        </div>

                        <div class="mes-annonces__champ">
                            <label for="pp-annonce-description">Description</label>
                            <textarea id="pp-annonce-description" name="description" rows="6" required><?php echo esc_textarea($pp_edition->post_content); ?></textarea>
                        </div>

                        <div class="mes-annonces__rangee">
                            <div class="mes-annonces__champ">
                                <label for="pp-annonce-prix">Prix par heure (€)</label>
                                <input type="number" id="pp-annonce-prix" name="prix" min="1" required value="<?php echo esc_attr(get_post_meta($pp_edition->ID, 'pp_prix_heure', true)); ?>">
                            </div>
                            <div class="mes-annonces__champ">
                                <label for="pp-annonce-capacite">Capacité maximale (invités)</label>
                                <input type="number" id="pp-annonce-capacite" name="capacite" min="1" value="<?php echo esc_attr(get_post_meta($pp_edition->ID, 'pp_capacite_max', true)); ?>">
                            </div>
                        </div>

                        <div class="mes-annonces__champ">
                            <label for="pp-annonce-equipements">Équipements (séparés par des virgules)</label>
                            <input type="text" id="pp-annonce-equipements" name="equipements" value="<?php echo esc_attr(get_post_meta($pp_edition->ID, 'pp_equipements', true)); ?>" placeholder="Transats, douche extérieure, barbecue">
                        </div>

                        <div class="mes-annonces__rangee">
                            <div class="mes-annonces__champ">
                                <label for="pp-annonce-jours">Jours d'ouverture (séparés par des virgules)</label>
                                <input type="text" id="pp-annonce-jours" name="jours" value="<?php echo esc_attr(get_post_meta($pp_edition->ID, 'pp_jours', true)); ?>" placeholder="Samedi, Dimanche">
                            </div>
                            <div class="mes-annonces__champ">
                                <label for="pp-annonce-indispos">Dates d'indisponibilité (séparées par des virgules)</label>
                                <input type="text" id="pp-annonce-indispos" name="indispos" value="<?php echo esc_attr(get_post_meta($pp_edition->ID, 'pp_indispos', true)); ?>" placeholder="15/08/2026, du 20/08 au 25/08">
                            </div>
                        </div>

                        <div class="mes-annonces__champ">
                            <label for="pp-annonce-photos">Ajouter des photos</label>
                            <input type="file" id="pp-annonce-photos" name="photos[]" multiple accept="image/*">
                            <p class="mes-annonces__aide">Les nouvelles photos s'ajoutent à la galerie de l'annonce.</p>
                        </div>

                        <div class="mes-annonces__form-actions">
                            <a href="<?php echo esc_url(home_url('/mes-annonces/')); ?>" class="btn btn-tertiary">Annuler</a>
                            <button type="submit" class="btn btn-primary">
                                <?php echo $pp_edition->post_status === 'draft' ? 'Enregistrer et renvoyer en validation' : 'Enregistrer les modifications'; ?>
                            </button>
                        </div>
                    </form>

                <?php endif; ?>

            <?php elseif (empty($pp_annonces)) : ?>

                <div class="mes-annonces__etat-vide">
                    <h2>Vous n'avez pas encore d'annonce</h2>
                    <p>Proposez votre espace en quelques minutes : votre annonce apparaîtra ici avec son statut de validation.</p>
                    <a href="<?php echo esc_url(home_url('/proposer/')); ?>" class="btn btn-primary">Proposer mon espace</a>
                </div>

            <?php else : ?>

                <div class="mes-annonces__grille">
                    <?php foreach ($pp_annonces as $pp_bien) :
                        $pp_badge   = poolparty_g4_mes_annonces_badge($pp_bien);
                        $pp_bloquee = poolparty_g4_bien_a_resa_en_cours($pp_bien->ID);
                        $pp_image   = get_the_post_thumbnail_url($pp_bien->ID, 'medium_large');
                        if (!$pp_image && function_exists('poolparty_g4_image_url')) {
                            $pp_image = poolparty_g4_image_url($pp_bien->ID);
                        }
                        $pp_ville = get_post_meta($pp_bien->ID, 'pp_ville', true);
                        $pp_prix  = get_post_meta($pp_bien->ID, 'pp_prix_heure', true);
                        $pp_motif = get_post_meta($pp_bien->ID, 'pp_refus_motif', true);
                        ?>
                        <article class="mes-annonces__carte">
                            <div class="mes-annonces__media">
                                <?php if ($pp_image) : ?>
                                    <img src="<?php echo esc_url($pp_image); ?>" alt="<?php echo esc_attr(get_the_title($pp_bien)); ?>" loading="lazy" decoding="async">
                                <?php endif; ?>
                                <span class="mes-annonces__etat <?php echo esc_attr($pp_badge[0]); ?>"><?php echo esc_html($pp_badge[1]); ?></span>
                            </div>
                            <div class="mes-annonces__corps">
                                <h2 class="mes-annonces__titre"><?php echo esc_html(get_the_title($pp_bien)); ?></h2>
                                <p class="mes-annonces__infos">
                                    <?php echo esc_html($pp_ville ? $pp_ville : ''); ?>
                                    <?php if ($pp_prix) : ?><?php echo $pp_ville ? ' · ' : ''; ?><?php echo esc_html($pp_prix); ?> € de l'heure<?php endif; ?>
                                </p>
                                <?php if ($pp_motif) : ?>
                                    <p class="mes-annonces__motif">Motif du refus : <?php echo esc_html($pp_motif); ?></p>
                                <?php endif; ?>
                                <div class="mes-annonces__actions">
                                    <?php if ($pp_bien->post_status === 'publish') : ?>
                                        <a class="btn btn-tertiary btn-small" href="<?php echo esc_url(get_permalink($pp_bien)); ?>">Voir</a>
                                    <?php endif; ?>
                                    <?php if ($pp_bloquee) : ?>
                                        <span class="mes-annonces__verrou">Réservation en cours : modification indisponible</span>
                                    <?php else : ?>
                                        <a class="btn btn-primary btn-small" href="<?php echo esc_url(add_query_arg('annonce', $pp_bien->ID, home_url('/mes-annonces/'))); ?>">Modifier</a>
                                    <?php endif; ?>
                                    <?php if (!$pp_bloquee) : ?>
                                        <form method="post" action="" onsubmit="return confirm('Supprimer cette annonce ? Elle sera retirée du site.');">
                                            <input type="hidden" name="pp_annonce_action" value="supprimer">
                                            <input type="hidden" name="bien_id" value="<?php echo esc_attr($pp_bien->ID); ?>">
                                            <?php wp_nonce_field('pp_annonce_supprimer_' . $pp_bien->ID); ?>
                                            <button type="submit" class="mes-annonces__trash" aria-label="Supprimer l'annonce" title="Supprimer l'annonce"><?php echo poolparty_g4_admin_icone_trash(); ?></button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </section>
    </main>

<?php get_footer(); ?>
