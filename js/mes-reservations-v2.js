/* =============================================================
   POOL PARTY - MES RÉSERVATIONS V2
   Page unique Hôte / Locataire : bascule de vue, onglets En
   attente / A venir / Passées / Avis, actions en AJAX (accepter,
   refuser, annuler, avis, réponses). Les données viennent de
   ppData (localisé par functions.php) : reservations (locataire),
   reservationsHote (hôte) et avis. Les cartes reprennent le
   markup .reservation-card du site ; le contact entre membres
   passe par la messagerie de la plateforme (jamais d'e-mail).
   ============================================================= */
(function () {
    'use strict';

    var grille = document.getElementById('rv2-grid');
    if (!grille || !window.ppData) {
        return; // visiteur non connecté (état rendu par PHP) ou autre page
    }

    /* ---- État ---- */
    var donnees = {
        locataire: Array.isArray(ppData.reservations) ? ppData.reservations : [],
        hote: Array.isArray(ppData.reservationsHote) ? ppData.reservationsHote : []
    };
    var avis = (ppData.avis && ppData.avis.locataire) ? ppData.avis : { locataire: [], hote: [] };
    var vue = 'locataire';
    var onglet = 'en-attente';
    var sousOnglet = 'espaces';
    var avisTri = 'recent';
    var avisFiltre = 'tous';
    var actionEnCours = null;
    var noteChoisie = 0;

    /* ---- Utilitaires ---- */
    function echapper(texte) {
        return String(texte == null ? '' : texte)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function lienSur(lien) {
        var repli = ppData.catalogueUrl || '#';
        lien = String(lien || '');
        if (!lien) { return repli; }
        var schema = /^([a-z][a-z0-9+.-]*):/i.exec(lien);
        if (schema && !/^https?$/i.test(schema[1])) { return repli; }
        return lien;
    }

    // Une réservation est passée quand sa date JJ/MM/AAAA est antérieure
    // à aujourd'hui ; date absente ou illisible = à venir.
    function estPassee(resa) {
        var m = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(resa.date || '');
        if (!m) { return false; }
        var jour = new Date(parseInt(m[3], 10), parseInt(m[2], 10) - 1, parseInt(m[1], 10));
        var aujourdHui = new Date();
        aujourdHui.setHours(0, 0, 0, 0);
        return jour < aujourdHui;
    }

    function appelAjax(action, params, nonce) {
        var corps = new URLSearchParams();
        corps.set('action', action);
        corps.set('nonce', nonce);
        Object.keys(params).forEach(function (cle) { corps.set(cle, params[cle]); });
        return fetch(ppData.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: corps.toString()
        }).then(function (reponse) { return reponse.json(); });
    }

    /* ---- Répartition par onglet ---- */
    function filtrer(liste) {
        return liste.filter(function (resa) {
            if (onglet === 'en-attente') { return resa.statut === 'en-attente'; }
            if (onglet === 'a-venir') { return resa.statut === 'acceptee' && !estPassee(resa); }
            if (resa.statut === 'acceptee') { return estPassee(resa); }
            return resa.statut === 'refusee' || resa.statut === 'annulee-hote'
                || (vue === 'hote' && resa.statut === 'annulee');
        });
    }

    /* ---- Libellés ---- */
    function messageCompteur(nombre) {
        var locataire = vue === 'locataire';
        if (nombre === 0) {
            if (onglet === 'en-attente') { return locataire ? 'Vous n’avez pas de réservation en attente' : 'Vous n’avez pas de demande en attente'; }
            if (onglet === 'a-venir') { return 'Vous n’avez pas de réservation à venir'; }
            return 'Vous n’avez pas de réservation passée';
        }
        if (onglet === 'en-attente') {
            if (locataire) { return nombre === 1 ? '1 réservation en attente de réponse' : nombre + ' réservations en attente de réponse'; }
            return nombre === 1 ? '1 demande en attente de votre réponse' : nombre + ' demandes en attente de votre réponse';
        }
        if (onglet === 'a-venir') { return nombre === 1 ? '1 réservation à venir' : nombre + ' réservations à venir'; }
        return nombre === 1 ? '1 réservation passée' : nombre + ' réservations passées';
    }

    function tagStatut(resa) {
        if (resa.statut === 'acceptee' && estPassee(resa)) {
            return '<span class="tag reservation-card__statut">Terminée</span>';
        }
        if (resa.statut === 'acceptee') {
            return '<span class="tag tag--succes reservation-card__statut">' + (vue === 'locataire' ? 'Confirmée par l’hôte' : 'Acceptée') + '</span>';
        }
        if (resa.statut === 'refusee') {
            return '<span class="tag reservation-card__statut">' + (vue === 'locataire' ? 'Non retenue' : 'Refusée') + '</span>';
        }
        if (resa.statut === 'annulee-hote') {
            return '<span class="tag reservation-card__statut">' + (vue === 'hote' ? 'Annulée par vous' : 'Annulée par l’hôte') + '</span>';
        }
        if (resa.statut === 'annulee') {
            return '<span class="tag reservation-card__statut">Annulée par le locataire</span>';
        }
        return '<span class="tag tag--top-vente reservation-card__statut">En attente de confirmation</span>';
    }

    /* ---- Carte de réservation (markup .reservation-card du site) ---- */
    function carte(resa) {
        var passee = onglet === 'passees';
        var lien = echapper(lienSur(resa.lien));
        var html = '<article class="reservation-card' + (passee ? ' reservation-card--passee' : '') + '" data-resa-id="' + echapper(resa.id) + '">';

        html += '<a class="reservation-card__media" href="' + lien + '">' +
                (resa.image ? '<img src="' + echapper(resa.image) + '" alt="' + echapper(resa.alt || '') + '" loading="lazy" decoding="async">' : '') +
                tagStatut(resa) +
            '</a>';

        html += '<div class="reservation-card__body">' +
                '<div class="reservation-card__head">' +
                    '<h3 class="reservation-card__title"><a href="' + lien + '">' + echapper(resa.titre) + '</a></h3>';
        var interlocuteur = vue === 'locataire' ? resa.hote : resa.locataire;
        if (interlocuteur) {
            /* Jamais de coordonnées personnelles : le contact passe par
               la messagerie de la plateforme */
            html += '<p class="reservation-card__hote">' +
                (vue === 'locataire' ? 'Proposé par ' : 'Demande de ') + echapper(interlocuteur) +
                ' · <a class="rv2-lien-message" href="' + echapper(ppData.messagesUrl || '#') + '">Envoyer un message</a></p>';
        }
        html += '</div>';

        html += '<dl class="reservation-card__infos">' +
                '<div class="reservation-card__info">' +
                    '<dt>Date et créneau</dt>' +
                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/></svg>' +
                    '<dd>' + echapper((resa.date || '') + (resa.creneau ? ' · ' + resa.creneau : '')) + '</dd>' +
                '</div>' +
                '<div class="reservation-card__info">' +
                    '<dt>Invités</dt>' +
                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>' +
                    '<dd>' + echapper(resa.invites) + '</dd>' +
                '</div>';
        if (vue === 'hote' && resa.message) {
            html += '<div class="reservation-card__info">' +
                    '<dt>Message</dt>' +
                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>' +
                    '<dd>' + echapper(resa.message) + '</dd>' +
                '</div>';
        }
        html += '</dl>';

        html += '<div class="reservation-card__foot">';
        if (resa.total) {
            html += '<p class="reservation-card__total">Total<strong>' + echapper(resa.total) + '</strong></p>';
        }
        html += '<div class="reservation-card__actions">';
        if (vue === 'hote' && resa.statut === 'en-attente') {
            html += '<button type="button" class="btn btn-primary btn-small" data-action="accepter">Accepter</button>' +
                    '<button type="button" class="btn btn-tertiary btn-small" data-action="refuser">Refuser</button>';
        } else if (vue === 'locataire' && resa.statut === 'en-attente') {
            html += '<button type="button" class="btn btn-tertiary btn-small" data-action="annuler">Annuler la demande</button>';
        } else if (vue === 'hote' && resa.statut === 'acceptee' && !estPassee(resa)) {
            html += '<button type="button" class="btn btn-tertiary btn-small" data-action="annuler-hote">Annuler la réservation</button>';
        } else {
            html += '<a class="btn btn-tertiary btn-small" href="' + lien + '">Voir l’annonce</a>';
        }
        html += '</div></div></div></article>';
        return html;
    }

    /* ---- Avis : tri, filtre, compteur, cartes ---- */
    function triAvis(liste) {
        return liste.slice().sort(function (a, b) {
            return avisTri === 'recent' ? (b.ts || 0) - (a.ts || 0) : (a.ts || 0) - (b.ts || 0);
        });
    }

    function optionsFiltreAvis() {
        if (vue === 'hote' && sousOnglet === 'espaces') {
            return [['tous', 'Tous les avis'], ['non-repondus', 'Non répondus'], ['repondus', 'Répondus']];
        }
        return [['tous', 'Tous'], ['a-evaluer', 'À évaluer'], ['evalues', 'Évalués']];
    }

    function appliquerFiltreAvis(item) {
        if (avisFiltre === 'non-repondus') { return !item.reponse; }
        if (avisFiltre === 'repondus') { return !!item.reponse; }
        if (avisFiltre === 'a-evaluer') { return !!item.aEvaluer; }
        if (avisFiltre === 'evalues') { return !item.aEvaluer; }
        return true;
    }

    function compteurAvis(liste) {
        var parties = [];
        if (vue === 'hote' && sousOnglet === 'espaces') {
            var n = liste.length;
            if (!n) { return ''; }
            if (avisFiltre === 'non-repondus') { return n === 1 ? '1 avis sans réponse' : n + ' avis sans réponse'; }
            if (avisFiltre === 'repondus') { return n === 1 ? '1 avis avec réponse' : n + ' avis avec réponse'; }
            return n === 1 ? '1 avis reçu' : n + ' avis reçus';
        }
        var aEvaluer = liste.filter(function (a) { return a.aEvaluer; }).length;
        var publies = liste.length - aEvaluer;
        var libelleAEvaluer = vue === 'hote' ? 'locataire' : 'venue';
        var libellePublie = vue === 'hote' ? 'avis laissé' : 'avis publié';
        if (aEvaluer) { parties.push(aEvaluer === 1 ? '1 ' + libelleAEvaluer + ' à évaluer' : aEvaluer + ' ' + libelleAEvaluer + 's à évaluer'); }
        if (publies) { parties.push(publies === 1 ? '1 ' + libellePublie : publies + ' ' + libellePublie + 's'); }
        return parties.join(' · ');
    }

    var ETOILE_SVG = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.6c.3 0 .58.17.71.45l2.45 4.96 5.48.8c.65.09.91.89.44 1.35l-3.96 3.86.93 5.45c.11.65-.57 1.14-1.15.84L12 17.74l-4.9 2.57c-.58.3-1.26-.19-1.15-.84l.93-5.45-3.96-3.86c-.47-.46-.21-1.26.44-1.35l5.48-.8 2.45-4.96A.79.79 0 0 1 12 2.6z"/></svg>';

    function etoilesHtml(note) {
        var html = '<span class="rv2-stars" role="img" aria-label="Note : ' + note + ' sur 5">';
        for (var i = 1; i <= 5; i++) {
            html += '<span class="rv2-stars__item' + (i <= note ? ' is-on' : '') + '">' + ETOILE_SVG + '</span>';
        }
        return html + '</span>';
    }

    function carteAvisLocataire(item) {
        var html = '<article class="rv2-avis-card" data-avis-id="' + echapper(item.id) + '">';
        html += '<div class="rv2-avis-card__head"><div>' +
            '<h3 class="rv2-avis-card__bien">' + echapper(item.locataire) + '</h3>' +
            '<p class="rv2-avis-card__meta">' +
            (item.aEvaluer ? 'Venue du ' : 'Votre avis du ') + echapper(item.date) +
            ' · ' + echapper(item.bien) + '</p>' +
            '</div>';
        if (item.aEvaluer) { html += '<span class="tag tag--top-vente">À évaluer</span>'; }
        html += '</div>';

        if (item.aEvaluer) {
            html += '<div class="rv2-avis-card__actions">' +
                '<button type="button" class="btn btn-primary btn-small" data-action="evaluer-locataire">Évaluer le locataire</button>' +
                '</div>';
        } else {
            html += etoilesHtml(item.note);
            if (item.texte) { html += '<p class="rv2-avis-card__texte">' + echapper(item.texte) + '</p>'; }
        }
        return html + '</article>';
    }

    function carteAvis(item) {
        if (vue === 'hote' && item.type === 'locataire') { return carteAvisLocataire(item); }
        var html = '<article class="rv2-avis-card" data-avis-id="' + echapper(item.id) + '">';

        html += '<div class="rv2-avis-card__head"><div>' +
            '<h3 class="rv2-avis-card__bien">' + echapper(item.bien) + '</h3>';
        if (vue === 'locataire') {
            html += '<p class="rv2-avis-card__meta">' +
                (item.aEvaluer ? 'Votre venue du ' : 'Votre avis du ') + echapper(item.date) +
                ' · chez ' + echapper(item.hote) + '</p>';
        } else {
            html += '<p class="rv2-avis-card__meta">Avis de ' + echapper(item.auteur) + ' · ' + echapper(item.date) + '</p>';
        }
        html += '</div>';
        if (item.aEvaluer) { html += '<span class="tag tag--top-vente">À évaluer</span>'; }
        html += '</div>';

        if (item.aEvaluer) {
            html += '<div class="rv2-avis-card__actions">' +
                '<button type="button" class="btn btn-primary btn-small" data-action="laisser-avis">Laisser un avis</button>' +
                '</div>';
            return html + '</article>';
        }

        html += etoilesHtml(item.note);
        if (item.texte) { html += '<p class="rv2-avis-card__texte">' + echapper(item.texte) + '</p>'; }

        if (item.reponse) {
            html += '<div class="rv2-avis-reponse">' +
                '<p class="rv2-avis-reponse__label">' + (vue === 'hote' ? 'Votre réponse' : 'Réponse de ' + echapper(item.hote)) + '</p>' +
                '<p>' + echapper(item.reponse) + '</p>' +
                '</div>';
        } else if (vue === 'hote') {
            html += '<div class="rv2-avis-card__actions">' +
                '<button type="button" class="btn btn-tertiary btn-small" data-action="repondre">Répondre</button>' +
                '</div>';
        }
        return html + '</article>';
    }

    /* ---- États vides illustrés ---- */
    function etatVideHtml() {
        var icone, titre, texte, action = '';
        var boutonExplorer = '<div class="reservations-etat__actions"><a href="' + echapper(ppData.catalogueUrl || '#') + '" class="btn btn-primary">Explorer les espaces</a></div>';
        if (onglet === 'avis') {
            icone = '<svg class="reservations-etat__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2.6 14.9 8.5l6.5.95-4.7 4.58 1.1 6.47L12 17.44 6.2 20.5l1.1-6.47L2.6 9.45l6.5-.95z"/></svg>';
            if (avisFiltre !== 'tous') {
                titre = 'Aucun résultat pour ce filtre';
                texte = avisFiltre === 'non-repondus'
                    ? 'Vous avez répondu à tous les avis reçus sur vos espaces.'
                    : 'Aucun avis ne correspond au filtre choisi.';
            } else if (vue === 'hote' && sousOnglet === 'locataires') {
                titre = 'Aucun locataire à évaluer';
                texte = 'Après chaque venue, vous pourrez évaluer votre locataire ici pour aider les autres hôtes.';
            } else if (vue === 'hote') {
                titre = 'Aucun avis reçu pour le moment';
                texte = 'Les avis laissés par les membres après leur venue s’afficheront ici, et vous pourrez y répondre.';
            } else {
                titre = 'Aucun avis pour le moment';
                texte = 'Après une venue, vous pourrez évaluer l’espace ici et lire la réponse de l’hôte.';
                action = boutonExplorer;
            }
        } else {
            icone = '<svg class="reservations-etat__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 10h18"/><path d="M12 14v3M10.5 15.5h3"/></svg>';
            if (onglet === 'a-venir') {
                titre = 'Aucune réservation à venir';
                texte = 'Réservez un espace : il apparaîtra ici avec sa date et son créneau.';
            } else {
                titre = 'Aucune réservation passée';
                texte = 'Vos venues terminées se rangeront ici.';
            }
            action = boutonExplorer;
        }
        return '<section class="reservations-etat reservations-etat--vide">' +
            icone + '<h2>' + titre + '</h2><p>' + texte + '</p>' + action + '</section>';
    }

    /* ---- Rendu ---- */
    var compteur = document.getElementById('rv2-count');
    var savoir = document.querySelector('.rv2-savoir');
    var savoirLocataire = document.getElementById('rv2-savoir-locataire');
    var savoirHote = document.getElementById('rv2-savoir-hote');
    var savoirAvenirLocataire = document.getElementById('rv2-savoir-avenir-locataire');
    var savoirAvenirHote = document.getElementById('rv2-savoir-avenir-hote');
    var panneau = document.getElementById('rv2-contenu');
    var soustabs = document.getElementById('rv2-avis-soustabs');
    var outils = document.getElementById('rv2-avis-outils');
    var filtreListe = document.getElementById('rv2-filtre-liste');
    var filtreBouton = document.getElementById('rv2-filtre-btn');

    function rendre() {
        outils.hidden = onglet !== 'avis';
        soustabs.hidden = !(onglet === 'avis' && vue === 'hote');

        if (onglet === 'avis') {
            var options = optionsFiltreAvis();
            if (!options.some(function (o) { return o[0] === avisFiltre; })) { avisFiltre = 'tous'; }
            filtreListe.innerHTML = options.map(function (o) {
                return '<li><button type="button" class="dropdown-item' + (avisFiltre === o[0] ? ' is-active' : '') + '" data-filtre-avis="' + o[0] + '">' + o[1] + '</button></li>';
            }).join('');
            filtreBouton.classList.toggle('is-active', avisFiltre !== 'tous');

            soustabs.querySelectorAll('.reservations-tab').forEach(function (bouton) {
                var actif = bouton.dataset.sousonglet === sousOnglet;
                bouton.classList.toggle('is-active', actif);
                bouton.setAttribute('aria-selected', actif ? 'true' : 'false');
            });

            var avisListe = avis[vue] || [];
            var base;
            if (vue === 'hote' && sousOnglet === 'espaces') {
                base = avisListe.filter(function (a) { return a.type === 'recu'; });
            } else if (vue === 'hote') {
                base = avisListe.filter(function (a) { return a.type === 'locataire'; });
            } else {
                base = avisListe.slice();
            }
            var filtres = base.filter(appliquerFiltreAvis);
            var affiches = (vue === 'hote' && sousOnglet === 'espaces')
                ? triAvis(filtres)
                : triAvis(filtres.filter(function (a) { return a.aEvaluer; }))
                    .concat(triAvis(filtres.filter(function (a) { return !a.aEvaluer; })));
            compteur.textContent = compteurAvis(filtres);
            grille.innerHTML = affiches.length ? affiches.map(carteAvis).join('') : etatVideHtml();
        } else {
            var visibles = filtrer(donnees[vue]);
            compteur.textContent = messageCompteur(visibles.length);
            grille.innerHTML = visibles.map(carte).join('');
            if (!visibles.length && onglet !== 'en-attente' && vue === 'locataire') {
                compteur.textContent = '';
                grille.innerHTML = etatVideHtml();
            }
        }

        /* Bon à savoir : En attente (24 h) et A venir (annulation 48 h) */
        savoir.hidden = onglet !== 'en-attente' && onglet !== 'a-venir';
        savoirLocataire.hidden = !(onglet === 'en-attente' && vue === 'locataire');
        savoirHote.hidden = !(onglet === 'en-attente' && vue === 'hote');
        savoirAvenirLocataire.hidden = !(onglet === 'a-venir' && vue === 'locataire');
        savoirAvenirHote.hidden = !(onglet === 'a-venir' && vue === 'hote');

        document.querySelectorAll('.rv2-toggle__btn').forEach(function (bouton) {
            var actif = bouton.dataset.role === vue;
            bouton.classList.toggle('is-active', actif);
            bouton.setAttribute('aria-pressed', actif ? 'true' : 'false');
        });
        document.querySelectorAll('.rv2-tab').forEach(function (bouton) {
            var actif = bouton.dataset.filtre === onglet;
            bouton.classList.toggle('is-active', actif);
            bouton.setAttribute('aria-selected', actif ? 'true' : 'false');
            if (actif) { panneau.setAttribute('aria-labelledby', bouton.id); }
        });
    }

    /* ---- Pop-up générique ---- */
    var popup = document.getElementById('rv2-popup');
    var popupTitre = document.getElementById('rv2-popup-titre');
    var popupTexte = document.getElementById('rv2-popup-texte');
    var popupGarder = document.getElementById('rv2-popup-garder');
    var popupConfirmer = document.getElementById('rv2-popup-confirmer');
    var popupChamp = document.getElementById('rv2-popup-champ');
    var popupEtoiles = document.getElementById('rv2-popup-etoiles');
    var popupErreur = document.getElementById('rv2-popup-erreur');

    var TEXTES_POPUP = {
        accepter: {
            titre: 'Accepter cette réservation ?',
            texte: 'Vous confirmez la venue de %QUI% le %DATE%. Le locataire sera prévenu et le créneau sera bloqué sur votre annonce.',
            garder: 'Revenir',
            confirmer: 'Accepter la réservation'
        },
        refuser: {
            titre: 'Refuser cette demande ?',
            texte: 'La demande de %QUI% pour le %DATE% sera refusée. Le locataire en sera informé, cette action est définitive.',
            garder: 'Revenir',
            confirmer: 'Refuser la demande'
        },
        annuler: {
            titre: 'Annuler cette demande ?',
            texte: 'Votre demande pour « %TITRE% » le %DATE% sera retirée. L’hôte n’y aura plus accès, cette action est définitive.',
            garder: 'Conserver',
            confirmer: 'Annuler la demande'
        },
        'annuler-hote': {
            titre: 'Annuler cette réservation ?',
            texte: 'Expliquez la raison de l’annulation à %QUI% : votre message lui sera transmis, la réservation du %DATE% sera annulée et il sera intégralement remboursé.',
            garder: 'Revenir',
            confirmer: 'Annuler la réservation'
        },
        repondre: {
            titre: 'Répondre à cet avis',
            texte: 'Votre réponse sera publiée sous l’avis de %QUI%, visible par tous les membres sur votre annonce.',
            garder: 'Annuler',
            confirmer: 'Publier la réponse'
        },
        'laisser-avis': {
            titre: 'Laisser un avis',
            texte: 'Comment s’est passée votre venue « %TITRE% » du %DATE% ? Votre note et votre commentaire seront publiés sur l’annonce.',
            garder: 'Annuler',
            confirmer: 'Publier l’avis'
        },
        'evaluer-locataire': {
            titre: 'Évaluer votre locataire',
            texte: 'Comment s’est passée la venue de %QUI% le %DATE% ? Votre note aide les autres hôtes de la plateforme.',
            garder: 'Annuler',
            confirmer: 'Publier l’avis'
        }
    };

    function majEtoiles() {
        popupEtoiles.querySelectorAll('button').forEach(function (bouton) {
            bouton.classList.toggle('is-on', Number(bouton.dataset.note) <= noteChoisie);
        });
    }

    function majConfirmer() {
        if (!actionEnCours) { return; }
        var action = actionEnCours.action;
        if (action === 'repondre' || action === 'annuler-hote') {
            popupConfirmer.disabled = popupChamp.value.trim() === '';
        } else if (action === 'laisser-avis' || action === 'evaluer-locataire') {
            popupConfirmer.disabled = noteChoisie === 0;
        } else {
            popupConfirmer.disabled = false;
        }
    }

    function ouvrirPopup(action, cible) {
        var modele = TEXTES_POPUP[action];
        var avecEtoiles = action === 'laisser-avis' || action === 'evaluer-locataire';
        var avecChamp = action === 'repondre' || action === 'annuler-hote' || avecEtoiles;
        actionEnCours = { action: action, cible: cible };
        popupTitre.textContent = modele.titre;
        popupTexte.textContent = modele.texte
            .replace('%QUI%', cible.locataire || cible.auteur || cible.hote || '')
            .replace('%DATE%', cible.date || '')
            .replace('%TITRE%', cible.titre || cible.bien || '');
        popupGarder.textContent = modele.garder;
        popupConfirmer.textContent = modele.confirmer;
        popupChamp.hidden = !avecChamp;
        popupChamp.value = '';
        popupChamp.placeholder = avecEtoiles ? 'Un mot sur cette venue (facultatif)'
            : (action === 'annuler-hote' ? 'Expliquez la raison de l’annulation' : 'Écrivez votre message');
        popupEtoiles.hidden = !avecEtoiles;
        popupErreur.hidden = true;
        noteChoisie = 0;
        majEtoiles();
        majConfirmer();
        popup.hidden = false;
        if (avecEtoiles) { popupEtoiles.querySelector('button').focus(); }
        else if (avecChamp) { popupChamp.focus(); }
        else { popupConfirmer.focus(); }
    }

    function fermerPopup() {
        popup.hidden = true;
        actionEnCours = null;
    }

    function erreurPopup(message) {
        popupErreur.textContent = message || 'Une erreur est survenue. Réessayez.';
        popupErreur.hidden = false;
        popupConfirmer.disabled = false;
    }

    /* ---- Confirmation : appels AJAX puis mise à jour locale ---- */
    function confirmerAction() {
        if (!actionEnCours) { return; }
        var action = actionEnCours.action;
        var cible = actionEnCours.cible;
        popupConfirmer.disabled = true;
        popupErreur.hidden = true;

        var requete;
        if (action === 'accepter' || action === 'refuser' || action === 'annuler') {
            requete = appelAjax('pp_maj_reservation', { resa_id: cible.id, statut: action }, ppData.reservationNonce)
                .then(function (retour) {
                    if (!retour || !retour.success) { throw retour; }
                    if (action === 'annuler') {
                        donnees.locataire = donnees.locataire.filter(function (r) { return r.id !== cible.id; });
                    } else {
                        cible.statut = retour.data.statut;
                    }
                });
        } else if (action === 'annuler-hote') {
            requete = appelAjax('pp_maj_reservation', { resa_id: cible.id, statut: 'annuler-hote', raison: popupChamp.value.trim() }, ppData.reservationNonce)
                .then(function (retour) {
                    if (!retour || !retour.success) { throw retour; }
                    cible.statut = 'annulee-hote';
                });
        } else if (action === 'laisser-avis') {
            requete = appelAjax('pp_creer_avis', { resa_id: cible.resaId, note: noteChoisie, texte: popupChamp.value.trim() }, ppData.avisNonce)
                .then(function (retour) {
                    if (!retour || !retour.success) { throw retour; }
                    cible.aEvaluer = false;
                    cible.note = noteChoisie;
                    cible.texte = popupChamp.value.trim();
                    cible.reponse = '';
                });
        } else if (action === 'repondre') {
            requete = appelAjax('pp_reponse_avis', { avis_id: cible.avisId, texte: popupChamp.value.trim() }, ppData.avisNonce)
                .then(function (retour) {
                    if (!retour || !retour.success) { throw retour; }
                    cible.reponse = popupChamp.value.trim();
                });
        } else if (action === 'evaluer-locataire') {
            requete = appelAjax('pp_avis_locataire', { resa_id: cible.resaId, note: noteChoisie, texte: popupChamp.value.trim() }, ppData.avisNonce)
                .then(function (retour) {
                    if (!retour || !retour.success) { throw retour; }
                    cible.aEvaluer = false;
                    cible.note = noteChoisie;
                    cible.texte = popupChamp.value.trim();
                });
        } else {
            return;
        }

        requete.then(function () {
            fermerPopup();
            rendre();
        }).catch(function (retour) {
            erreurPopup(retour && retour.data && retour.data.message ? retour.data.message : '');
        });
    }

    /* ---- Écouteurs ---- */
    document.querySelectorAll('.rv2-toggle__btn').forEach(function (bouton) {
        bouton.addEventListener('click', function () {
            if (vue === bouton.dataset.role) { return; }
            vue = bouton.dataset.role;
            sousOnglet = 'espaces';
            avisFiltre = 'tous';
            rendre();
        });
    });

    document.querySelectorAll('.rv2-tab').forEach(function (bouton) {
        bouton.addEventListener('click', function () {
            if (onglet === bouton.dataset.filtre) { return; }
            onglet = bouton.dataset.filtre;
            sousOnglet = 'espaces';
            avisFiltre = 'tous';
            rendre();
        });
    });

    soustabs.addEventListener('click', function (evenement) {
        var bouton = evenement.target.closest('[data-sousonglet]');
        if (!bouton || sousOnglet === bouton.dataset.sousonglet) { return; }
        sousOnglet = bouton.dataset.sousonglet;
        avisFiltre = 'tous';
        rendre();
    });

    grille.addEventListener('click', function (evenement) {
        var bouton = evenement.target.closest('[data-action]');
        if (!bouton) { return; }
        var carteAvisEl = bouton.closest('[data-avis-id]');
        if (carteAvisEl) {
            var item = (avis[vue] || []).find(function (a) { return String(a.id) === carteAvisEl.dataset.avisId; });
            if (item) { ouvrirPopup(bouton.dataset.action, item); }
            return;
        }
        var carteResa = bouton.closest('.reservation-card');
        var resa = donnees[vue].find(function (r) { return String(r.id) === carteResa.dataset.resaId; });
        if (resa) { ouvrirPopup(bouton.dataset.action, resa); }
    });

    /* Pilules Trier par / Filtrer (mêmes gestes que les filtres du
       catalogue : une liste ouverte à la fois, Échap pour fermer) */
    var triBouton = document.getElementById('rv2-tri-btn');
    var triListe = document.getElementById('rv2-tri-liste');

    function fermerPilules() {
        [[triBouton, triListe], [filtreBouton, filtreListe]].forEach(function (paire) {
            paire[1].hidden = true;
            paire[0].classList.remove('is-open');
            paire[0].setAttribute('aria-expanded', 'false');
        });
    }

    function basculerPilule(bouton, liste) {
        var etaitOuverte = !liste.hidden;
        fermerPilules();
        if (!etaitOuverte) {
            liste.hidden = false;
            bouton.classList.add('is-open');
            bouton.setAttribute('aria-expanded', 'true');
        }
    }

    triBouton.addEventListener('click', function () { basculerPilule(triBouton, triListe); });
    filtreBouton.addEventListener('click', function () { basculerPilule(filtreBouton, filtreListe); });

    triListe.addEventListener('click', function (evenement) {
        var choix = evenement.target.closest('[data-tri]');
        if (!choix) { return; }
        avisTri = choix.dataset.tri;
        triListe.querySelectorAll('[data-tri]').forEach(function (item) {
            item.classList.toggle('is-active', item.dataset.tri === avisTri);
        });
        fermerPilules();
        rendre();
    });

    filtreListe.addEventListener('click', function (evenement) {
        var choix = evenement.target.closest('[data-filtre-avis]');
        if (!choix) { return; }
        avisFiltre = choix.dataset.filtreAvis;
        fermerPilules();
        rendre();
    });

    document.addEventListener('click', function (evenement) {
        if (!evenement.target.closest('.rv2-pilule')) { fermerPilules(); }
    });

    popup.addEventListener('click', function (evenement) {
        if (evenement.target === popup || evenement.target.closest('[data-rv2-fermer]')) { fermerPopup(); }
    });
    popupConfirmer.addEventListener('click', confirmerAction);
    popupChamp.addEventListener('input', majConfirmer);
    popupEtoiles.addEventListener('click', function (evenement) {
        var bouton = evenement.target.closest('button[data-note]');
        if (!bouton) { return; }
        noteChoisie = Number(bouton.dataset.note);
        majEtoiles();
        majConfirmer();
    });
    document.addEventListener('keydown', function (evenement) {
        if (evenement.key !== 'Escape') { return; }
        if (!popup.hidden) { fermerPopup(); }
        fermerPilules();
    });

    /* ---- Lancement ----
       ?vue=hote|locataire et ?onglet=... permettent d'ouvrir la page
       dans un état précis (liens internes, tests). */
    var params = new URLSearchParams(window.location.search);
    if (params.get('vue') === 'hote' || params.get('vue') === 'locataire') { vue = params.get('vue'); }
    if (['en-attente', 'a-venir', 'passees', 'avis'].indexOf(params.get('onglet')) !== -1) { onglet = params.get('onglet'); }
    rendre();
})();
