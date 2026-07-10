/* =============================================================
   POOL PARTY - JEU DE DONNÉES DES BIENS
   -------------------------------------------------------------
   Source de données centrale du site (le futur contenu de la
   base). Aujourd'hui les cartes annonce sont écrites en dur dans
   categorie.html ; ce fichier rassemble les mêmes biens sous une
   forme structurée, façon table de base de données, pour :
     - servir de démonstration de l'architecture de données lors
       de la soutenance (à mettre en regard de schema-bdd.html) ;
     - pouvoir, à terme, générer les cartes en JavaScript plutôt
       que de les recopier à la main dans le HTML.

   Chaque tableau correspond à une TABLE du schéma entité-association.
   Les liens entre tables passent par les identifiants (id_hote,
   id_bien, id_categorie...), comme des clés étrangères.

   Aucune donnée réelle : projet étudiant fictif, aucune
   réservation possible.

   NB — Ce fichier n'est PAS chargé par le site : il n'est jamais
   mis en file d'attente (wp_enqueue_script) et main.js ne le lit
   pas (le front s'alimente via ppData et le DOM). Il sert de
   support à la soutenance / au schéma de base de données.
   Les chemins d'images ci-dessous sont donnés relativement au
   dossier du thème ; pour les exploiter un jour côté client, il
   faudrait les préfixer par l'URL du thème (sinon 404).
   ============================================================= */

