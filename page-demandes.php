<?php
/**
 * Gabarit de la page « Demandes de réservation » (espace hôte).
 * -------------------------------------------------------------
 * DÉSACTIVÉE le 28-07-2026 : les demandes ont fusionné dans la page
 * « Mes réservations » V2 (vue Hôte). La page redirige, le gabarit
 * d'origine est conservé ci-dessous au cas où.
 * -------------------------------------------------------------
 * Réservée aux hôtes : liste les demandes reçues sur leurs biens et
 * permet de les accepter ou de les refuser. Composée avec les briques
 * du site (fil d'Ariane, cartes .reservation-card, boutons, badges),
 * exactement comme « Mes réservations » côté locataire. Rendu serveur :
 * les demandes viennent de la base (type de contenu « reservation »).
 * Page privée : noindex (voir functions.php).
 */

// Redirection vers la page fusionnée, directement sur la vue Hôte.
wp_safe_redirect(home_url('/mes-reservations/?vue=hote'), 301);
exit;

get_header();

$pp_connecte = is_user_logged_in();
$pp_est_hote = $pp_connecte && poolparty_g4_est_hote();

// Demandes reçues par cet hôte, réparties « à traiter » / « traitées ».
$pp_a_traiter = array();
$pp_traitees  = array();
if ($pp_est_hote) {
    foreach (poolparty_g4_reservations_hote(get_current_user_id()) as $pp_resa) {
        $pp_statut = get_post_meta($pp_resa->ID, 'pp_statut', true);
        if ($pp_statut === 'acceptee' || $pp_statut === 'refusee') {
            $pp_traitees[] = $pp_resa;
        } else {
            $pp_a_traiter[] = $pp_resa;
        }
    }
}
$pp_total = count($pp_a_traiter) + count($pp_traitees);
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane + titre + compteur -->
        <section class="reservations-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li class="is-current" aria-current="page">Demandes de réservation</li>
                </ol>
            </nav>

            <h1>Demandes de réservation</h1>
            <?php if ($pp_est_hote) : ?>
                <p class="reservations-intro__sub" aria-live="polite">
                    <?php
                    if ($pp_total === 0) {
                        echo 'Aucune demande pour le moment';
                    } else {
                        $n = count($pp_a_traiter);
                        echo esc_html($n === 0
                            ? 'Aucune demande en attente'
                            : ($n === 1 ? '1 demande en attente de votre réponse' : $n . ' demandes en attente de votre réponse'));
                    }
                    ?>
                </p>
            <?php endif; ?>
        </section>

        <?php if (!$pp_connecte) : ?>

            <!-- État visiteur : connexion requise -->
            <section class="reservations-etat reservations-etat--connexion" aria-labelledby="demandes-connexion-titre">
                <svg class="reservations-etat__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/><path d="m9 15 2 2 4-4"/></svg>
                <h2 id="demandes-connexion-titre">Connectez-vous à votre compte hôte</h2>
                <p>Les demandes de réservation de vos espaces sont rattachées à votre compte. Connectez-vous pour les retrouver et y répondre.</p>
                <div class="reservations-etat__actions">
                    <button type="button" class="btn btn-primary js-open-login">Connexion</button>
                </div>
            </section>

        <?php elseif (!$pp_est_hote) : ?>

            <!-- Connecté mais pas hôte -->
            <section class="reservations-etat reservations-etat--vide" aria-labelledby="demandes-nonhote-titre">
                <svg class="reservations-etat__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                <h2 id="demandes-nonhote-titre">Cette page est réservée aux hôtes</h2>
                <p>Vous n'avez pas encore d'espace en ligne. Proposez le vôtre pour recevoir vos premières demandes de réservation ici.</p>
                <div class="reservations-etat__actions">
                    <a href="<?php echo esc_url(home_url('/proposer/')); ?>" class="btn btn-primary">Proposer mon espace</a>
                </div>
            </section>

        <?php elseif ($pp_total === 0) : ?>

            <?php
            // A-t-il déjà une annonce en ligne (ou en attente) ? On adapte le
            // message : proposer un espace s'il n'en a aucun, patienter sinon.
            $pp_mes_biens = get_posts(array(
                'post_type'      => 'bien',
                'author'         => get_current_user_id(),
                'post_status'    => array('publish', 'pending', 'draft'),
                'posts_per_page' => 1,
                'fields'         => 'ids',
            ));
            ?>
            <section class="reservations-etat reservations-etat--vide" aria-labelledby="demandes-vide-titre">
                <svg class="reservations-etat__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/><path d="M12 14v3M10.5 15.5h3"/></svg>
                <?php if (empty($pp_mes_biens)) : ?>
                    <h2 id="demandes-vide-titre">Vous n'avez pas encore d'annonce</h2>
                    <p>Proposez votre espace en quelques minutes : dès qu'un membre le réservera, sa demande apparaîtra ici et vous pourrez l'accepter ou la refuser.</p>
                    <div class="reservations-etat__actions">
                        <a href="<?php echo esc_url(home_url('/proposer/')); ?>" class="btn btn-primary">Proposer mon espace</a>
                    </div>
                <?php else : ?>
                    <h2 id="demandes-vide-titre">Aucune demande pour le moment</h2>
                    <p>Dès qu'un membre réservera l'un de vos espaces, sa demande apparaîtra ici et vous pourrez l'accepter ou la refuser.</p>
                    <div class="reservations-etat__actions">
                        <a href="<?php echo esc_url(get_post_type_archive_link('bien')); ?>" class="btn btn-primary">Voir les espaces</a>
                    </div>
                <?php endif; ?>
            </section>

        <?php else : ?>

            <!-- Liste des demandes -->
            <section class="reservations-liste" aria-label="Vos demandes de réservation">
                <?php if (!empty($pp_a_traiter)) : ?>
                    <h2 class="reservations-groupe-titre">À traiter</h2>
                    <div class="reservations-grid">
                        <?php foreach ($pp_a_traiter as $pp_resa) {
                            poolparty_g4_carte_demande($pp_resa);
                        } ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($pp_traitees)) : ?>
                    <h2 class="reservations-groupe-titre">Déjà traitées</h2>
                    <div class="reservations-grid">
                        <?php foreach ($pp_traitees as $pp_resa) {
                            poolparty_g4_carte_demande($pp_resa);
                        } ?>
                    </div>
                <?php endif; ?>
            </section>

        <?php endif; ?>
    </main>

<?php get_footer(); ?>
