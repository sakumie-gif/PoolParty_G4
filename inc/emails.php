<?php
/**
 * E-mails transactionnels du thème PoolParty_G4.
 *
 * Envois déclenchés par une action de l'utilisateur, via wp_mail (donc
 * l'envoi effectif dépend de la configuration SMTP du site : sur o2switch
 * WP Mail SMTP prend le relais ; en local sans SMTP, wp_mail renvoie
 * simplement false sans erreur). Tous les messages partagent le même
 * gabarit HTML aux couleurs de la charte.
 *
 * Événements couverts :
 *   - Contact       : accusé au visiteur + notification à l'équipe
 *   - Partenaire    : accusé au candidat + notification à l'équipe
 *   - Dépôt de bien : à l'hôte « en attente de validation » + à l'admin,
 *                     puis « annonce en ligne » quand l'admin la publie
 *   - Réservation   : accusé au locataire (via une requête AJAX de main.js ;
 *                     l'e-mail à l'hôte, lui, part déjà côté client)
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Expéditeur commun à tous les e-mails du site : « Pool Party
 * <contact@poolparty.fr> ». Renvoyé sous forme d'en-têtes wp_mail.
 */
function poolparty_g4_email_from() {
    $nom = get_bloginfo('name');
    if (!$nom) {
        $nom = 'Pool Party';
    }
    return $nom . ' <contact@poolparty.fr>';
}

/**
 * Gabarit HTML commun (styles en ligne, obligatoires pour les clients
 * mail). Bandeau corail, carte blanche, pied de page discret.
 *
 * @param string $titre Titre affiché dans le bandeau.
 * @param string $corps Contenu HTML déjà échappé/formaté.
 */
function poolparty_g4_email_gabarit($titre, $corps) {
    $accueil = esc_url(home_url('/'));
    $nom     = esc_html(get_bloginfo('name') ? get_bloginfo('name') : 'Pool Party');

    ob_start();
    ?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f3efe9;font-family:Arial,Helvetica,sans-serif;color:#2f2a26;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f3efe9;padding:24px 0;">
        <tr><td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06);">
                <tr>
                    <td style="background:#CA8171;padding:28px 32px;">
                        <a href="<?php echo $accueil; ?>" style="color:#ffffff;text-decoration:none;font-size:24px;font-weight:bold;letter-spacing:.5px;"><?php echo $nom; ?></a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px;">
                        <h1 style="margin:0 0 18px;font-size:21px;line-height:1.3;color:#2f2a26;"><?php echo esc_html($titre); ?></h1>
                        <div style="font-size:15px;line-height:1.6;color:#4a443e;"><?php echo $corps; ?></div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:22px 32px;background:#faf7f2;border-top:1px solid #ece5db;font-size:12px;line-height:1.6;color:#9b938a;">
                        <p style="margin:0 0 6px;"><?php echo $nom; ?> — location d'espaces aquatiques entre particuliers.</p>
                        <p style="margin:0;">Cet e-mail vous est envoyé dans le cadre d'un projet étudiant fictif ; aucune transaction réelle n'a lieu.</p>
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
    <?php
    return ob_get_clean();
}

/**
 * Envoie un e-mail HTML avec l'expéditeur et le gabarit du site.
 *
 * @param string $destinataire Adresse du destinataire.
 * @param string $sujet        Objet de l'e-mail.
 * @param string $titre        Titre affiché dans le bandeau.
 * @param string $corps        Corps HTML.
 * @param string $repondre_a   Adresse de réponse facultative (Reply-To).
 * @return bool  Résultat de wp_mail.
 */
function poolparty_g4_email_envoyer($destinataire, $sujet, $titre, $corps, $repondre_a = '') {
    if (!is_email($destinataire)) {
        return false;
    }
    // En développement (hôte se terminant par .local), on n'envoie rien :
    // aucun SMTP n'y est configuré et cela éviterait de polluer le journal
    // d'erreurs. Même logique que le tag Google Analytics. L'envoi réel a
    // lieu en production (WP Mail SMTP sur o2switch).
    $hote = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    if ($hote && substr($hote, -6) === '.local') {
        return true;
    }
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . poolparty_g4_email_from(),
    );
    if ($repondre_a && is_email($repondre_a)) {
        $headers[] = 'Reply-To: ' . $repondre_a;
    }
    return wp_mail($destinataire, $sujet, poolparty_g4_email_gabarit($titre, $corps), $headers);
}

/* ------------------------------------------------------------------ *
 *  CONTACT
 * ------------------------------------------------------------------ */

/**
 * Envois liés au formulaire de contact : appelé par page-contact.php quand
 * la soumission est valide (POST + jeton + anti-spam). Lit les champs dans
 * $_POST (déjà filtrés par le jeton), assainit, puis envoie l'accusé au
 * visiteur et la notification à l'équipe.
 */
