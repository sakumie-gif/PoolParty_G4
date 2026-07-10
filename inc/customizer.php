<?php
/**
 * Personnalisateur du thème (Apparence > Personnaliser).
 * Rend éditables depuis le back-office les textes clés de la page
 * d'accueil (front-page.php), SANS toucher au design ni à la maquette.
 * Chaque réglage a pour valeur par défaut le texte d'origine : tant que
 * le client ne modifie rien, l'accueil reste strictement identique.
 * Les contenus dynamiques (biens, catégories) restent gérés depuis le
 * menu Biens et la base de données.
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Inventaire des textes éditables de l'accueil.
 * Clé courte => array(libellé affiché dans le Personnalisateur, texte
 * d'origine de la maquette servant de valeur par défaut).
 * Sert à la fois à générer les champs et à fournir les défauts au gabarit.
 */
function pp_accueil_textes_defauts() {
    return array(
        'hero_title'    => array('Titre principal (héros)', "L'expérience piscine, jacuzzi ou espace bien-être, rien que pour vous"),
        'hero_subtitle' => array('Sous-titre (héros)', 'À louer entre particuliers, partout en France'),
        'coup_titre'    => array('Titre « Coups de cœur »', 'Coups de coeur du moment'),
        'coup_sub'      => array('Sous-titre « Coups de cœur »', 'Petits prix, grand impact.'),
    );
}

/**
 * Valeur d'un texte de l'accueil : celle saisie dans le Personnalisateur,
 * ou le texte d'origine de la maquette si le client n'a rien changé.
 * À utiliser dans front-page.php : echo esc_html(pp_accueil_texte('hero_title'))
 */
function pp_accueil_texte($cle) {
    $defauts = pp_accueil_textes_defauts();
    $defaut  = isset($defauts[$cle]) ? $defauts[$cle][1] : '';
    return get_theme_mod('pp_accueil_' . $cle, $defaut);
}

/**
 * Déclare la section « Page d'accueil » du Personnalisateur et ses champs.
 */
function poolparty_g4_customize_register($wp_customize) {
    $wp_customize->add_section('pp_accueil', array(
        'title'       => 'Page d’accueil PoolParty',
        'description' => 'Modifiez les textes de la page d’accueil. Les biens et catégories se gèrent depuis le menu Biens.',
        'priority'    => 30,
    ));

    foreach (pp_accueil_textes_defauts() as $cle => $infos) {
        list($libelle, $defaut) = $infos;
        $id = 'pp_accueil_' . $cle;

        $wp_customize->add_setting($id, array(
            'default'           => $defaut,
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ));

        $wp_customize->add_control($id, array(
            'label'   => $libelle,
            'section' => 'pp_accueil',
            'type'    => 'text',
        ));
    }
}
add_action('customize_register', 'poolparty_g4_customize_register');