(function () {
    'use strict';

    /* ---------------------------------------------------------
       TABLE CATEGORIE
       Les cinq familles d'espaces proposées par Pool Party.
       --------------------------------------------------------- */
    const categories = [
        { id: 'piscine', nom: 'Piscine', pluriel: 'Piscines' },
        { id: 'jacuzzi', nom: 'Jacuzzi', pluriel: 'Jacuzzis' },
        { id: 'spa',     nom: 'Spa',     pluriel: 'Spas' },
        { id: 'sauna',   nom: 'Sauna',   pluriel: 'Saunas' },
        { id: 'hammam',  nom: 'Hammam',  pluriel: 'Hammams' }
    ];

    /* ---------------------------------------------------------
       TABLE HOTE
       Un hôte est un utilisateur qui met un ou plusieurs biens
       en location. La note et le nombre d'avis sont agrégés à
       partir des avis reçus sur ses biens.
       Julien est l'hôte de démonstration : c'est lui qui reçoit
       la demande de réservation par e-mail (voir js/main.js).
       --------------------------------------------------------- */
    const hotes = [
        { id: 'h-julien', prenom: 'Julien',   membre_depuis: 2021, superhote: true,  photo: 'assets/images/temoignages/temoin-julien.jpg', bio: "Passionné de baignades entre amis, Julien loue sa piscine chauffée depuis trois ans." },
        { id: 'h-paula',  prenom: 'Paula',    membre_depuis: 2022, superhote: true,  photo: 'assets/images/temoignages/hote-paula.jpg',    bio: "Paula ouvre son jardin et son spa aux familles du quartier le week-end." },
        { id: 'h-yoann',  prenom: 'Yoann',    membre_depuis: 2023, superhote: false, photo: 'assets/images/temoignages/hote-yoann.jpg',    bio: "Yoann propose son jacuzzi pour les soirées détente en petit comité." },
        { id: 'h-tony',   prenom: 'Tony',     membre_depuis: 2020, superhote: true,  photo: 'assets/images/temoignages/hote-tony.jpg',     bio: "Tony gère un sauna et un hammam soigneusement entretenus toute l'année." },
        { id: 'h-sarah',  prenom: 'Sarah',    membre_depuis: 2024, superhote: false, photo: 'assets/images/temoignages/hote-fraise.jpg',   bio: "Sarah accueille avec le sourire dans sa piscine intérieure au coeur de la ville." }
    ];

    /* ---------------------------------------------------------
       TABLE BIEN (les annonces)
       - id_categorie  : clé étrangère vers categories
       - id_hote       : clé étrangère vers hotes
       - prix_heure    : tarif horaire en euros
       - prix_ancien   : tarif barré si le bien est en promotion (sinon null)
       - capacite_min / capacite_max : fourchette d'invités
       - note / nb_avis : agrégats des avis (table avis)
       - lat / lon     : coordonnées pour la carte OpenStreetMap
       - tag           : mise en avant éventuelle (top-vente, proche, promo, nouveau)
       - favori_id     : identifiant utilisé par la fonction Favoris (localStorage)
       Les douze premiers biens reprennent à l'identique les cartes
       présentes aujourd'hui dans categorie.html ; les suivants
       enrichissent le catalogue pour couvrir les cinq catégories.
       --------------------------------------------------------- */
    const biens = [
        {
            id: 'piscine-torcy-1', id_categorie: 'piscine', id_hote: 'h-julien',
            titre: 'Piscine intérieure chauffée', ville: 'Torcy', pays: 'France',
            distance_km: 4, prix_heure: 23, prix_ancien: null,
            capacite_min: 4, capacite_max: 5, note: 4.7, nb_avis: 5,
            lat: 48.850, lon: 2.653,
            image: 'assets/images/piscines/annonce-torcy.jpg',
            alt: 'Piscine intérieure chauffée avec fresque murale à Torcy',
            tag: 'top-vente', favori_id: null
        },
        {
            id: 'piscine-chelles', id_categorie: 'piscine', id_hote: 'h-sarah',
            titre: 'Bassin en pierre de caractère', ville: 'Chelles', pays: 'France',
            distance_km: 3, prix_heure: 23, prix_ancien: null,
            capacite_min: 6, capacite_max: 7, note: 4.9, nb_avis: 12,
            lat: 48.883, lon: 2.590,
            image: 'assets/images/piscines/annonce-chelles.jpg',
            alt: "Bassin en pierre dans la cour d'une maison de caractère à Chelles",
            tag: 'proche', favori_id: null
        },
        {
            id: 'jacuzzi-croissy', id_categorie: 'jacuzzi', id_hote: 'h-yoann',
            titre: 'Jacuzzi sur terrasse bois', ville: 'Croissy-Beaubourg', pays: 'France',
            distance_km: 5, prix_heure: 22, prix_ancien: null,
            capacite_min: 7, capacite_max: 7, note: 4.7, nb_avis: 9,
            lat: 48.827, lon: 2.672,
            image: 'assets/images/piscines/annonce-croissy.jpg',
            alt: 'Jacuzzi gonflable sur une terrasse en bois à Croissy-Beaubourg',
            tag: null, favori_id: null
        },
        {
            id: 'spa-pantin', id_categorie: 'spa', id_hote: 'h-paula',
            titre: 'Spa privatif baigné de lumière', ville: 'Pantin', pays: 'France',
            distance_km: 5, prix_heure: 23, prix_ancien: null,
            capacite_min: 8, capacite_max: 8, note: 4.7, nb_avis: 5,
            lat: 48.894, lon: 2.409,
            image: 'assets/images/cta/sauna.jpg',
            alt: 'Spa privatif en bois clair baigné de lumière à Pantin',
            tag: null, favori_id: 'spa-pantin'
        },
        {
            id: 'piscine-champs', id_categorie: 'piscine', id_hote: 'h-julien',
            titre: 'Piscine avec vue sur la vallée', ville: 'Champs-sur-Marne', pays: 'France',
            distance_km: 11, prix_heure: 20, prix_ancien: null,
            capacite_min: 5, capacite_max: 6, note: 4.6, nb_avis: 3,
            lat: 48.853, lon: 2.603,
            image: 'assets/images/piscines/annonce-champs.jpg',
            alt: 'Piscine avec vue dégagée sur la vallée à Champs-sur-Marne',
            tag: 'top-vente', favori_id: null
        },
        {
            id: 'piscine-torcy-2', id_categorie: 'piscine', id_hote: 'h-sarah',
            titre: 'Piscine ombragée par les arbres', ville: 'Torcy', pays: 'France',
            distance_km: 7, prix_heure: 21, prix_ancien: 25,
            capacite_min: 7, capacite_max: 8, note: 4.7, nb_avis: 5,
            lat: 48.858, lon: 2.665,
            image: 'assets/images/categories/categorie-3.jpg',
            alt: 'Piscine ombragée par de grands arbres à Torcy',
            tag: 'promo', favori_id: null
        },
        {
            id: 'jacuzzi-bussy', id_categorie: 'jacuzzi', id_hote: 'h-yoann',
            titre: 'Jacuzzi encastré devant mur en pierre', ville: 'Bussy-Saint-Georges', pays: 'France',
            distance_km: 8, prix_heure: 24, prix_ancien: null,
            capacite_min: 4, capacite_max: 4, note: 4.7, nb_avis: 5,
            lat: 48.842, lon: 2.712,
            image: 'assets/images/piscines/annonce-noisy.jpg',
            alt: 'Jacuzzi encastré dans une terrasse en bois devant un mur en pierre à Bussy-Saint-Georges',
            tag: null, favori_id: 'jacuzzi-bussy'
        },
        {
            id: 'piscine-saint-thibault', id_categorie: 'piscine', id_hote: 'h-paula',
            titre: 'Piscine de villa contemporaine', ville: 'Saint-Thibault-des-Vignes', pays: 'France',
            distance_km: 19, prix_heure: 21, prix_ancien: null,
            capacite_min: 10, capacite_max: 10, note: 4.7, nb_avis: 5,
            lat: 48.870, lon: 2.680,
            image: 'assets/images/piscines/annonce-pantin.jpg',
            alt: "Piscine d'une villa contemporaine entourée de verdure à Saint-Thibault-des-Vignes",
            tag: null, favori_id: 'piscine-saint-thibault'
        },
        {
            id: 'piscine-pontault', id_categorie: 'piscine', id_hote: 'h-julien',
            titre: 'Bassin turquoise en jardin arboré', ville: 'Pontault-Combault', pays: 'France',
            distance_km: 16, prix_heure: 23, prix_ancien: null,
            capacite_min: 6, capacite_max: 6, note: 4.7, nb_avis: 5,
            lat: 48.786, lon: 2.605,
            image: 'assets/images/categories/categorie-2.jpg',
            alt: 'Bassin turquoise au coeur d\'un jardin arboré à Pontault-Combault',
            tag: 'nouveau', favori_id: null
        },
        {
            id: 'piscine-joinville', id_categorie: 'piscine', id_hote: 'h-tony',
            titre: 'Bassin dans un patio verdoyant', ville: 'Joinville-le-Pont', pays: 'France',
            distance_km: 11, prix_heure: 23, prix_ancien: null,
            capacite_min: 12, capacite_max: 12, note: 4.7, nb_avis: 5,
            lat: 48.821, lon: 2.473,
            image: 'assets/images/piscines/annonce-lagny.jpg',
            alt: "Bassin dans un patio verdoyant vu depuis l'étage à Joinville-le-Pont",
            tag: null, favori_id: null
        },
        {
            id: 'piscine-paris', id_categorie: 'piscine', id_hote: 'h-sarah',
            titre: 'Couloir de nage au crépuscule', ville: 'Paris', pays: 'France',
            distance_km: 15, prix_heure: 24, prix_ancien: null,
            capacite_min: 5, capacite_max: 5, note: 4.7, nb_avis: 5,
            lat: 48.860, lon: 2.412,
            image: 'assets/images/categories/categorie-4.jpg',
            alt: 'Couloir de nage au crépuscule à Paris',
            tag: null, favori_id: null
        },
        {
            id: 'piscine-chessy', id_categorie: 'piscine', id_hote: 'h-paula',
            titre: 'Piscine intérieure végétalisée', ville: 'Chessy', pays: 'France',
            distance_km: 15, prix_heure: 23, prix_ancien: null,
            capacite_min: 8, capacite_max: 8, note: 4.7, nb_avis: 5,
            lat: 48.885, lon: 2.765,
            image: 'assets/images/categories/categorie-5.jpg',
            alt: 'Piscine intérieure entourée de végétation luxuriante à Chessy',
            tag: null, favori_id: null
        },

        /* --- Biens ajoutés pour couvrir les cinq catégories --- */
        {
            id: 'sauna-vincennes', id_categorie: 'sauna', id_hote: 'h-tony',
            titre: 'Sauna finlandais en bois clair', ville: 'Vincennes', pays: 'France',
            distance_km: 9, prix_heure: 19, prix_ancien: null,
            capacite_min: 2, capacite_max: 4, note: 4.8, nb_avis: 7,
            lat: 48.847, lon: 2.437,
            image: 'assets/images/evenements/detente.jpg',
            alt: 'Sauna finlandais en bois clair avec banquettes à Vincennes',
            tag: 'nouveau', favori_id: null
        },
        {
            id: 'hammam-montreuil', id_categorie: 'hammam', id_hote: 'h-tony',
            titre: 'Hammam traditionnel en zellige', ville: 'Montreuil', pays: 'France',
            distance_km: 8, prix_heure: 21, prix_ancien: null,
            capacite_min: 2, capacite_max: 6, note: 4.6, nb_avis: 4,
            lat: 48.864, lon: 2.443,
            image: 'assets/images/evenements/famille.jpg',
            alt: 'Hammam traditionnel habillé de zellige à Montreuil',
            tag: null, favori_id: null
        },
        {
            id: 'spa-nogent', id_categorie: 'spa', id_hote: 'h-paula',
            titre: 'Spa de nage avec jets massants', ville: 'Nogent-sur-Marne', pays: 'France',
            distance_km: 12, prix_heure: 26, prix_ancien: 30,
            capacite_min: 4, capacite_max: 6, note: 4.8, nb_avis: 8,
            lat: 48.837, lon: 2.482,
            image: 'assets/images/evenements/anniversaire.jpg',
            alt: 'Spa de nage avec jets massants et éclairage tamisé à Nogent-sur-Marne',
            tag: 'promo', favori_id: null
        },
        {
            id: 'jacuzzi-meaux', id_categorie: 'jacuzzi', id_hote: 'h-yoann',
            titre: 'Jacuzzi panoramique en rooftop', ville: 'Meaux', pays: 'France',
            distance_km: 24, prix_heure: 25, prix_ancien: null,
            capacite_min: 2, capacite_max: 5, note: 4.9, nb_avis: 11,
            lat: 48.960, lon: 2.878,
            image: 'assets/images/evenements/pool-party.jpg',
            alt: 'Jacuzzi panoramique installé sur un rooftop à Meaux',
            tag: 'top-vente', favori_id: null
        }
    ];

    /* ---------------------------------------------------------
       TABLE AVIS (échantillon)
       Un avis relie un bien (id_bien) à son auteur (prénom du
       locataire, ici en clair pour la démonstration). Ce sont ces
       lignes qui alimentent note et nb_avis de chaque bien.
       --------------------------------------------------------- */
    const avis = [
        { id: 'a-001', id_bien: 'piscine-chelles',   auteur: 'Lucie',    note: 5, date: '2026-06-12', texte: "Bassin superbe et accueil parfait, on reviendra." },
        { id: 'a-002', id_bien: 'piscine-chelles',   auteur: 'Thomas',   note: 5, date: '2026-06-02', texte: "Eau à bonne température, cadre calme, rien à redire." },
        { id: 'a-003', id_bien: 'jacuzzi-meaux',     auteur: 'Sandrine', note: 5, date: '2026-05-28', texte: "Vue imprenable au coucher du soleil, magique." },
        { id: 'a-004', id_bien: 'spa-nogent',        auteur: 'Thomas',   note: 5, date: '2026-05-20', texte: "Les jets massants valent le détour, très reposant." },
        { id: 'a-005', id_bien: 'piscine-torcy-1',   auteur: 'Lucie',    note: 4, date: '2026-05-15', texte: "Piscine chauffée agréable, fresque murale très jolie." }
    ];

    /* ---------------------------------------------------------
       TABLE RESERVATION (échantillon)
       Une réservation relie un bien (id_bien) et un locataire
       (id_locataire) à un créneau. Le statut suit le cycle du
       modèle « demande de réservation » : demande -> acceptee /
       refusee -> payee. Aucun paiement réel n'a lieu.
       --------------------------------------------------------- */
    const reservations = [
        { id: 'r-001', id_bien: 'piscine-torcy-1', id_locataire: 'Lucie',  date: '2026-07-19', creneau: '14:00-16:00', nb_invites: 4, montant_ttc: 46, statut: 'acceptee' },
        { id: 'r-002', id_bien: 'jacuzzi-meaux',   id_locataire: 'Thomas', date: '2026-07-25', creneau: '20:00-22:00', nb_invites: 3, montant_ttc: 50, statut: 'demande' }
    ];

    /* ---------------------------------------------------------
       ACCÈS PUBLIC + QUELQUES REQUÊTES UTILES
       Exposé sur window.PoolPartyData pour rester en JS vanilla
       sans build ni module. Les helpers imitent des requêtes SQL
       simples (SELECT ... WHERE ...).
       --------------------------------------------------------- */
    window.PoolPartyData = {
        categories: categories,
        hotes: hotes,
        biens: biens,
        avis: avis,
        reservations: reservations,

        getBienById: function (id) {
            return biens.find(function (b) { return b.id === id; }) || null;
        },
        getHoteById: function (id) {
            return hotes.find(function (h) { return h.id === id; }) || null;
        },
        getBiensParCategorie: function (idCategorie) {
            return biens.filter(function (b) { return b.id_categorie === idCategorie; });
        },
        getAvisParBien: function (idBien) {
            return avis.filter(function (a) { return a.id_bien === idBien; });
        }
    };
})();