function poolparty_g4_email_contact_envoi() {
    $email   = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $prenom  = isset($_POST['prenom']) ? sanitize_text_field(wp_unslash($_POST['prenom'])) : '';
    $nom     = isset($_POST['nom']) ? sanitize_text_field(wp_unslash($_POST['nom'])) : '';
    $objet   = isset($_POST['objet']) ? sanitize_text_field(wp_unslash($_POST['objet'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

    // Accusé de réception au visiteur.
    if (is_email($email)) {
        $corps  = '<p>Bonjour ' . esc_html($prenom) . ',</p>';
        $corps .= '<p>Nous avons bien reçu votre message et nous vous en remercions. Notre équipe vous répond sous 24&nbsp;heures, du lundi au samedi.</p>';
        if ($objet) {
            $corps .= '<p style="margin:18px 0;padding:14px 16px;background:#faf7f2;border-radius:10px;"><strong>Objet de votre demande&nbsp;:</strong><br>' . esc_html($objet) . '</p>';
        }
        $corps .= '<p>En attendant, vous trouverez peut-être votre réponse dans notre <a href="' . esc_url(home_url('/faq/')) . '" style="color:#CA8171;">foire aux questions</a>.</p>';
        $corps .= '<p>À très vite,<br>L\'équipe Pool Party</p>';
        poolparty_g4_email_envoyer($email, 'Nous avons bien reçu votre message', 'Merci pour votre message !', $corps);
    }

    // Notification interne à l'équipe.
    $admin = get_option('admin_email');
    if ($admin) {
        $corps  = '<p>Nouveau message reçu via le formulaire de contact&nbsp;:</p>';
        $corps .= '<p style="margin:16px 0;padding:14px 16px;background:#faf7f2;border-radius:10px;">';
        $corps .= '<strong>Nom&nbsp;:</strong> ' . esc_html(trim($prenom . ' ' . $nom)) . '<br>';
        $corps .= '<strong>Email&nbsp;:</strong> ' . esc_html($email) . '<br>';
        if ($objet) {
            $corps .= '<strong>Objet&nbsp;:</strong> ' . esc_html($objet) . '<br>';
        }
        $corps .= '<strong>Message&nbsp;:</strong><br>' . nl2br(esc_html($message));
        $corps .= '</p>';
        poolparty_g4_email_envoyer($admin, 'Nouveau message de contact', 'Nouveau message de contact', $corps, $email);
    }
}

/* ------------------------------------------------------------------ *
 *  PARTENAIRE
 * ------------------------------------------------------------------ */

/**
 * Envois liés au formulaire « Devenir partenaire » : appelé par
 * page-devenir-partenaire.php quand la soumission est valide.
 */
function poolparty_g4_email_partenaire_envoi() {
    $email   = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $contact = isset($_POST['contact']) ? sanitize_text_field(wp_unslash($_POST['contact'])) : '';
    $societe = isset($_POST['societe']) ? sanitize_text_field(wp_unslash($_POST['societe'])) : '';
    $type    = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

    // Accusé de réception au candidat.
    if (is_email($email)) {
        $corps  = '<p>Bonjour ' . esc_html($contact) . ',</p>';
        $corps .= '<p>Merci pour votre candidature au réseau de partenaires Pool&nbsp;Party. Nous avons bien reçu votre proposition';
        $corps .= $societe ? ' pour <strong>' . esc_html($societe) . '</strong>' : '';
        $corps .= '.</p>';
        $corps .= '<p>Votre interlocuteur dédié étudie votre dossier et vous recontacte sous <strong>48&nbsp;heures ouvrées</strong> pour définir ensemble les contours de la collaboration.</p>';
        $corps .= '<p>À très bientôt,<br>L\'équipe Partenariats Pool Party</p>';
        poolparty_g4_email_envoyer($email, 'Votre candidature partenaire est bien reçue', 'Merci pour votre candidature !', $corps);
    }

    // Notification interne à l'équipe.
    $admin = get_option('admin_email');
    if ($admin) {
        $corps  = '<p>Nouvelle candidature reçue via « Devenir partenaire »&nbsp;:</p>';
        $corps .= '<p style="margin:16px 0;padding:14px 16px;background:#faf7f2;border-radius:10px;">';
        $corps .= '<strong>Entreprise&nbsp;:</strong> ' . esc_html($societe) . '<br>';
        $corps .= '<strong>Contact&nbsp;:</strong> ' . esc_html($contact) . '<br>';
        $corps .= '<strong>Email&nbsp;:</strong> ' . esc_html($email) . '<br>';
        if ($type) {
            $corps .= '<strong>Activité&nbsp;:</strong> ' . esc_html($type) . '<br>';
        }
        $corps .= '<strong>Projet&nbsp;:</strong><br>' . nl2br(esc_html($message));
        $corps .= '</p>';
        poolparty_g4_email_envoyer($admin, 'Nouvelle candidature partenaire', 'Nouvelle candidature partenaire', $corps, $email);
    }
}

/* ------------------------------------------------------------------ *
 *  DÉPÔT DE BIEN (cycle de vie de l'annonce)
 * ------------------------------------------------------------------ */

/**
 * E-mails du cycle de vie d'une annonce « Bien », branchés sur le vrai
 * changement de statut WordPress :
 *   - passage EN ATTENTE (pending) : un hôte a soumis une annonce
 *       → e-mail « en attente de validation » à l'hôte + « à valider » à l'admin
 *   - passage EN LIGNE depuis EN ATTENTE (pending → publish) : l'admin valide
 *       → e-mail « votre annonce est en ligne » à l'hôte
 *
 * Le passage direct « nouveau → publié » (import automatique des 16 biens
 * de démonstration, création directe par l'admin) ne déclenche AUCUN
 * e-mail : on ne notifie que les transitions issues d'une soumission.
 */
function poolparty_g4_email_bien_transition($nouveau, $ancien, $post) {
    if (!$post || $post->post_type !== 'bien') {
        return;
    }
    if (wp_is_post_revision($post->ID) || wp_is_post_autosave($post->ID)) {
        return;
    }

    $auteur_email = get_the_author_meta('user_email', $post->post_author);
    $titre_bien   = get_the_title($post->ID);
    if (!$titre_bien) {
        $titre_bien = 'votre annonce';
    }

    // 1) Soumission : l'annonce passe en attente de modération.
    if ($nouveau === 'pending' && $ancien !== 'pending') {
        if (is_email($auteur_email)) {
            $corps  = '<p>Bonjour,</p>';
            $corps .= '<p>Votre annonce <strong>« ' . esc_html($titre_bien) . ' »</strong> a bien été enregistrée. Merci&nbsp;!</p>';
            $corps .= '<p>Notre équipe la vérifie sous <strong>24&nbsp;heures</strong>. Dès qu\'elle sera validée, elle apparaîtra dans les résultats de recherche et vous recevrez un e-mail de confirmation.</p>';
            $corps .= '<p>Vous pouvez d\'ici là compléter votre profil d\'hôte et ouvrir votre calendrier de disponibilités.</p>';
            $corps .= '<p>À très vite,<br>L\'équipe Pool Party</p>';
            poolparty_g4_email_envoyer($auteur_email, 'Votre annonce est en attente de validation', 'Annonce bien enregistrée !', $corps);
        }

        $admin = get_option('admin_email');
        if ($admin) {
            $lien   = admin_url('post.php?post=' . $post->ID . '&action=edit');
            $corps  = '<p>Une nouvelle annonce est en attente de validation&nbsp;:</p>';
            $corps .= '<p style="margin:16px 0;padding:14px 16px;background:#faf7f2;border-radius:10px;"><strong>' . esc_html($titre_bien) . '</strong></p>';
            $corps .= '<p><a href="' . esc_url($lien) . '" style="color:#CA8171;">Examiner et publier l\'annonce</a></p>';
            poolparty_g4_email_envoyer($admin, 'Une annonce à valider', 'Une annonce à valider', $corps);
        }
        return;
    }

    // 2) Validation : l'annonce en attente est publiée par l'admin.
    if ($nouveau === 'publish' && $ancien === 'pending') {
        if (is_email($auteur_email)) {
            $lien   = get_permalink($post->ID);
            $corps  = '<p>Bonjour,</p>';
            $corps .= '<p>Bonne nouvelle&nbsp;: votre annonce <strong>« ' . esc_html($titre_bien) . ' »</strong> a été validée par notre équipe et elle est désormais <strong>en ligne</strong>&nbsp;! 🎉</p>';
            $corps .= '<p><a href="' . esc_url($lien) . '" style="color:#CA8171;">Voir mon annonce en ligne</a></p>';
            $corps .= '<p>Les invités peuvent maintenant la découvrir et vous envoyer leurs demandes de réservation.</p>';
            $corps .= '<p>Bonnes locations,<br>L\'équipe Pool Party</p>';
            poolparty_g4_email_envoyer($auteur_email, 'Votre annonce est en ligne', 'Votre annonce est en ligne ! 🎉', $corps);
        }
    }
}
add_action('transition_post_status', 'poolparty_g4_email_bien_transition', 10, 3);

/* ------------------------------------------------------------------ *
 *  RÉSERVATION
 * ------------------------------------------------------------------ *
 * Les e-mails de réservation (accusé au locataire, notification à
 * l'hôte, puis confirmation / refus) vivent dans inc/reservations.php,
 * au plus près de l'enregistrement réel de la demande en base. Ils
 * réutilisent le gabarit et l'expéditeur définis ci-dessus, via
 * poolparty_g4_email_envoyer().
 */
