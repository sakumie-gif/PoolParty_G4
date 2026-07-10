<?php
/**
 * Gabarit de la page « Partenaires » (contenu repris de partenaires.html).
 * En-tête et pied de page fournis par header.php / footer.php ; les
 * styles propres à la page sont chargés dans functions.php.
 */
get_header();
?>

    <main id="contenu">
        <!-- BLOC 1 : Fil d'Ariane -->
        <div class="partenaires-intro">
            <nav aria-label="Fil d'Ariane">
                <ol class="breadcrumb">
                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            <svg class="breadcrumb__home-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-5.999a2 2 0 0 1 2.582 0l7 5.999A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                            Accueil
                        </a>
                    </li>
                    <li><span class="is-current" aria-current="page">Partenaires</span></li>
                </ol>
            </nav>
        </div>

        <!-- BLOC 2 : En-tête de page -->
        <section class="partenaires-hero">
            <h1>Des partenaires qui font grandir Pool Party</h1>
            <p>Assureurs, spécialistes du paiement, professionnels de l'entretien : ils sécurisent chaque baignade et accompagnent nos hôtes au quotidien.</p>
        </section>

        <!-- BLOC 3 : Grille des partenaires -->
        <section class="partenaires-liste" aria-labelledby="liste-titre">
            <h2 id="liste-titre">Ils nous accompagnent</h2>
            <p class="partenaires-liste__sub">Chaque partenaire est sélectionné pour la qualité de son service et sa proximité avec nos hôtes et nos locataires.</p>

            <ul class="partenaires-grid">
                <li class="card-partenaire">
                    <span class="card-partenaire__logo" aria-hidden="true">
                        <svg viewBox="0 0 200 64" focusable="false">
                            <path d="M28 6 L48 13 V31 C48 44 40 53 28 58 C16 53 8 44 8 31 V13 Z" fill="none" stroke="#305E63" stroke-width="3" stroke-linejoin="round"/>
                            <path d="M15 33 Q21.5 27 28 33 T41 33" fill="none" stroke="#CA8171" stroke-width="3" stroke-linecap="round"/>
                            <text x="58" y="36" font-family="'Cormorant Garamond', serif" font-weight="700" font-size="27" fill="#305E63">AquaSûr</text>
                            <text x="59" y="52" font-family="Manrope, sans-serif" font-weight="600" font-size="9" letter-spacing="3" fill="#4F7C82">ASSURANCES</text>
                        </svg>
                    </span>
                    <h3 class="card-partenaire__nom">AquaSûr Assurances</h3>
                    <p class="card-partenaire__domaine">Assurance et protection</p>
                    <p class="card-partenaire__texte">Couvre l'ensemble des réservations : responsabilité civile, dommages matériels et assistance sur place.</p>
                </li>
                <li class="card-partenaire">
                    <span class="card-partenaire__logo" aria-hidden="true">
                        <svg viewBox="0 0 205 64" focusable="false">
                            <path d="M28 7 C22 17 12 28 12 38 a16 16 0 0 0 32 0 C44 28 34 17 28 7 Z" fill="none" stroke="#4F7C82" stroke-width="3" stroke-linejoin="round"/>
                            <path d="M21 38 l5.5 5.5 L38 32" fill="none" stroke="#305E63" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            <text x="56" y="40" font-family="'Cormorant Garamond', serif" font-style="italic" font-weight="600" font-size="24" fill="#305E63">Garantie Bleue</text>
                        </svg>
                    </span>
                    <h3 class="card-partenaire__nom">Garantie Bleue</h3>
                    <p class="card-partenaire__domaine">Assurance et protection</p>
                    <p class="card-partenaire__texte">Gère les cautions et les litiges entre hôtes et locataires, avec une prise en charge des litiges sous 72 heures.</p>
                </li>
                <li class="card-partenaire">
                    <span class="card-partenaire__logo" aria-hidden="true">
                        <svg viewBox="0 0 200 64" focusable="false">
                            <circle cx="23" cy="32" r="14" fill="#305E63"/>
                            <circle cx="39" cy="32" r="14" fill="#CA8171" fill-opacity="0.85"/>
                            <text x="62" y="40" font-family="Manrope, sans-serif" font-weight="700" font-size="23" fill="#305E63">Pay<tspan fill="#4F7C82">Lagon</tspan></text>
                        </svg>
                    </span>
                    <h3 class="card-partenaire__nom">PayLagon</h3>
                    <p class="card-partenaire__domaine">Paiement sécurisé</p>
                    <p class="card-partenaire__texte">Accompagne les versements aux hôtes en complément de Stripe : virements chiffrés, envoyés sous 24 heures après la location.</p>
                </li>
                <li class="card-partenaire">
                    <span class="card-partenaire__logo" aria-hidden="true">
                        <svg viewBox="0 0 200 64" focusable="false">
                            <path d="M28 8 L46 18.5 V39.5 L28 50 L10 39.5 V18.5 Z" fill="none" stroke="#305E63" stroke-width="3" stroke-linejoin="round"/>
                            <path d="M28 19 C25 24 20 29.5 20 34 a8 8 0 0 0 16 0 C36 29.5 31 24 28 19 Z" fill="#8EA8A3"/>
                            <text x="56" y="35" font-family="Inter, sans-serif" font-weight="700" font-size="21" fill="#305E63">HydroNet</text>
                            <text x="57" y="51" font-family="Manrope, sans-serif" font-weight="600" font-size="9" letter-spacing="3.5" fill="#4F7C82">SERVICES</text>
                        </svg>
                    </span>
                    <h3 class="card-partenaire__nom">HydroNet Services</h3>
                    <p class="card-partenaire__domaine">Entretien</p>
                    <p class="card-partenaire__texte">Réseau de piscinistes franciliens : analyse de l'eau, nettoyage et hivernage à tarif négocié pour nos hôtes.</p>
                </li>
                <li class="card-partenaire">
                    <span class="card-partenaire__logo" aria-hidden="true">
                        <svg viewBox="0 0 205 64" focusable="false">
                            <circle cx="28" cy="32" r="16" fill="none" stroke="#305E63" stroke-width="7" stroke-dasharray="6.3 6.26"/>
                            <circle cx="28" cy="32" r="7" fill="none" stroke="#CA8171" stroke-width="3"/>
                            <text x="58" y="35" font-family="Manrope, sans-serif" font-weight="800" font-size="21" fill="#305E63">AquaTech</text>
                            <text x="59" y="51" font-family="Manrope, sans-serif" font-weight="600" font-size="9" letter-spacing="2.5" fill="#4F7C82">ÉQUIPEMENTS</text>
                        </svg>
                    </span>
                    <h3 class="card-partenaire__nom">AquaTech Équipements</h3>
                    <p class="card-partenaire__domaine">Équipement</p>
                    <p class="card-partenaire__texte">Fournit alarmes, bâches et abris conformes aux normes de sécurité, livrés et posés partout en Île-de-France.</p>
                </li>
                <li class="card-partenaire">
                    <span class="card-partenaire__logo" aria-hidden="true">
                        <svg viewBox="0 0 200 64" focusable="false">
                            <circle cx="28" cy="32" r="17" fill="none" stroke="#CA8171" stroke-width="3"/>
                            <circle cx="28" cy="32" r="8" fill="none" stroke="#305E63" stroke-width="3"/>
                            <path d="M28 15 V24 M28 40 V49 M11 32 H20 M36 32 H45" stroke="#305E63" stroke-width="3"/>
                            <text x="56" y="36" font-family="'Cormorant Garamond', serif" font-weight="700" font-size="28" fill="#305E63">Splash</text>
                            <text x="58" y="52" font-family="Manrope, sans-serif" font-weight="600" font-size="9" letter-spacing="3" fill="#4F7C82">FORMATION</text>
                        </svg>
                    </span>
                    <h3 class="card-partenaire__nom">Splash Formation</h3>
                    <p class="card-partenaire__domaine">Sécurité aquatique</p>
                    <p class="card-partenaire__texte">Forme nos hôtes aux gestes de premiers secours et propose des surveillants de baignade diplômés à la demande.</p>
                </li>
                <li class="card-partenaire">
                    <span class="card-partenaire__logo" aria-hidden="true">
                        <svg viewBox="0 0 200 64" focusable="false">
                            <path d="M14 36 a14 14 0 0 1 28 0 Z" fill="#EDBDA7"/>
                            <path d="M28 12 v6 M12 20 l4.5 4.5 M44 20 l-4.5 4.5" stroke="#CA8171" stroke-width="3" stroke-linecap="round"/>
                            <path d="M10 44 Q17 39 24 44 T38 44 M14 52 Q21 47 28 52 T42 52" stroke="#4F7C82" stroke-width="3" fill="none" stroke-linecap="round"/>
                            <text x="56" y="35" font-family="'Cormorant Garamond', serif" font-style="italic" font-weight="600" font-size="26" fill="#305E63">Évasion</text>
                            <text x="57" y="51" font-family="Manrope, sans-serif" font-weight="600" font-size="8.5" letter-spacing="2.2" fill="#4F7C82">ÎLE-DE-FRANCE</text>
                        </svg>
                    </span>
                    <h3 class="card-partenaire__nom">Évasion Île-de-France</h3>
                    <p class="card-partenaire__domaine">Tourisme local</p>
                    <p class="card-partenaire__texte">Met en avant nos espaces auprès des visiteurs de la région et relaie les événements de la communauté.</p>
                </li>
                <li class="card-partenaire">
                    <span class="card-partenaire__logo" aria-hidden="true">
                        <svg viewBox="0 0 195 64" focusable="false">
                            <circle cx="22" cy="42" r="11" fill="#4F7C82"/>
                            <circle cx="38" cy="26" r="7" fill="#8EA8A3"/>
                            <circle cx="46" cy="12" r="4" fill="#EDBDA7"/>
                            <text x="58" y="39" font-family="'Cormorant Garamond', serif" font-weight="700" font-size="30" fill="#305E63">bulle</text>
                            <text x="59" y="53" font-family="Manrope, sans-serif" font-weight="600" font-size="9" letter-spacing="3.5" fill="#4F7C82">MAGAZINE</text>
                        </svg>
                    </span>
                    <h3 class="card-partenaire__nom">Bulle Magazine</h3>
                    <p class="card-partenaire__domaine">Média bien-être</p>
                    <p class="card-partenaire__texte">Raconte chaque mois les plus beaux espaces de la plateforme et les coulisses de la vie d'hôte.</p>
                </li>
            </ul>
        </section>

        <!-- BLOC 4 : Chiffres clés du réseau -->
        <section class="partenaires-chiffres" aria-labelledby="chiffres-titre">
            <h2 class="sr-only" id="chiffres-titre">Le réseau de partenaires en chiffres</h2>
            <ul class="partenaires-chiffres__liste">
                <li class="partenaires-chiffre">
                    <span class="partenaires-chiffre__valeur">8</span>
                    <span class="partenaires-chiffre__label">partenaires de confiance à nos côtés</span>
                </li>
                <li class="partenaires-chiffre">
                    <span class="partenaires-chiffre__valeur">100 %</span>
                    <span class="partenaires-chiffre__label">des réservations couvertes par une assurance</span>
                </li>
                <li class="partenaires-chiffre">
                    <span class="partenaires-chiffre__valeur">350</span>
                    <span class="partenaires-chiffre__label">espaces équipés et entretenus avec eux</span>
                </li>
                <li class="partenaires-chiffre">
                    <span class="partenaires-chiffre__valeur">98 %</span>
                    <span class="partenaires-chiffre__label">d'hôtes satisfaits des interventions</span>
                </li>
            </ul>
        </section>

        <!-- BLOC 5 : Pourquoi devenir partenaire -->
        <section class="partenaires-pourquoi" aria-labelledby="pourquoi-titre">
            <h2 id="pourquoi-titre">Pourquoi rejoindre le réseau ?</h2>
            <p class="partenaires-pourquoi__sub">Un partenariat Pool Party, c'est un accès direct à une communauté d'hôtes et de locataires passionnés d'espaces aquatiques.</p>

            <div class="partenaires-pourquoi__grid">
                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <h3 class="card-protection__title">Une audience engagée</h3>
                    <p class="card-protection__text">Des milliers d'hôtes et de locataires actifs chaque mois, tous concernés par vos services.</p>
                </article>

                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2v4"/><path d="m16.2 7.8 2.9-2.9"/><path d="M18 12h4"/><path d="m16.2 16.2 2.9 2.9"/><path d="M12 18v4"/><path d="m4.9 19.1 2.9-2.9"/><path d="M2 12h4"/><path d="m4.9 4.9 2.9 2.9"/></svg>
                    <h3 class="card-protection__title">Des projets concrets</h3>
                    <p class="card-protection__text">Offres réservées à la communauté, interventions terrain, contenus communs : chaque partenariat se construit sur mesure.</p>
                </article>

                <article class="card-protection">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5Zm0 0a9 9 0 0 1 18 0m0 0v5a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3Z"/><path d="M21 16v2a4 4 0 0 1-4 4h-5"/></svg>
                    <h3 class="card-protection__title">Une équipe à vos côtés</h3>
                    <p class="card-protection__text">Un interlocuteur unique suit votre collaboration, du premier échange au bilan de chaque saison.</p>
                </article>
            </div>
        </section>

        <!-- BLOC 6 : Témoignage d'un partenaire -->
        <section class="partenaires-temoignage" aria-labelledby="temoignage-titre">
            <h2 class="sr-only" id="temoignage-titre">Le mot d'un partenaire</h2>
            <figure class="partenaires-temoignage__carte">
                <blockquote class="partenaires-temoignage__citation">
                    <p>"Avec Pool Party, nous touchons des propriétaires que nous n'aurions jamais rencontrés seuls. Les demandes arrivent qualifiées et l'équipe suit chaque dossier avec nous."</p>
                </blockquote>
                <figcaption class="partenaires-temoignage__auteur">
                    <span class="card-avis__avatar" aria-hidden="true">C</span>
                    <div>
                        <p class="partenaires-temoignage__nom">Claire V.</p>
                        <p class="partenaires-temoignage__role">Directrice des partenariats, AquaSûr Assurances</p>
                    </div>
                </figcaption>
            </figure>
        </section>

        <!-- BLOC 7 : Renvoi vers la candidature partenaire (parcours détaillé sur sa page dédiée) -->
        <section class="partenaires-etapes" id="devenir-partenaire" aria-labelledby="etapes-titre">
            <h2 id="etapes-titre">Devenir partenaire</h2>
            <p class="partenaires-etapes__sub">De la prise de contact au lancement, comptez moins d'un mois. Les étapes détaillées, les profils recherchés et le formulaire de candidature sont réunis sur une page dédiée.</p>
            <div class="partenaires-cta__actions">
                <a href="<?php echo esc_url(home_url('/devenir-partenaire/')); ?>" class="btn btn-secondary">Déposer ma candidature</a>
            </div>
        </section>

        <!-- BLOC 8 : CTA final -->
        <section class="partenaires-cta" aria-labelledby="partenaires-cta-titre">
            <h2 id="partenaires-cta-titre">Une idée de collaboration ?</h2>
            <p>Parlez-nous de votre activité, nous construirons le partenariat qui lui ressemble.</p>
            <div class="partenaires-cta__actions">
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="btn btn-secondary">Proposer un partenariat</a>
                <a href="<?php echo esc_url(home_url('/a-propos/')); ?>" class="btn btn-tertiary">Découvrir notre histoire</a>
            </div>
        </section>
    </main>

<?php get_footer(); ?>
