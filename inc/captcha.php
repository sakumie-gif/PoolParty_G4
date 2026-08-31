<?php
/**
 * Captcha visible des formulaires publics.
 * -------------------------------------------------------------
 * Une question d'arithmétique écrite en toutes lettres, posée au
 * visiteur avant l'envoi d'un formulaire ouvert à tous (contact,
 * candidature partenaire). Elle complète le champ piège invisible
 * déjà en place, qui reste utile contre les robots basiques.
 *
 * Choix d'un captcha maison plutôt qu'un service extérieur : aucune
 * donnée du visiteur ne part chez un tiers, rien à configurer, et la
 * question reste lisible par un lecteur d'écran, contrairement aux
 * images de caractères déformés.
 *
 * La réponse attendue n'est jamais envoyée au navigateur : le
 * formulaire transporte seulement une signature calculée avec les
 * clés du site, que le serveur recalcule à la réception.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Validité d'une question, en secondes. Au-delà, la page est
// considérée comme trop ancienne et le formulaire est à renvoyer.
define('PP_CAPTCHA_DUREE', HOUR_IN_SECONDS);

/**
 * Nombres en toutes lettres : un robot qui cherche des chiffres dans
 * la page ne trouve rien à additionner.
 */
function poolparty_g4_captcha_mots() {
    return array(
        1 => 'un', 2 => 'deux', 3 => 'trois', 4 => 'quatre', 5 => 'cinq',
        6 => 'six', 7 => 'sept', 8 => 'huit', 9 => 'neuf',
    );
}

/**
 * Signature d'une réponse attendue, liée à l'horodatage de la page.
 */
function poolparty_g4_captcha_signature($reponse, $horodatage) {
    return wp_hash('pp_captcha|' . intval($reponse) . '|' . intval($horodatage));
}

/**
 * Affiche le champ du captcha : la question, le champ de saisie et
 * les deux champs cachés qui permettent la vérification.
 *
 * @param string $prefixe Préfixe des identifiants HTML (ex. « contact »).
 */
function poolparty_g4_captcha_champs($prefixe) {
    $mots = poolparty_g4_captcha_mots();
    $a    = wp_rand(1, 9);
    $b    = wp_rand(1, 9);

    $horodatage = time();
    $signature  = poolparty_g4_captcha_signature($a + $b, $horodatage);
    $id         = $prefixe . '-captcha';
    ?>
    <div class="form-field pp-captcha">
        <label class="form-field__label" for="<?php echo esc_attr($id); ?>">Question de sécurité : combien font <?php echo esc_html($mots[$a]); ?> plus <?php echo esc_html($mots[$b]); ?> ?</label>
        <input class="form-field__input" type="text" id="<?php echo esc_attr($id); ?>" name="pp_captcha"
               inputmode="numeric" autocomplete="off" required
               aria-describedby="<?php echo esc_attr($id); ?>-aide" placeholder="Votre réponse en chiffres">
        <p class="pp-captcha__aide" id="<?php echo esc_attr($id); ?>-aide">Cette question nous permet de vérifier que vous n'êtes pas un robot.</p>
        <input type="hidden" name="pp_captcha_ts" value="<?php echo esc_attr($horodatage); ?>">
        <input type="hidden" name="pp_captcha_sig" value="<?php echo esc_attr($signature); ?>">
    </div>
    <?php
}

/**
 * Vérifie la réponse reçue. Renvoie true seulement si la question
 * était bien celle servie par le site, qu'elle n'est pas périmée et
 * que le résultat est juste.
 */
function poolparty_g4_captcha_valide() {
    if (!isset($_POST['pp_captcha'], $_POST['pp_captcha_ts'], $_POST['pp_captcha_sig'])) {
        return false;
    }

    $saisie     = trim(sanitize_text_field(wp_unslash($_POST['pp_captcha'])));
    $horodatage = intval($_POST['pp_captcha_ts']);
    $signature  = sanitize_text_field(wp_unslash($_POST['pp_captcha_sig']));

    if ($saisie === '' || !ctype_digit($saisie)) {
        return false;
    }

    $ecart = time() - $horodatage;
    if ($horodatage <= 0 || $ecart < 0 || $ecart > PP_CAPTCHA_DUREE) {
        return false;
    }

    return hash_equals(poolparty_g4_captcha_signature($saisie, $horodatage), $signature);
}
