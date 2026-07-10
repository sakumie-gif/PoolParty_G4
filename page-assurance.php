<?php
/**
 * Gabarit de la page « Assurances » (contenu repris de assurance.html).
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 * Recommandations adaptées d'un mockup fourni par la cliente : les
 * bandeaux cyan/rose d'origine sont remplacés par la charte, les noms
 * d'assureurs sont en libellés sobres (pas de logos de marque).
 */
get_header();
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane -->
        <div class="assurance-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><span class="is-current" aria-current="page">Assurances</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="assurance-hero">
            <h1>Nos recommandations d'assurance</h1>
            <p class="assurance-hero__baseline">Protégez-vous et protégez vos biens en toute sérénité.</p>
            <p>Chez Pool Party, nous conseillons à chacun de souscrire une assurance adaptée avant de louer ou de mettre à disposition un espace aquatique. Que vous soyez propriétaire ou locataire, ces couvertures vous protègent en cas de dommage.</p>
        </section>

        <!-- BLOC 3 : Pour les propriétaires -->
        <section class="assurance-public" aria-labelledby="proprietaires-titre">
            <div class="assurance-public__entete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 21v-6a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v6"/></svg>
                <h2 id="proprietaires-titre">Pour les propriétaires</h2>
                <p>Assurez-vous que votre piscine, votre jacuzzi ou votre sauna est bien protégé contre les risques liés à l'accueil de locataires.</p>
            </div>

            <div class="assurance-grille">
                <article class="assurance-card">
                    <p class="assurance-card__marque" aria-hidden="true">AXA</p>
                    <h3 class="assurance-card__nom">AXA Assurance</h3>
                    <p class="assurance-card__desc">Une couverture complète des biens et une protection contre les dommages causés par les locataires.</p>
                    <p class="assurance-card__label">Protection recommandée</p>
                    <ul class="assurance-card__liste">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Assurance habitation étendue
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Protection contre les dommages
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Responsabilité civile propriétaire
                        </li>
                    </ul>
                    <a class="assurance-card__lien" href="https://www.axa.fr/" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                        Voir l'offre AXA (nouvel onglet)
                    </a>
                </article>

                <article class="assurance-card">
                    <p class="assurance-card__marque" aria-hidden="true">Allianz</p>
                    <h3 class="assurance-card__nom">Allianz France</h3>
                    <p class="assurance-card__desc">La recommandation pour les propriétaires qui souhaitent une protection spécifique aux piscines et jacuzzis.</p>
                    <p class="assurance-card__label">Protection recommandée</p>
                    <ul class="assurance-card__liste">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Assurance piscine spécialisée
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Couverture des équipements
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Protection juridique incluse
                        </li>
                    </ul>
                    <a class="assurance-card__lien" href="https://www.allianz.fr/" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                        Voir l'offre Allianz (nouvel onglet)
                    </a>
                </article>

                <article class="assurance-card">
                    <p class="assurance-card__marque" aria-hidden="true">Generali</p>
                    <h3 class="assurance-card__nom">Generali</h3>
                    <p class="assurance-card__desc">Recommandé pour sa flexibilité et ses garanties adaptées aux activités de location entre particuliers.</p>
                    <p class="assurance-card__label">Protection recommandée</p>
                    <ul class="assurance-card__liste">
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Assurance location saisonnière
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Protection contre les accidents
                        </li>
                        <li>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
                            Couverture des dommages matériels
                        </li>
                    </ul>
                    <a class="assurance-card__lien" href="https://www.generali.fr/" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                        Voir l'offre Generali (nouvel onglet)
                    </a>
                </article>
            </div>
        </section>

        <!-- BLOC 4 : Pour les locataires -->
        <section class="assurance-public assurance-public--locataires" aria-labelledby="locataires-titre">
            <div class="assurance-public__entete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <h2 id="locataires-titre">Pour les locataires</h2>
                <p>Protégez-vous contre les dommages que vous pourriez causer pendant votre séjour, et couvrez vos propres accidents.</p>
            </div>

            <div class="assurance-conseils">
                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/></svg>
                    <h3 class="card-protection__title">Responsabilité civile</h3>
                    <p class="card-protection__text">Elle couvre les dommages que vous pourriez causer aux biens du propriétaire. Elle est le plus souvent déjà incluse dans votre assurance habitation, au titre de la villégiature : vérifiez ce point auprès de votre assureur avant votre venue.</p>
                </article>

                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                    <h3 class="card-protection__title">Assurance personnelle</h3>
                    <p class="card-protection__text">Une garantie individuelle accident vous protège en cas de blessure pendant votre séjour. Utile si votre contrat habitation ne prévoit pas de couverture des accidents de la vie courante.</p>
                </article>

                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/></svg>
                    <h3 class="card-protection__title">Le réflexe utile</h3>
                    <p class="card-protection__text">Un seul appel à votre assureur suffit le plus souvent pour confirmer que votre location est couverte. Conservez la confirmation écrite : elle vous sera utile en cas de litige.</p>
                </article>
            </div>
        </section>

        <!-- BLOC 5 : Information importante -->
        <section class="assurance-note" aria-labelledby="note-titre">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
            <div class="assurance-note__contenu">
                <h2 id="note-titre">Information importante</h2>
                <p>Ces recommandations sont fournies à titre informatif. Pool Party n'est pas un courtier en assurance et ne perçoit aucune commission. Nous vous invitons à comparer les offres et à choisir l'assurance qui correspond le mieux à vos besoins.</p>
            </div>
        </section>

        <!-- BLOC 6 : CTA final -->
        <section class="assurance-cta" aria-labelledby="assurance-cta-titre">
            <h2 id="assurance-cta-titre">Une question sur les garanties ?</h2>
            <p>Notre équipe vous oriente vers la bonne information avant votre réservation.</p>
            <div class="assurance-cta__actions">
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-secondary">Contacter le service client</a>
                <a href="<?php echo esc_url(home_url('/faq/')); ?>" class="btn btn-tertiary">Consulter la FAQ</a>
            </div>
        </section>
    </main>

<?php get_footer(); ?>
