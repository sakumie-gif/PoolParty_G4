/* =============================================================
   POOL PARTY - SCRIPT PRINCIPAL
   Gère les interactions JS du site (menu burger, carrousel,
   bandeau cookies, pop-up de connexion, etc.).
   ============================================================= */

document.addEventListener('DOMContentLoaded', function () {

    // --- Suivi GA4 : événements personnalisés (clic_cta, form_submit,
    // reservation_complete). ppTrackGA n'envoie rien si le tag gtag est
    // absent (environnement local, ou page mot de passe côté visiteur).
    function ppTrackGA(nom, params) {
        if (typeof window.gtag === 'function') {
            window.gtag('event', nom, params || {});
        }
    }

    document.addEventListener('click', function (event) {
        if (!event.target.closest) {
            return;
        }
        var cta = event.target.closest('.header-cta, .cta-hote__btn, .resa__submit, .resa-barre__btn, [class*="-cta__actions"] a');
        if (cta) {
            ppTrackGA('clic_cta', {
                cta_texte: (cta.textContent || '').trim().slice(0, 100),
                page: window.location.pathname
            });
        }
    });

    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (form && form.matches && form.matches('.contact-form, .partenaire-form')) {
            ppTrackGA('form_submit', {
                formulaire: form.classList.contains('contact-form') ? 'contact' : 'partenaire',
                page: window.location.pathname
            });
        }
    });

    // --- Astérisque sur les champs obligatoires (tout le site)
    // Ajoute une étoile au libellé de chaque champ qu'il faut remplir pour
    // valider un formulaire ou passer à l'écran suivant. On se base sur
    // l'attribut required / aria-required porté par le champ : le marquage
    // reste ainsi synchronisé avec la validation, sans lister les champs à
    // la main. Le libellé doit être visible (les libellés sr-only, comme
    // sur les écrans à une seule question du tunnel, sont ignorés).
    Array.prototype.slice.call(document.querySelectorAll('label.form-field__label')).forEach(function (label) {
        if (label.querySelector('.form-field__requis') || label.classList.contains('sr-only')) {
            return;
        }

        // Champ associé : via l'attribut for, sinon le champ du même bloc
        var champ = null;
        var cible = label.getAttribute('for');
        if (cible) {
            champ = document.getElementById(cible);
        }
        if (!champ) {
            var bloc = label.closest('.form-field');
            if (bloc) {
                champ = bloc.querySelector('input, textarea, select');
            }
        }
        if (!champ) {
            return;
        }

        var obligatoire = champ.hasAttribute('required') || champ.getAttribute('aria-required') === 'true';
        if (!obligatoire) {
            return;
        }

        var etoile = document.createElement('span');
        etoile.className = 'form-field__requis';
        etoile.setAttribute('aria-hidden', 'true');
        etoile.textContent = '*';
        label.appendChild(etoile);
    });

    // Menu burger : ouverture et fermeture du menu déroulant
    var burger = document.querySelector('.header-burger');
    var mainMenu = document.getElementById('main-menu');

    if (burger && mainMenu) {
        burger.addEventListener('click', function (event) {
            event.stopPropagation();
            var isOpen = !mainMenu.hidden;
            mainMenu.hidden = isOpen;
            burger.setAttribute('aria-expanded', String(!isOpen));
        });

        // Fermer le menu si on clique ailleurs sur la page
        document.addEventListener('click', function (event) {
            if (!mainMenu.hidden && !mainMenu.contains(event.target) && !burger.contains(event.target)) {
                mainMenu.hidden = true;
                burger.setAttribute('aria-expanded', 'false');
            }
        });

        // Fermer le menu avec la touche Échap
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !mainMenu.hidden) {
                mainMenu.hidden = true;
                burger.setAttribute('aria-expanded', 'false');
                burger.focus();
            }
        });
    }

    // Connexion simulée : aucun compte réel (projet fictif). L'état est
    // conservé le temps de la session du navigateur. La classe is-connected
    // posée sur body bascule les sections visiteur / connecté du menu.
    // Accès au stockage protégés : si le navigateur le bloque (cookies
    // refusés, navigation privée stricte), un accès direct lèverait une
    // exception et stopperait tout le script.
    var lireConnecte = function () {
        try {
            return sessionStorage.getItem('pp-connecte') === '1';
        } catch (erreur) {
            return false;
        }
    };

    var ecrireConnecte = function (actif) {
        try {
            if (actif) {
                sessionStorage.setItem('pp-connecte', '1');
            } else {
                sessionStorage.removeItem('pp-connecte');
            }
        } catch (erreur) {
            // Stockage indisponible : l'état vivra le temps de la page
        }
    };

    // Identité simulée mémorisée à la connexion / inscription : sert à
    // préremplir le checkout et à rattacher la demande au bon compte.
    var ecrireIdentite = function (prenom, email) {
        try {
            if (prenom) { sessionStorage.setItem('pp-prenom', prenom); }
            if (email) { sessionStorage.setItem('pp-email', email); }
        } catch (erreur) {}
    };

    var effacerIdentite = function () {
        try {
            sessionStorage.removeItem('pp-prenom');
            sessionStorage.removeItem('pp-email');
        } catch (erreur) {}
    };

    if (lireConnecte()) {
        document.body.classList.add('is-connected');
    }

    document.querySelectorAll('.js-logout').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            ecrireConnecte(false);
            effacerIdentite();
            document.body.classList.remove('is-connected');
            // Prévient les autres modules (favoris) du changement d'état
            document.dispatchEvent(new CustomEvent('pp-deconnexion'));
            if (mainMenu) {
                mainMenu.hidden = true;
            }
            if (burger) {
                burger.setAttribute('aria-expanded', 'false');
            }
            // Confirme la déconnexion (montrerToast est défini plus bas,
            // il est disponible au moment du clic)
            montrerToast('Vous avez bien été déconnecté. À bientôt !', false);
        });
    });

    // Liens de fonctionnalités pas encore livrées (ex. « Messages » de
    // l'espace compte). Plutôt que de renvoyer vers « # » — qui remonte la
    // page sans rien faire — on neutralise la navigation et on prévient
    // l'utilisateur par le même toast que le reste du site.
    document.querySelectorAll('.js-bientot').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            montrerToast('Cette fonctionnalité arrive bientôt.', false);
        });
    });

    // Page presse : « Lire le communiqué » déplie le texte complet sous le
    // résumé (le PDF reste téléchargeable par le lien voisin).
    document.querySelectorAll('.js-communique-toggle').forEach(function (bouton) {
        var cible = document.getElementById(bouton.getAttribute('aria-controls'));
        if (!cible) {
            return;
        }
        bouton.addEventListener('click', function () {
            var ouvert = cible.hidden;
            cible.hidden = !ouvert;
            bouton.setAttribute('aria-expanded', String(ouvert));
            bouton.textContent = ouvert ? 'Réduire le communiqué' : 'Lire le communiqué';
        });
    });

    // Page index : faire apparaître la barre de recherche dans le header
    // quand on a scrollé au-delà du hero (la recherche de la photo n'est plus visible).
    var heroHeader = document.querySelector('.site-header--hero');
    var hero = document.querySelector('.hero');

    if (heroHeader && hero && 'IntersectionObserver' in window) {
        var headerHeight = heroHeader.offsetHeight || 90;
        var heroObserver = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                // Quand le hero n'est plus dans la zone visible sous le header,
                // on affiche la recherche dans le header.
                heroHeader.classList.toggle('is-search-visible', !entry.isIntersecting);
            });
        }, { rootMargin: '-' + headerHeight + 'px 0px 0px 0px', threshold: 0 });
        heroObserver.observe(hero);
    }

    // Barres de progression : applique la largeur depuis data-value (0 à 100).
    // On passe par JS pour éviter les styles inline dans le HTML.
    var progressBars = document.querySelectorAll('.progress[data-value]');

    progressBars.forEach(function (progress) {
        var bar = progress.querySelector('.progress__bar');
        var value = parseFloat(progress.getAttribute('data-value'));

        if (bar && !isNaN(value)) {
            // Borne la valeur entre 0 et 100 par sécurité
            value = Math.min(100, Math.max(0, value));
            bar.style.width = value + '%';
        }
    });

    // Carrousel des catégories (page d'accueil) : éventail de cartes.
    // La carte sélectionnée vient au centre et au-dessus des autres,
    // l'onglet associé remplit sa barre de progression en vert.
    // La sélection se fait au clic sur une carte, un onglet ou une flèche.
    var categoriesSection = document.querySelector('.categories');

    if (categoriesSection) {
        var catSlides = Array.prototype.slice.call(categoriesSection.querySelectorAll('.categories-slide'));
        var catTabs = Array.prototype.slice.call(categoriesSection.querySelectorAll('.categorie-tab'));
        var catPrev = categoriesSection.querySelector('[data-carousel-prev]');
        var catNext = categoriesSection.querySelector('[data-carousel-next]');
        var tabsBar = categoriesSection.querySelector('.categories-tabs');
        var positionClasses = ['pos-m2', 'pos-m1', 'pos-0', 'pos-p1', 'pos-p2'];
        var total = catSlides.length;

        // Carte active au chargement : celle qui porte pos-0 (Nouveautés)
        var currentIndex = catSlides.findIndex(function (slide) {
            return slide.classList.contains('pos-0');
        });
        if (currentIndex < 0) {
            currentIndex = Math.floor(total / 2);
        }

        // Décalage circulaire ramené entre -2 et +2 : la sélection est
        // au centre (0), les autres se répartissent de part et d'autre
        var offsetDepuisCentre = function (index) {
            var offset = (index - currentIndex + total) % total;
            return offset > 2 ? offset - total : offset;
        };

        var renderCategories = function () {
            catSlides.forEach(function (slide, index) {
                var offset = offsetDepuisCentre(index);
                positionClasses.forEach(function (cls) {
                    slide.classList.remove(cls);
                });
                slide.classList.add(positionClasses[offset + 2]);
                slide.setAttribute('aria-pressed', String(index === currentIndex));
            });

            catTabs.forEach(function (tab, index) {
                // Les onglets tournent avec les cartes : chaque libellé
                // reste sous sa carte, l'onglet actif toujours au centre
                tab.style.order = String(offsetDepuisCentre(index) + 2);
                tab.classList.toggle('is-active', index === currentIndex);
                tab.setAttribute('aria-pressed', String(index === currentIndex));
            });

            // Centre l'onglet actif quand la rangée d'onglets défile (mobile)
            var activeTab = catTabs[currentIndex];
            if (tabsBar && activeTab && tabsBar.scrollWidth > tabsBar.clientWidth) {
                tabsBar.scrollTo({
                    left: activeTab.offsetLeft - (tabsBar.clientWidth - activeTab.offsetWidth) / 2,
                    behavior: 'smooth'
                });
            }
        };

        var selectCategory = function (index) {
            currentIndex = (index + total) % total;
            renderCategories();
        };

        // Chaque carte et chaque onglet portent un data-categorie qui
        // pointe vers la page des annonces filtrée sur cette catégorie
        var ouvrirCategorie = function (element) {
            var slug = element.getAttribute('data-categorie');
            // Les catégories du carrousel (près de chez vous, coup de coeur,
            // nouveautés...) sont des regroupements marketing sans terme de
            // taxonomie correspondant : on renvoie donc vers le catalogue
            // complet. En WordPress, ppData.catalogueUrl fournit le permalien
            // du catalogue (/catalogue/) ; repli sur categorie.html en statique.
            var base = (window.ppData && window.ppData.catalogueUrl) || 'categorie.html';
            if (!slug) {
                window.location.href = base;
                return;
            }
            var sep = base.indexOf('?') === -1 ? '?' : '&';
            window.location.href = base + sep + 'categorie=' + slug;
        };

        catSlides.forEach(function (slide, index) {
            slide.addEventListener('click', function () {
                // Un clic sur une carte de côté la ramène au centre ;
                // un clic sur la carte déjà centrée ouvre ses annonces
                // (un double clic enchaîne donc les deux naturellement)
                if (index === currentIndex) {
                    ouvrirCategorie(slide);
                } else {
                    selectCategory(index);
                }
            });
        });

        catTabs.forEach(function (tab) {
            // Le clic sur le titre emmène directement aux annonces
            tab.addEventListener('click', function () {
                ouvrirCategorie(tab);
            });
        });

        if (catPrev) {
            catPrev.addEventListener('click', function () {
                selectCategory(currentIndex - 1);
            });
        }

        if (catNext) {
            catNext.addEventListener('click', function () {
                selectCategory(currentIndex + 1);
            });
        }

        // Rendu initial : centre l'onglet actif dans la rangée (mobile)
        renderCategories();
    }

    // Newsletter : pas d'envoi réel (projet fictif), on affiche
    // simplement une confirmation à la place du formulaire.
    var newsletterForm = document.querySelector('.newsletter__form');

    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (event) {
            event.preventDefault();

            var confirmation = document.createElement('p');
            confirmation.className = 'newsletter__text';
            confirmation.setAttribute('role', 'status');
            confirmation.textContent = 'Merci ! Votre inscription est bien prise en compte.';
            newsletterForm.replaceWith(confirmation);
        });
    }

    // Pop-up de connexion : ouverte depuis le lien Connexion du menu
    // (ou de la page inscription), fermée par la croix, un clic sur le
    // voile ou la touche Échap. L'inscription se fait sur sa propre
    // page (inscription.html), plus en pop-up.
    // Le bouton de validation reste grisé tant que des champs sont vides (Figma).
    var loginOverlay = document.getElementById('login-popup');

    var configurerPopupCompte = function (overlay, classeLiens) {
        if (!overlay) {
            return;
        }

        var card = overlay.querySelector('.login-popup__card');
        var croix = overlay.querySelector('.login-popup__close');
        var form = overlay.querySelector('.login-popup__form');
        var reset = overlay.querySelector('.login-popup__reset');
        // Champs du seul écran de connexion (le champ e-mail de l'écran de
        // réinitialisation porte la même classe mais ne doit pas peser sur
        // l'activation du bouton « Continuer »)
        var champs = form ? form.querySelectorAll('.form-field__input') : overlay.querySelectorAll('.form-field__input');
        var submit = overlay.querySelector('.login-popup__submit');
        var oeil = overlay.querySelector('.form-field__eye');
        var motDePasse = overlay.querySelector('input[name="password"]');
        var veilleAutofill = null;

        var ouvrir = function () {
            overlay.hidden = false;
            // Toujours rouvrir sur l'écran de connexion, jamais sur l'écran
            // de réinitialisation laissé ouvert la fois précédente
            if (form && reset) {
                form.hidden = false;
                reset.hidden = true;
            }
            // Referme le menu déroulant s'il est ouvert
            if (mainMenu) {
                mainMenu.hidden = true;
            }
            if (burger) {
                burger.setAttribute('aria-expanded', 'false');
            }
            if (champs.length) {
                champs[0].focus();
            }
            // Veille anti-autofill : le remplissage automatique du
            // navigateur ne déclenche pas toujours input, on revalide
            // régulièrement tant que la fenêtre est ouverte
            if (!veilleAutofill) {
                veilleAutofill = setInterval(function () {
                    majSubmit();
                }, 800);
            }
        };

        var fermer = function () {
            overlay.hidden = true;
            if (veilleAutofill) {
                clearInterval(veilleAutofill);
                veilleAutofill = null;
            }
            // Prévient le module favoris : une fermeture sans connexion
            // abandonne le coup de coeur mis en attente
            document.dispatchEvent(new CustomEvent('pp-popup-fermee'));
        };

        document.querySelectorAll(classeLiens).forEach(function (lien) {
            lien.addEventListener('click', function (event) {
                event.preventDefault();
                ouvrir();
            });
        });

        if (croix) {
            croix.addEventListener('click', fermer);
        }

        // Liens internes qui referment simplement la pop-up (proposer.html :
        // l'écran de création de compte est déjà derrière la fenêtre)
        overlay.querySelectorAll('.js-close-popup').forEach(function (lien) {
            lien.addEventListener('click', function (event) {
                event.preventDefault();
                fermer();
            });
        });

        // Clic sur le voile sombre : fermeture
        overlay.addEventListener('click', function (event) {
            if (card && !card.contains(event.target)) {
                fermer();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !overlay.hidden) {
                fermer();
            }
        });

        // Oeil du champ mot de passe : bascule l'affichage en clair
        if (oeil && motDePasse) {
            oeil.addEventListener('click', function () {
                var visible = motDePasse.type === 'text';
                motDePasse.type = visible ? 'password' : 'text';
                oeil.setAttribute('aria-pressed', String(!visible));
            });
        }

        // Validation : active seulement quand tous les champs sont remplis
        var majSubmit = function () {
            if (submit) {
                var complet = Array.prototype.every.call(champs, function (champ) {
                    return champ.value.trim() !== '';
                });
                submit.disabled = !complet;
            }
        };

        champs.forEach(function (champ) {
            champ.addEventListener('input', majSubmit);
        });

        // Aucun compte réel : la soumission active l'état connecté
        // simulé (menu version connectée) puis referme la fenêtre.
        // L'événement pp-connexion prévient les autres modules (favoris).
        if (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                // Un coup de coeur mis en attente affichera son propre
                // toast à la connexion : on ne double pas la confirmation
                var favoriEnAttente = favorisEnAttente;
                var champEmailConnexion = form.querySelector('input[name="email"]');
                ecrireConnecte(true);
                ecrireIdentite('', champEmailConnexion ? champEmailConnexion.value.trim() : '');
                document.body.classList.add('is-connected');
                // pp-connexion d'abord : le coup de coeur en attente est
                // appliqué avant que la fermeture ne l'abandonne
                document.dispatchEvent(new CustomEvent('pp-connexion'));
                fermer();
                // Confirme la connexion, sauf si un favori vient déjà
                // de déclencher son toast (montrerToast est défini plus bas,
                // il est disponible au moment de la soumission)
                if (!favoriEnAttente) {
                    montrerToast('Connexion réussie. Bon retour parmi nous !', false);
                }
            });
        }

        // Mot de passe oublié : bascule vers un mini-écran de
        // réinitialisation. Projet fictif — aucun e-mail n'est réellement
        // envoyé, on confirme simplement la demande (comme la connexion et
        // l'inscription simulées). Retour possible vers l'écran de connexion.
        var lienOubli = overlay.querySelector('.js-forgot');
        if (lienOubli && form && reset) {
            var resetEmail = reset.querySelector('input[type="email"]');
            var resetSubmit = reset.querySelector('.login-popup__reset-submit');
            var resetConfirm = reset.querySelector('.login-popup__reset-confirmation');
            var resetRetour = reset.querySelector('.js-back-login');

            var afficherReset = function (actif) {
                form.hidden = actif;
                reset.hidden = !actif;
                if (actif && resetEmail) {
                    resetEmail.focus();
                }
            };

            // Le bouton reste grisé tant que l'e-mail n'est pas plausible
            var majResetSubmit = function () {
                if (resetSubmit && resetEmail) {
                    resetSubmit.disabled = resetEmail.value.indexOf('@') <= 0;
                }
            };

            lienOubli.addEventListener('click', function (event) {
                event.preventDefault();
                if (resetConfirm) {
                    resetConfirm.hidden = true;
                    resetConfirm.textContent = '';
                }
                afficherReset(true);
                majResetSubmit();
            });

            if (resetRetour) {
                resetRetour.addEventListener('click', function () {
                    afficherReset(false);
                });
            }

            if (resetEmail) {
                resetEmail.addEventListener('input', majResetSubmit);
            }

            reset.addEventListener('submit', function (event) {
                event.preventDefault();
                if (resetConfirm && resetEmail) {
                    resetConfirm.hidden = false;
                    resetConfirm.textContent = 'Si un compte est associé à ' + resetEmail.value.trim() +
                        ', un e-mail avec un lien de réinitialisation vient de partir.';
                }
                if (resetSubmit) {
                    resetSubmit.disabled = true;
                }
            });
        }

        // La fonction d'ouverture est rendue pour que d'autres modules
        // puissent afficher la fenêtre (coeur favori d'un visiteur)
        return ouvrir;
    };

    var ouvrirConnexion = configurerPopupCompte(loginOverlay, '.js-open-login');

    // Page inscription : mêmes comportements que la pop-up de connexion
    // (oeil du mot de passe, bouton grisé tant que le formulaire est
    // incomplet ou que les CGU ne sont pas acceptées). La soumission
    // active l'état connecté simulé et remplace le formulaire par le
    // message de bienvenue.
    var inscriptionForm = document.getElementById('inscription-form');

    if (inscriptionForm) {
        var inscriptionChamps = inscriptionForm.querySelectorAll('.form-field__input');
        var inscriptionCgu = inscriptionForm.querySelector('input[name="cgu"]');
        var inscriptionSubmit = inscriptionForm.querySelector('.inscription-form__submit');
        var inscriptionOeil = inscriptionForm.querySelector('.form-field__eye');
        var inscriptionMdp = inscriptionForm.querySelector('input[name="password"]');
        var inscriptionSucces = document.getElementById('inscription-succes');
        var inscriptionManque = inscriptionForm.querySelector('[data-manque]');
        var inscriptionPrenom = document.getElementById('signup-prenom');
        var inscriptionNom = document.getElementById('signup-nom');
        var inscriptionEmail = document.getElementById('signup-email');

        if (inscriptionOeil && inscriptionMdp) {
            inscriptionOeil.addEventListener('click', function () {
                var visible = inscriptionMdp.type === 'text';
                inscriptionMdp.type = visible ? 'password' : 'text';
                inscriptionOeil.setAttribute('aria-pressed', String(!visible));
            });
        }

        // Mêmes règles que l'écran compte du tunnel proposer : champs
        // remplis, e-mail plausible, mot de passe de 8 caractères et CGU
        var manquesInscription = function () {
            var manques = [];
            if (!inscriptionPrenom.value.trim()) {
                manques.push('votre prénom');
            }
            if (!inscriptionNom.value.trim()) {
                manques.push('votre nom');
            }
            if (inscriptionEmail.value.indexOf('@') <= 0) {
                manques.push('une adresse e-mail valide');
            }
            if (inscriptionMdp.value.length < 8) {
                manques.push('un mot de passe de 8 caractères minimum');
            }
            if (inscriptionCgu && !inscriptionCgu.checked) {
                manques.push('la case des conditions d\'utilisation');
            }
            return manques;
        };

        // Le message n'apparaît qu'une fois la saisie commencée, pour ne
        // pas accueillir le visiteur avec une liste de reproches
        var inscriptionEngagee = function () {
            return Boolean(inscriptionPrenom.value ||
                inscriptionNom.value ||
                inscriptionEmail.value ||
                inscriptionMdp.value ||
                (inscriptionCgu && inscriptionCgu.checked));
        };

        var majInscription = function () {
            var manques = manquesInscription();

            if (inscriptionSubmit) {
                inscriptionSubmit.disabled = manques.length > 0;
            }

            // Le champ mot de passe se signale dès qu'il est trop court
            inscriptionMdp.classList.toggle('has-error',
                inscriptionMdp.value !== '' && inscriptionMdp.value.length < 8);

            if (inscriptionManque) {
                var visibles = inscriptionEngagee() ? manques : [];
                inscriptionManque.hidden = visibles.length === 0;
                inscriptionManque.textContent = visibles.length
                    ? 'Pour continuer, il manque : ' + visibles.join(', ') + '.'
                    : '';
            }
        };

        inscriptionChamps.forEach(function (champ) {
            champ.addEventListener('input', majInscription);
        });

        if (inscriptionCgu) {
            inscriptionCgu.addEventListener('change', majInscription);
        }

        // Veille anti-autofill : même besoin que la pop-up de connexion,
        // le remplissage automatique ne déclenche pas toujours input
        var inscriptionVeille = setInterval(majInscription, 800);

        inscriptionForm.addEventListener('submit', function (event) {
            event.preventDefault();
            clearInterval(inscriptionVeille);
            ecrireConnecte(true);
            ecrireIdentite(
                inscriptionPrenom ? inscriptionPrenom.value.trim() : '',
                inscriptionEmail ? inscriptionEmail.value.trim() : ''
            );
            document.body.classList.add('is-connected');
            inscriptionForm.hidden = true;
            if (inscriptionSucces) {
                inscriptionSucces.hidden = false;
            }
        });
    }

    // Calendrier : sélecteur de date attaché aux champs Date des barres
    // de recherche (hero et header). Le panneau est construit ici pour
    // garder le HTML léger ; tous les styles vivent dans global.css.
    var MOIS_FR = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    var JOURS_FR = ['L', 'M', 'M', 'J', 'V', 'S', 'D'];

    var construireCalendrier = function (input) {
        // Le panneau s'ancre sur un champ des barres de recherche ou
        // sur le champ Date du panneau de réservation (page produit)
        var field = input.closest('.search-field, .resa-field');
        if (!field) {
            return;
        }

        var aujourdhui = new Date();
        aujourdhui.setHours(0, 0, 0, 0);

        var vueMois = aujourdhui.getMonth();
        var vueAnnee = aujourdhui.getFullYear();
        var selection = null;

        // Plus de sélecteur d'horaire dans le calendrier : le panneau de
        // réservation a déjà son propre champ Créneau, et les barres de
        // recherche filtrent au jour près (le créneau se choisit à la
        // réservation). L'horaire débordait aussi du champ étroit et
        // masquait la date que l'on venait de saisir.
        var avecHoraire = false;

        // Reprend la date déjà affichée dans le champ (ex. "15/07/2026")
        // pour marquer le jour correspondant dans la grille
        var valeurInitiale = (input.value || '').match(/^(\d{2})\/(\d{2})\/(\d{4})/);
        if (valeurInitiale) {
            var dateInitiale = new Date(parseInt(valeurInitiale[3], 10),
                parseInt(valeurInitiale[2], 10) - 1, parseInt(valeurInitiale[1], 10));
            if (!isNaN(dateInitiale.getTime())) {
                selection = dateInitiale;
                vueMois = selection.getMonth();
                vueAnnee = selection.getFullYear();
            }
        }

        // Créneau horaire : double curseur de 6h à minuit,
        // sélection par défaut 9h - 12h. Minuit est porté par la
        // valeur 24 et s'affiche 00h.
        var HEURE_MIN = 6;
        var HEURE_MAX = 24;
        var heureDebut = 9;
        var heureFin = 12;

        var libelleHeure = function (heure) {
            return (heure === 24 ? '00' : heure) + 'h';
        };

        // Squelette du panneau (classes stylées dans global.css)
        var panel = document.createElement('div');
        panel.className = 'calendar';
        panel.hidden = true;
        panel.innerHTML =
            '<div class="calendar__header">' +
                '<div class="calendar__picker">' +
                    '<button type="button" class="calendar__picker-btn" data-picker="mois" aria-expanded="false"></button>' +
                    '<ul class="calendar__picker-list" data-liste="mois" hidden></ul>' +
                '</div>' +
                '<div class="calendar__picker">' +
                    '<button type="button" class="calendar__picker-btn" data-picker="annee" aria-expanded="false"></button>' +
                    '<ul class="calendar__picker-list" data-liste="annee" hidden></ul>' +
                '</div>' +
                '<button type="button" class="calendar__today">Aujourd’hui</button>' +
            '</div>' +
            '<div class="calendar__days" aria-hidden="true"></div>' +
            '<div class="calendar__grid"></div>';
        if (avecHoraire) {
            panel.innerHTML +=
                '<hr class="calendar__sep">' +
                '<p class="calendar__label">Horaire</p>' +
                '<p class="calendar__range-value"></p>' +
                '<div class="calendar__range">' +
                    '<span class="calendar__range-fill"></span>' +
                    '<input type="range" class="calendar__range-input" data-borne="debut" aria-label="Heure de début">' +
                    '<input type="range" class="calendar__range-input" data-borne="fin" aria-label="Heure de fin">' +
                '</div>';
        }
        field.appendChild(panel);

        var moisBtn = panel.querySelector('[data-picker="mois"]');
        var anneeBtn = panel.querySelector('[data-picker="annee"]');
        var moisListe = panel.querySelector('[data-liste="mois"]');
        var anneeListe = panel.querySelector('[data-liste="annee"]');
        var joursRow = panel.querySelector('.calendar__days');
        var grille = panel.querySelector('.calendar__grid');
        var rangeValue = panel.querySelector('.calendar__range-value');
        var rangeFill = panel.querySelector('.calendar__range-fill');
        var curseurDebut = panel.querySelector('[data-borne="debut"]');
        var curseurFin = panel.querySelector('[data-borne="fin"]');

        JOURS_FR.forEach(function (jour) {
            var span = document.createElement('span');
            span.textContent = jour;
            joursRow.appendChild(span);
        });

        // Écrit la sélection dans le champ : date puis créneau horaire
        var majInput = function () {
            if (!selection) {
                return;
            }
            input.value = ('0' + selection.getDate()).slice(-2) + '/' +
                ('0' + (selection.getMonth() + 1)).slice(-2) + '/' + selection.getFullYear() +
                (avecHoraire ? ', ' + libelleHeure(heureDebut) + ' - ' + libelleHeure(heureFin) : '');
            // Prévient les scripts qui écoutent le champ (le récapitulatif
            // du panneau de réservation recalcule son total)
            input.dispatchEvent(new Event('change', { bubbles: true }));
        };

        // Positionne le segment vert entre les deux poignées et
        // met à jour le créneau affiché ("9h - 12h")
        var majCreneau = function () {
            if (!avecHoraire) {
                return;
            }
            var plage = HEURE_MAX - HEURE_MIN;
            var gauche = ((heureDebut - HEURE_MIN) / plage) * 100;
            var largeur = ((heureFin - heureDebut) / plage) * 100;
            rangeFill.style.left = gauche + '%';
            rangeFill.style.width = largeur + '%';
            rangeValue.textContent = libelleHeure(heureDebut) + ' - ' + libelleHeure(heureFin);
            majInput();
        };

        // Bornes des deux curseurs (Figma : 9h à 20h)
        if (avecHoraire) {
            [curseurDebut, curseurFin].forEach(function (curseur) {
                curseur.min = HEURE_MIN;
                curseur.max = HEURE_MAX;
                curseur.step = 1;
            });
            curseurDebut.value = heureDebut;
            curseurFin.value = heureFin;

            // Chaque poignée reste de son côté : une heure d'écart minimum
            curseurDebut.addEventListener('input', function () {
                heureDebut = Math.min(parseInt(curseurDebut.value, 10), heureFin - 1);
                curseurDebut.value = heureDebut;
                majCreneau();
            });

            curseurFin.addEventListener('input', function () {
                heureFin = Math.max(parseInt(curseurFin.value, 10), heureDebut + 1);
                curseurFin.value = heureFin;
                majCreneau();
            });
        }

        var fermerListes = function () {
            [moisListe, anneeListe].forEach(function (liste) {
                liste.hidden = true;
            });
            [moisBtn, anneeBtn].forEach(function (btn) {
                btn.setAttribute('aria-expanded', 'false');
            });
        };

        // Dessine la grille du mois affiché
        var rendre = function () {
            moisBtn.textContent = MOIS_FR[vueMois];
            anneeBtn.textContent = String(vueAnnee);
            grille.innerHTML = '';

            // Indice du premier jour du mois, semaine commençant le lundi
            var premier = new Date(vueAnnee, vueMois, 1);
            var decalage = (premier.getDay() + 6) % 7;
            var nbJours = new Date(vueAnnee, vueMois + 1, 0).getDate();

            for (var i = 0; i < decalage; i++) {
                grille.appendChild(document.createElement('span'));
            }

            for (var jour = 1; jour <= nbJours; jour++) {
                var date = new Date(vueAnnee, vueMois, jour);
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'calendar__day';
                btn.textContent = String(jour);

                // Les jours passés ne sont pas réservables
                if (date < aujourdhui) {
                    btn.disabled = true;
                }

                if (selection && date.getTime() === selection.getTime()) {
                    btn.classList.add('is-selected');
                }

                btn.addEventListener('click', (function (d) {
                    return function () {
                        selection = d;
                        majInput();
                        rendre();
                    };
                })(date));

                grille.appendChild(btn);
            }
        };

        // Listes déroulantes Mois et Année (composant Dropdown item)
        var remplirListe = function (liste, valeurs, surChoix) {
            liste.innerHTML = '';
            valeurs.forEach(function (valeur, index) {
                var li = document.createElement('li');
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item';
                btn.textContent = String(valeur);
                btn.addEventListener('click', function () {
                    surChoix(index, valeur);
                    fermerListes();
                    rendre();
                });
                li.appendChild(btn);
                liste.appendChild(li);
            });
        };

        remplirListe(moisListe, MOIS_FR, function (index) {
            vueMois = index;
        });
        remplirListe(anneeListe, [vueAnnee, vueAnnee + 1], function (index, valeur) {
            vueAnnee = valeur;
        });

        [[moisBtn, moisListe], [anneeBtn, anneeListe]].forEach(function (paire) {
            paire[0].addEventListener('click', function () {
                var etaitFermee = paire[1].hidden;
                fermerListes();
                paire[1].hidden = !etaitFermee;
                paire[0].setAttribute('aria-expanded', String(etaitFermee));
            });
        });

        // Aujourd'hui : sélectionne la date du jour et remet la vue dessus
        panel.querySelector('.calendar__today').addEventListener('click', function () {
            vueMois = aujourdhui.getMonth();
            vueAnnee = aujourdhui.getFullYear();
            selection = new Date(aujourdhui);
            majInput();
            rendre();
        });

        // Ouverture au clic sur le champ Date ; un seul panneau ouvert à la fois
        input.addEventListener('click', function () {
            document.querySelectorAll('.calendar').forEach(function (autre) {
                if (autre !== panel) {
                    autre.hidden = true;
                }
            });
            panel.hidden = false;
            rendre();
        });

        // Fermeture au clic ailleurs sur la page ou avec Échap.
        // On écoute pointerdown : il précède le clic, donc la grille
        // n'a pas encore été reconstruite et le test reste fiable.
        document.addEventListener('pointerdown', function (event) {
            if (!panel.hidden && !panel.contains(event.target) && event.target !== input) {
                panel.hidden = true;
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !panel.hidden) {
                panel.hidden = true;
                fermerListes();
                // Échap ne vise que le calendrier ouvert : on empêche les
                // autres écouteurs (pop-up de réservation) de fermer leur
                // fenêtre en même temps, ce qui annulerait la saisie
                event.stopImmediatePropagation();
            }
        });

        rendre();
        majCreneau();
    };

    document.querySelectorAll('.hero-search input[name="date"], .header-search input[name="date"], .resa-field input[name="date"]')
        .forEach(construireCalendrier);

    // Sélecteur d'invités des barres de recherche (header et hero) :
    // petit panneau compteur Adultes / Enfants ancré au champ, à l'image
    // de la fiche produit, pour lever l'incohérence relevée (la barre ne
    // dissociait pas adultes et enfants). Le champ visible devient un
    // résumé en lecture seule ("2 adultes, 1 enfant") et un champ caché
    // porte le total numérique envoyé au catalogue, dont le filtre de
    // capacité attend un nombre.
    var construireCompteurInvites = function (input) {
        var field = input.closest('.search-field');
        if (!field) {
            return;
        }
        // On retire --small (98px, prévu pour un simple nombre) au profit
        // de --invites, plus large, sinon le résumé "2 adultes, 1 enfant"
        // serait tronqué (--small est déclaré plus bas et l'emporterait).
        field.classList.remove('search-field--small');
        field.classList.add('search-field--invites');

        var CAP_MAX = 8; // adultes + enfants
        var invites = { adultes: 1, enfants: 0 };

        // Une recherche relancée réaffiche le total déjà choisi
        var totalInitial = parseInt((input.value || '').replace(/\D/g, ''), 10);
        var aChoisi = !isNaN(totalInitial) && totalInitial > 0;
        if (aChoisi) {
            invites.adultes = Math.min(Math.max(1, totalInitial), CAP_MAX);
        }

        // Champ caché : porte le total sous le nom "invites" ; le champ
        // visible ne doit plus être soumis tel quel (il contient du texte)
        var hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = input.getAttribute('name') || 'invites';
        field.appendChild(hidden);
        input.removeAttribute('name');
        input.readOnly = true;
        input.setAttribute('aria-haspopup', 'dialog');
        input.setAttribute('aria-expanded', 'false');

        var MOINS = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/></svg>';
        var PLUS = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14M12 5v14"/></svg>';
        var lignes = [
            { nom: 'adultes', libelle: 'Adultes', detail: '13 ans et +', min: 1, article: 'un adulte' },
            { nom: 'enfants', libelle: 'Enfants', detail: 'De 2 à 12 ans', min: 0, article: 'un enfant' }
        ];

        var panel = document.createElement('div');
        panel.className = 'invites-pop';
        panel.hidden = true;
        lignes.forEach(function (ligne) {
            panel.innerHTML +=
                '<div class="invites-stepper" data-compte="' + ligne.nom + '" data-min="' + ligne.min + '">' +
                    '<div class="invites-stepper__libelle">' +
                        '<span class="invites-stepper__nom">' + ligne.libelle + '</span>' +
                        '<span class="invites-stepper__detail">' + ligne.detail + '</span>' +
                    '</div>' +
                    '<div class="invites-stepper__controles">' +
                        '<button type="button" class="invites-stepper__btn" data-action="moins" aria-label="Retirer ' + ligne.article + '">' + MOINS + '</button>' +
                        '<span class="invites-stepper__valeur" aria-live="polite">0</span>' +
                        '<button type="button" class="invites-stepper__btn" data-action="plus" aria-label="Ajouter ' + ligne.article + '">' + PLUS + '</button>' +
                    '</div>' +
                '</div>';
        });
        field.appendChild(panel);

        // Résumé lisible : "2 adultes, 1 enfant" (le pluriel suit le nombre)
        var resume = function () {
            var parts = [invites.adultes + (invites.adultes > 1 ? ' adultes' : ' adulte')];
            if (invites.enfants > 0) {
                parts.push(invites.enfants + (invites.enfants > 1 ? ' enfants' : ' enfant'));
            }
            return parts.join(', ');
        };

        var majInput = function () {
            hidden.value = String(invites.adultes + invites.enfants);
            input.value = aChoisi ? resume() : '';
        };

        var steppers = Array.prototype.slice.call(panel.querySelectorAll('.invites-stepper'));

        var majSteppers = function () {
            var occupants = invites.adultes + invites.enfants;
            steppers.forEach(function (stepper) {
                var nom = stepper.getAttribute('data-compte');
                var min = parseInt(stepper.getAttribute('data-min'), 10) || 0;
                stepper.querySelector('.invites-stepper__valeur').textContent = String(invites[nom]);
                stepper.querySelector('[data-action="moins"]').disabled = invites[nom] <= min;
                stepper.querySelector('[data-action="plus"]').disabled = occupants >= CAP_MAX;
            });
        };

        steppers.forEach(function (stepper) {
            var nom = stepper.getAttribute('data-compte');
            var min = parseInt(stepper.getAttribute('data-min'), 10) || 0;
            stepper.querySelector('[data-action="moins"]').addEventListener('click', function () {
                if (invites[nom] <= min) {
                    return;
                }
                invites[nom] -= 1;
                aChoisi = true;
                majSteppers();
                majInput();
            });
            stepper.querySelector('[data-action="plus"]').addEventListener('click', function () {
                if (invites.adultes + invites.enfants >= CAP_MAX) {
                    return;
                }
                invites[nom] += 1;
                aChoisi = true;
                majSteppers();
                majInput();
            });
        });

        // Ouverture au clic ou au focus ; un seul panneau ouvert à la fois
        var ouvrir = function () {
            document.querySelectorAll('.invites-pop').forEach(function (autre) {
                if (autre !== panel) {
                    autre.hidden = true;
                }
            });
            document.querySelectorAll('.calendar').forEach(function (cal) {
                cal.hidden = true;
            });
            panel.hidden = false;
            input.setAttribute('aria-expanded', 'true');
        };
        var fermer = function () {
            panel.hidden = true;
            input.setAttribute('aria-expanded', 'false');
        };
        input.addEventListener('click', ouvrir);
        input.addEventListener('focus', ouvrir);

        // Fermeture au clic ailleurs sur la page ou avec Échap
        document.addEventListener('pointerdown', function (event) {
            if (!panel.hidden && !panel.contains(event.target) && event.target !== input) {
                fermer();
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !panel.hidden) {
                fermer();
                event.stopImmediatePropagation();
            }
        });

        majSteppers();
        majInput();
    };

    document.querySelectorAll('.hero-search input[name="invites"], .header-search input[name="invites"]')
        .forEach(construireCompteurInvites);

    // Champ « Quoi » des barres de recherche : liste déroulante des types
    // de biens louables plutôt qu'une saisie libre. Le champ devient un
    // sélecteur en lecture seule ; l'item choisi remplit le champ (name
    // "quoi"), que le catalogue lit en recherche plein texte (les titres
    // des biens contiennent le type : « Piscine… », « Spa… »).
    var construireListeQuoi = function (input) {
        var field = input.closest('.search-field');
        if (!field) {
            return;
        }
        field.classList.add('search-field--quoi');
        input.readOnly = true;
        input.setAttribute('aria-haspopup', 'listbox');
        input.setAttribute('aria-expanded', 'false');

        var TYPES = ['Piscine', 'Jacuzzi', 'Spa', 'Sauna', 'Hammam'];
        var options = [{ label: 'Tous les types', valeur: '' }].concat(
            TYPES.map(function (t) { return { label: t, valeur: t }; })
        );

        var panel = document.createElement('div');
        panel.className = 'quoi-pop';
        panel.hidden = true;
        var liste = document.createElement('ul');
        options.forEach(function (opt) {
            var li = document.createElement('li');
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'dropdown-item';
            btn.textContent = opt.label;
            btn.setAttribute('data-valeur', opt.valeur);
            li.appendChild(btn);
            liste.appendChild(li);
        });
        panel.appendChild(liste);
        field.appendChild(panel);

        var items = Array.prototype.slice.call(panel.querySelectorAll('.dropdown-item'));

        // Marque l'item courant (utile si une recherche est relancée)
        var marquerActif = function () {
            items.forEach(function (item) {
                item.classList.toggle('is-active', item.getAttribute('data-valeur') === input.value);
            });
        };
        marquerActif();

        var fermer = function () {
            panel.hidden = true;
            input.setAttribute('aria-expanded', 'false');
        };
        var ouvrir = function () {
            document.querySelectorAll('.quoi-pop, .invites-pop, .calendar').forEach(function (autre) {
                if (autre !== panel) {
                    autre.hidden = true;
                }
            });
            panel.hidden = false;
            input.setAttribute('aria-expanded', 'true');
        };

        items.forEach(function (item) {
            item.addEventListener('click', function () {
                input.value = item.getAttribute('data-valeur');
                marquerActif();
                fermer();
            });
        });

        input.addEventListener('click', ouvrir);
        input.addEventListener('focus', ouvrir);

        // Fermeture au clic ailleurs sur la page ou avec Échap
        document.addEventListener('pointerdown', function (event) {
            if (!panel.hidden && !panel.contains(event.target) && event.target !== input) {
                fermer();
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !panel.hidden) {
                fermer();
                event.stopImmediatePropagation();
            }
        });
    };

    document.querySelectorAll('.hero-search input[name="quoi"], .header-search input[name="quoi"]')
        .forEach(construireListeQuoi);

    // Page catégorie : quand on arrive depuis le carrousel de l'accueil
    // (paramètre ?categorie= dans l'adresse), le fil d'Ariane, le titre
    // de l'onglet et l'en-tête des résultats reprennent la catégorie choisie.
    // L'étape ville du fil d'Ariane n'apparaît que si une adresse a été
    // saisie dans la recherche : arriver par le carrousel ne doit pas
    // afficher un lieu qui n'a jamais été choisi.
    var breadcrumbCourant = document.querySelector('.breadcrumb__etape.is-current');

    if (breadcrumbCourant && window.location.search) {
        var parametresRecherche = new URLSearchParams(window.location.search);
        var nomsCategories = {
            'pres-de-chez-vous': 'Près de chez vous',
            'coup-de-coeur': 'Coup de coeur',
            'nouveautes': 'Nouveautés',
            'spas-jacuzzis': 'Spas et Jacuzzis',
            'insolite': 'Insolite'
        };
        var nomCategorie = nomsCategories[parametresRecherche.get('categorie')];

        if (nomCategorie) {
            breadcrumbCourant.textContent = nomCategorie;
            document.title = nomCategorie + ' en Île-de-France - Pool Party';

            var titreResultats = document.getElementById('resultats-titre');
            if (titreResultats) {
                titreResultats.textContent = nomCategorie;
            }
        }

        var etapeVille = document.querySelector('.breadcrumb__region');
        var adresseRecherchee = (parametresRecherche.get('adresse') || '').trim();

        if (etapeVille && adresseRecherchee) {
            // L'étape peut être un simple <li> texte (fil d'Ariane des
            // archives) ou contenir une ancre : on écrit dans l'un ou l'autre.
            var lienVille = etapeVille.querySelector('a');
            (lienVille || etapeVille).textContent = adresseRecherchee;
            etapeVille.hidden = false;
        }
    }

    // Page catégorie : pilules de filtres (Catégories, Prix, Distance,
    // Date, Nombre de personne, Filtres). Chaque pilule ouvre sa liste
    // déroulante ; une seule liste ouverte à la fois, fermeture au clic
    // ailleurs ou avec Échap. Dans les listes simples, choisir un item
    // le marque actif et referme ; les panneaux prix et filtres restent
    // ouverts pendant la manipulation.
    var pilules = Array.prototype.slice.call(document.querySelectorAll('.pilule'));

    if (pilules.length) {
        var fermerPilules = function (sauf) {
            pilules.forEach(function (pilule) {
                if (pilule === sauf) {
                    return;
                }
                var bouton = pilule.querySelector('.menu-item');
                var liste = pilule.querySelector('.pilule__liste');
                liste.hidden = true;
                bouton.classList.remove('is-open');
                bouton.setAttribute('aria-expanded', 'false');
            });
        };

        pilules.forEach(function (pilule) {
            var bouton = pilule.querySelector('.menu-item');
            var liste = pilule.querySelector('.pilule__liste');

            bouton.addEventListener('click', function () {
                var ouvrir = liste.hidden;
                fermerPilules(pilule);
                liste.hidden = !ouvrir;
                bouton.classList.toggle('is-open', ouvrir);
                bouton.setAttribute('aria-expanded', String(ouvrir));
            });

            // Listes simples : l'item choisi devient actif et referme.
            // Les panneaux (prix, filtres détaillés) n'ont pas d'items
            // directs, leurs contrôles internes ne ferment donc rien.
            var items = Array.prototype.slice.call(liste.querySelectorAll('.dropdown-item'));
            items.forEach(function (item) {
                item.addEventListener('click', function () {
                    items.forEach(function (autre) {
                        autre.classList.toggle('is-active', autre === item);
                    });
                    fermerPilules(null);
                });
            });
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.pilule')) {
                fermerPilules(null);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                fermerPilules(null);
            }
        });
    }

    // Page catégorie : interrupteur "Afficher la carte". La carte
    // interactive (Leaflet + tuiles OpenStreetMap) n'est construite
    // qu'au premier affichage. Chaque carte produit porte les
    // coordonnées de son annonce (data-lat, data-lon, data-titre) :
    // un marqueur point par bien, le grand pin maison pour le bien
    // sélectionné, synchronisé avec la grille dans les deux sens.
    var carteCheckbox = document.getElementById('carte-checkbox');
    var cartePanel = document.getElementById('carte-panel');
    var carteLayout = document.getElementById('categorie-layout');

    if (carteCheckbox && cartePanel && carteLayout) {
        var carteConteneur = document.getElementById('carte-osm');
        var cartesAnnonces = Array.prototype.slice.call(document.querySelectorAll('.resultats-grid .card-product'));
        var carte = null;
        var marqueurs = [];

        // Deux icônes : le point discret et le grand pin maison, dont la
        // pointe reste ancrée sur la ville (iconAnchor en bas au centre)
        var svgPinMaison = '<svg viewBox="0 0 32 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">'
            + '<path class="carte-pin__goutte" d="M16 1C8.05 1 1.6 7.36 1.6 15.2c0 9.5 11.55 20.05 13.3 21.6a1.65 1.65 0 0 0 2.2 0C18.85 35.25 30.4 24.7 30.4 15.2 30.4 7.36 23.95 1 16 1Z"/>'
            + '<path class="carte-pin__maison" d="M9.8 14.8 16 9.8l6.2 5M11.6 14.4v6.6h8.8v-6.6"/></svg>';

        var iconePoint = null;
        var iconeMaison = null;
        if (window.L) {
            iconePoint = L.divIcon({
                className: 'carte-pin',
                html: '<span class="carte-pin__point"></span>',
                iconSize: [18, 18],
                iconAnchor: [9, 9]
            });
            iconeMaison = L.divIcon({
                className: 'carte-pin carte-pin--maison',
                html: svgPinMaison,
                iconSize: [34, 42],
                iconAnchor: [17, 42]
            });
        }

        // Un seul bien sélectionné à la fois : son marqueur devient le
        // grand pin maison et son annonce est cerclée dans la grille
        var selectionneMarqueur = function (indice, defiler) {
            marqueurs.forEach(function (marqueur, i) {
                var actif = i === indice;
                if (marqueur) {
                    marqueur.setIcon(actif ? iconeMaison : iconePoint);
                    marqueur.setZIndexOffset(actif ? 1000 : 0);
                }
                if (cartesAnnonces[i]) {
                    cartesAnnonces[i].classList.toggle('is-carte-active', actif);
                    if (actif && defiler) {
                        cartesAnnonces[i].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                    }
                }
            });
        };

        var initialiseCarte = function () {
            if (carte || !window.L || !carteConteneur) {
                return;
            }

            carte = L.map(carteConteneur, { zoomControl: false });
            L.control.zoom({ position: 'topright' }).addTo(carte);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(carte);

            var bornes = [];
            cartesAnnonces.forEach(function (annonce, i) {
                var lat = parseFloat(annonce.getAttribute('data-lat'));
                var lon = parseFloat(annonce.getAttribute('data-lon'));
                if (isNaN(lat) || isNaN(lon)) {
                    marqueurs.push(null);
                    return;
                }

                var marqueur = L.marker([lat, lon], {
                    icon: iconePoint,
                    title: annonce.getAttribute('data-titre') || '',
                    riseOnHover: true
                });
                marqueur.on('click', function () {
                    selectionneMarqueur(i, true);
                });
                marqueur.addTo(carte);
                marqueurs.push(marqueur);
                bornes.push([lat, lon]);
            });

            if (bornes.length) {
                carte.fitBounds(bornes, { padding: [40, 40] });
            } else {
                carte.setView([48.855, 2.62], 11);
            }

            // Au départ, le pin sélectionné est celui du bien le plus proche
            selectionneMarqueur(marqueurs[1] ? 1 : 0, false);
        };

        var majAffichageCarte = function () {
            var visible = carteCheckbox.checked;
            cartePanel.hidden = !visible;
            carteLayout.classList.toggle('is-carte', visible);

            if (visible) {
                initialiseCarte();
                if (carte) {
                    // La taille du panneau change avec la disposition
                    carte.invalidateSize();
                }
            }
        };

        carteCheckbox.addEventListener('change', majAffichageCarte);

        // Le navigateur peut restaurer l'état coché (retour arrière)
        if (carteCheckbox.checked) {
            majAffichageCarte();
        }

        // Survoler ou atteindre au clavier une annonce sélectionne son pin
        cartesAnnonces.forEach(function (annonce, i) {
            var suitAnnonce = function () {
                if (!cartePanel.hidden && marqueurs[i]) {
                    selectionneMarqueur(i, false);
                }
            };
            annonce.addEventListener('mouseenter', suitAnnonce);
            annonce.addEventListener('focusin', suitAnnonce);
        });
    }

    // Page catégorie : fourchette de prix par heure. Même mécanique que
    // le créneau horaire du calendrier : deux curseurs superposés, le
    // segment vert est repositionné entre les deux poignées et chaque
    // poignée reste de son côté avec un pas d'écart minimum.
    var prixGroupe = document.querySelector('.filtres__prix');

    if (prixGroupe) {
        var prixValeur = prixGroupe.querySelector('.range-double__value');
        var prixFill = prixGroupe.querySelector('.range-double__fill');
        var prixDebut = prixGroupe.querySelector('[data-borne="debut"]');
        var prixFin = prixGroupe.querySelector('[data-borne="fin"]');
        var prixMin = parseInt(prixDebut.min, 10);
        var prixMax = parseInt(prixDebut.max, 10);
        var prixPas = parseInt(prixDebut.step, 10) || 1;

        var majFourchette = function () {
            var debut = parseInt(prixDebut.value, 10);
            var fin = parseInt(prixFin.value, 10);
            var plage = prixMax - prixMin;
            prixFill.style.left = (((debut - prixMin) / plage) * 100) + '%';
            prixFill.style.width = (((fin - debut) / plage) * 100) + '%';
            prixValeur.textContent = debut + ' € - ' + fin + ' €';
        };

        prixDebut.addEventListener('input', function () {
            var borne = parseInt(prixFin.value, 10) - prixPas;
            if (parseInt(prixDebut.value, 10) > borne) {
                prixDebut.value = borne;
            }
            majFourchette();
        });

        prixFin.addEventListener('input', function () {
            var borne = parseInt(prixDebut.value, 10) + prixPas;
            if (parseInt(prixFin.value, 10) < borne) {
                prixFin.value = borne;
            }
            majFourchette();
        });

        // Tout effacer : les curseurs vivent dans la pilule Prix, hors du
        // formulaire des filtres, le reset natif ne les touche donc pas.
        // On les remet nous-mêmes à leurs valeurs de départ.
        var filtresForm = document.querySelector('.filtres__form');
        if (filtresForm) {
            filtresForm.addEventListener('reset', function () {
                prixDebut.value = prixDebut.defaultValue;
                prixFin.value = prixFin.defaultValue;
                majFourchette();
            });
        }

        // Appliquer : recharge le catalogue avec la fourchette choisie en
        // paramètres d'URL (prix_min / prix_max), lus côté serveur pour
        // filtrer réellement les résultats. Les autres filtres actifs
        // (catégorie, distance, tri…) déjà présents dans l'URL sont conservés.
        var prixAppliquer = prixGroupe.querySelector('.filtres__prix-appliquer');
        if (prixAppliquer) {
            prixAppliquer.addEventListener('click', function () {
                var url = new URL(window.location.href);
                url.searchParams.set('prix_min', prixDebut.value);
                url.searchParams.set('prix_max', prixFin.value);
                window.location.href = url.toString();
            });
        }

        majFourchette();
    }

    // Page catégorie : tri des annonces. "Trier par : Avis" ouvre la
    // liste des ordres de tri ; l'item choisi remplace le libellé.
    var tri = document.querySelector('.tri');

    if (tri) {
        var triBouton = tri.querySelector('.tri__bouton');
        var triValeur = document.getElementById('tri-valeur');
        var triListe = document.getElementById('tri-liste');
        var triItems = Array.prototype.slice.call(tri.querySelectorAll('.dropdown-item'));

        var fermerTri = function () {
            triListe.hidden = true;
            triBouton.classList.remove('is-open');
            triBouton.setAttribute('aria-expanded', 'false');
        };

        triBouton.addEventListener('click', function () {
            var ouvrir = triListe.hidden;
            triListe.hidden = !ouvrir;
            triBouton.classList.toggle('is-open', ouvrir);
            triBouton.setAttribute('aria-expanded', String(ouvrir));
        });

        triItems.forEach(function (item) {
            item.addEventListener('click', function () {
                triValeur.textContent = item.textContent.trim();
                triItems.forEach(function (autre) {
                    autre.classList.toggle('is-active', autre === item);
                });
                fermerTri();
            });
        });

        // Fermeture au clic ailleurs sur la page ou avec Échap
        document.addEventListener('click', function (event) {
            if (!triListe.hidden && !tri.contains(event.target)) {
                fermerTri();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !triListe.hidden) {
                fermerTri();
            }
        });
    }

    // =========================================================
    // FAVORIS
    // Les coeurs des cartes annonce et le bouton Favori de la
    // fiche produit alimentent une liste d'annonces conservée
    // dans localStorage : elle survit d'une page à l'autre et se
    // retrouve sur favoris.html. Un visiteur non connecté est
    // d'abord invité à se connecter ; son coup de coeur est
    // appliqué juste après la connexion, puis une confirmation
    // propose d'ouvrir la page Mes favoris.
    // =========================================================
    var FAVORIS_CLE = 'pp-favoris';
    var favorisEnAttente = null;

    var estConnecte = function () {
        return document.body.classList.contains('is-connected');
    };

    var lireFavoris = function () {
        try {
            var liste = JSON.parse(localStorage.getItem(FAVORIS_CLE));
            return Array.isArray(liste) ? liste : [];
        } catch (erreur) {
            return [];
        }
    };

    var ecrireFavoris = function (liste) {
        try {
            localStorage.setItem(FAVORIS_CLE, JSON.stringify(liste));
        } catch (erreur) {
            // Stockage indisponible : le coeur restera visuel, sans mémoire
        }
    };

    // Identifiant stable d'une annonce : l'attribut data-favori-id posé
    // dans le HTML quand plusieurs annonces partagent la même photo,
    // sinon le nom de fichier de la photo
    var idDepuisImage = function (src) {
        return (src || '').split('/').pop().replace(/\.[a-z]+$/i, '');
    };

    // Photographie les informations d'une carte annonce pour pouvoir
    // la reconstruire telle quelle sur la page Mes favoris
    var annonceDepuisCarte = function (carte) {
        var img = carte.querySelector('.card-product__media img');
        var titre = carte.querySelector('.card-product__title a');
        var note = carte.querySelector('.rating');
        var tag = carte.querySelector('.card-product__tag');
        var prix = carte.querySelector('.card-product__price');

        var annonce = {
            id: carte.getAttribute('data-favori-id') || (img ? idDepuisImage(img.getAttribute('src')) : ''),
            image: img ? img.getAttribute('src') : '',
            alt: img ? img.alt : '',
            titre: titre ? titre.textContent.trim() : 'Annonce',
            lien: titre ? titre.getAttribute('href') : 'produit.html',
            note: '',
            avis: '',
            tag: tag ? tag.textContent.trim() : '',
            tagClasse: '',
            metas: [],
            prix: prix ? prix.textContent.trim() : ''
        };

        if (note) {
            var compte = note.querySelector('.rating__count');
            annonce.avis = compte ? compte.textContent.trim() : '';
            annonce.note = note.textContent.replace(annonce.avis, '').trim();
        }

        if (tag) {
            // Conserve la couleur du tag (tag--proche, tag--top-vente...)
            Array.prototype.forEach.call(tag.classList, function (classe) {
                if (classe.indexOf('tag--') === 0) {
                    annonce.tagClasse = classe;
                }
            });
        }

        carte.querySelectorAll('.card-product__meta').forEach(function (meta) {
            var valeurs = Array.prototype.map.call(meta.querySelectorAll('span'), function (span) {
                return span.textContent.trim();
            });
            if (valeurs.length) {
                annonce.metas.push(valeurs);
            }
        });

        return annonce;
    };

    // Même photographie pour l'en-tête de la fiche produit, dont le
    // bouton Favori enregistre l'annonce affichée
    var annonceDepuisFiche = function () {
        var entete = document.querySelector('.produit-entete');
        var photo = document.getElementById('galerie-photo');
        if (!entete || !photo) {
            return null;
        }

        var titre = entete.querySelector('h1');
        var note = entete.querySelector('.rating');
        var prix = document.querySelector('.resa__prix');

        var annonce = {
            id: entete.getAttribute('data-favori-id') || idDepuisImage(photo.getAttribute('src')),
            image: photo.getAttribute('src'),
            alt: photo.alt,
            titre: titre ? titre.textContent.trim() : 'Annonce',
            lien: window.location.pathname + window.location.search,
            note: '',
            avis: '',
            tag: '',
            tagClasse: '',
            metas: [],
            prix: prix ? prix.textContent.trim() : ''
        };

        if (note) {
            var lienAvis = note.querySelector('.rating__link');
            annonce.avis = lienAvis ? '(' + lienAvis.textContent.trim() + ')' : '';
            annonce.note = note.textContent.replace(lienAvis ? lienAvis.textContent : '', '').trim();
        }

        entete.querySelectorAll('.produit-entete__info').forEach(function (info) {
            annonce.metas.push([info.textContent.trim()]);
        });

        return annonce;
    };

    // Confirmation discrète en bas d'écran (modèle Etsy) : favoris,
    // avec un lien vers la page Mes favoris quand une annonce vient
    // d'y entrer, mais aussi déconnexion
    var toastFavoris = null;
    var toastMinuteur = null;

    var montrerToast = function (message, avecLien) {
        if (!toastFavoris) {
            toastFavoris = document.createElement('div');
            toastFavoris.className = 'fav-toast';
            toastFavoris.setAttribute('role', 'status');
            document.body.appendChild(toastFavoris);
        }

        toastFavoris.innerHTML = '';
        var texte = document.createElement('span');
        texte.className = 'fav-toast__texte';
        texte.textContent = message;
        toastFavoris.appendChild(texte);

        if (avecLien) {
            var lien = document.createElement('a');
            lien.className = 'fav-toast__lien';
            lien.href = (window.ppData && window.ppData.favorisUrl) || 'favoris.html';
            lien.textContent = 'Voir mes favoris';
            toastFavoris.appendChild(lien);
        }

        toastFavoris.classList.add('is-visible');
        clearTimeout(toastMinuteur);
        toastMinuteur = setTimeout(function () {
            toastFavoris.classList.remove('is-visible');
        }, 5000);
    };

    // Recense les coeurs de la page avec l'annonce qu'ils représentent,
    // pour les allumer ou les éteindre d'un seul geste
    var coeursFavoris = [];

    var rafraichirCoeurs = function () {
        var favoris = estConnecte() ? lireFavoris() : [];
        coeursFavoris.forEach(function (entree) {
            var actif = favoris.some(function (annonce) {
                return annonce.id === entree.annonce.id;
            });
            entree.bouton.classList.toggle('is-active', actif);
            entree.bouton.setAttribute('aria-pressed', String(actif));
        });
    };

    // Ajoute ou retire l'annonce de la liste, puis confirme par le toast
    var appliquerFavori = function (annonce, forcerAjout) {
        var favoris = lireFavoris();
        var present = favoris.some(function (f) {
            return f.id === annonce.id;
        });

        if (present && !forcerAjout) {
            ecrireFavoris(favoris.filter(function (f) {
                return f.id !== annonce.id;
            }));
            montrerToast('Annonce retirée de vos favoris.', false);
        } else if (!present) {
            favoris.push(annonce);
            ecrireFavoris(favoris);
            montrerToast('Annonce enregistrée dans vos favoris.', true);
        } else {
            montrerToast('Cette annonce est déjà dans vos favoris.', true);
        }

        rafraichirCoeurs();
    };

    // Clic sur un coeur : un visiteur non connecté doit d'abord se
    // connecter, son coup de coeur patiente pendant ce temps
    var demanderFavori = function (annonce) {
        if (!annonce || !annonce.id) {
            return;
        }
        if (!estConnecte()) {
            favorisEnAttente = annonce;
            if (typeof ouvrirConnexion === 'function') {
                ouvrirConnexion();
            }
            return;
        }
        appliquerFavori(annonce, false);
    };

    document.querySelectorAll('.card-product__fav').forEach(function (bouton) {
        var carte = bouton.closest('.card-product');
        if (!carte) {
            return;
        }
        var annonce = annonceDepuisCarte(carte);
        coeursFavoris.push({ bouton: bouton, annonce: annonce });
        bouton.addEventListener('click', function () {
            demanderFavori(annonce);
        });
    });

    // Après connexion : applique le coup de coeur mis en attente et
    // rallume les coeurs enregistrés lors des visites précédentes
    document.addEventListener('pp-connexion', function () {
        if (favorisEnAttente) {
            appliquerFavori(favorisEnAttente, true);
            favorisEnAttente = null;
        } else {
            rafraichirCoeurs();
        }
        if (typeof rendreFavorisPage === 'function') {
            rendreFavorisPage();
        }
    });

    // Pop-up de connexion refermée sans se connecter : le coup de
    // coeur mis en attente est abandonné, il ne doit pas s'appliquer
    // tout seul lors d'une connexion ultérieure
    document.addEventListener('pp-popup-fermee', function () {
        favorisEnAttente = null;
    });

    // À la déconnexion les coeurs s'éteignent ; la liste reste
    // enregistrée et réapparaîtra à la prochaine connexion
    document.addEventListener('pp-deconnexion', function () {
        favorisEnAttente = null;
        rafraichirCoeurs();
        if (typeof rendreFavorisPage === 'function') {
            rendreFavorisPage();
        }
    });

    // Page Mes favoris : la grille est reconstruite depuis le
    // localStorage ; trois états basculés par l'attribut hidden
    // (visiteur invité à se connecter, liste vide, liste remplie).
    var favorisGrille = document.getElementById('favoris-grid');
    var rendreFavorisPage = null;

    if (favorisGrille) {
        var favorisConnexion = document.getElementById('favoris-connexion');
        var favorisVide = document.getElementById('favoris-vide');
        var favorisCompte = document.getElementById('favoris-compte');

        // Neutralise le HTML des valeurs relues depuis le localStorage.
        // Les guillemets sont encodés aussi : ces valeurs sont injectées
        // dans des attributs (src, alt, href) entre guillemets doubles.
        var echapper = function (texte) {
            return String(texte == null ? '' : texte)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        };

        // Reconstruit une carte annonce du site depuis sa photographie
        var construireCarteFavori = function (annonce) {
            var carte = document.createElement('article');
            carte.className = 'card-product';

            // Le lien relu du localStorage doit rester une adresse du site :
            // http(s) (permaliens WordPress absolus) et les liens relatifs
            // passent, un schéma dangereux (javascript:, data:...) est écarté
            var schemaLien = /^([a-z][a-z0-9+.-]*):/i.exec(annonce.lien || '');
            if (!annonce.lien || (schemaLien && !/^https?$/i.test(schemaLien[1]))) {
                annonce.lien = (window.ppData && window.ppData.catalogueUrl) || 'produit.html';
            }

            var html =
                '<div class="card-product__media">' +
                    '<img src="' + echapper(annonce.image) + '" alt="' + echapper(annonce.alt) + '">' +
                    '<button type="button" class="card-product__fav is-active" aria-label="Retirer des favoris" aria-pressed="true">' +
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.51 4.04 3 5.5l7 7Z"/></svg>' +
                    '</button>' +
                '</div>';

            if (annonce.tag) {
                var classeTag = /^tag--[a-z-]+$/.test(annonce.tagClasse) ? ' ' + annonce.tagClasse : '';
                html += '<span class="tag' + classeTag + ' card-product__tag">' + echapper(annonce.tag) + '</span>';
            }

            html += '<div class="card-product__body">' +
                '<div class="card-product__head">' +
                    '<h3 class="card-product__title"><a href="' + echapper(annonce.lien) + '">' + echapper(annonce.titre) + '</a></h3>';

            if (annonce.note) {
                html += '<span class="rating rating--small">' + echapper(annonce.note) +
                    (annonce.avis ? ' <span class="rating__count">' + echapper(annonce.avis) + '</span>' : '') +
                    '</span>';
            }

            html += '</div>';

            (annonce.metas || []).forEach(function (valeurs) {
                html += '<p class="card-product__meta">' + valeurs.map(function (valeur) {
                    return '<span>' + echapper(valeur) + '</span>';
                }).join('') + '</p>';
            });

            if (annonce.prix) {
                html += '<p class="card-product__price">' + echapper(annonce.prix) + '</p>';
            }

            html += '</div>';
            carte.innerHTML = html;

            // Le coeur retire l'annonce et fait disparaître la carte
            carte.querySelector('.card-product__fav').addEventListener('click', function () {
                ecrireFavoris(lireFavoris().filter(function (f) {
                    return f.id !== annonce.id;
                }));
                montrerToast('Annonce retirée de vos favoris.', false);
                rendreFavorisPage();
            });

            return carte;
        };

        rendreFavorisPage = function () {
            var connecte = estConnecte();
            var favoris = connecte ? lireFavoris() : [];

            if (favorisConnexion) {
                favorisConnexion.hidden = connecte;
            }
            if (favorisVide) {
                favorisVide.hidden = !connecte || favoris.length > 0;
            }
            favorisGrille.hidden = favoris.length === 0;

            if (favorisCompte) {
                if (!connecte) {
                    favorisCompte.textContent = 'Vos coups de coeur vous attendent après connexion';
                } else if (favoris.length === 0) {
                    favorisCompte.textContent = 'Aucun espace enregistré pour le moment';
                } else if (favoris.length === 1) {
                    favorisCompte.textContent = '1 espace enregistré';
                } else {
                    favorisCompte.textContent = favoris.length + ' espaces enregistrés';
                }
            }

            favorisGrille.innerHTML = '';
            favoris.forEach(function (annonce) {
                favorisGrille.appendChild(construireCarteFavori(annonce));
            });
        };

        rendreFavorisPage();
    }

    // =========================================================
    // RÉSERVATIONS
    // Le tunnel « Confirmer et payer » enregistre chaque demande
    // envoyée à l'hôte dans le localStorage : elle survit d'une
    // page à l'autre et se retrouve sur Mes réservations. Comme les
    // favoris, la liste est rattachée au compte : un visiteur non
    // connecté est d'abord invité à se connecter.
    // =========================================================
    var RESERVATIONS_CLE = 'pp-reservations';
    // Marqueur de version du jeu de démonstration : tant qu'il ne
    // correspond pas (première visite, ou après avoir tout supprimé
    // sous une version antérieure), les exemples sont ressemés. Bumper
    // cette valeur force une réinitialisation des demandes de démo.
    var RESERVATIONS_SEED_CLE = 'pp-reservations-seed';
    var RESERVATIONS_DEMO_VERSION = '2';

    var lireReservations = function () {
        try {
            var liste = JSON.parse(localStorage.getItem(RESERVATIONS_CLE));
            return Array.isArray(liste) ? liste : [];
        } catch (erreur) {
            return [];
        }
    };

    var ecrireReservations = function (liste) {
        try {
            localStorage.setItem(RESERVATIONS_CLE, JSON.stringify(liste));
        } catch (erreur) {
            // Stockage indisponible : la demande partira quand même par e-mail
        }
    };

    // Ajoute une demande en tête de liste (la plus récente d'abord).
    // On pose aussi le marqueur de version pour qu'une vraie demande ne
    // soit jamais écrasée par un futur réamorçage des exemples de démo.
    var enregistrerReservation = function (resa) {
        var liste = lireReservations();
        liste.unshift(resa);
        ecrireReservations(liste);
        try {
            localStorage.setItem(RESERVATIONS_SEED_CLE, RESERVATIONS_DEMO_VERSION);
        } catch (erreur) {
            // Stockage indisponible : sans conséquence sur l'envoi de la demande
        }
    };

    // Lecture « brute » : distingue une liste absente (null, jamais
    // amorcée) d'une liste vidée par l'utilisateur ([]), pour ne semer
    // les réservations de démonstration qu'une seule fois.
    var lireReservationsBrut = function () {
        try {
            var liste = JSON.parse(localStorage.getItem(RESERVATIONS_CLE));
            return Array.isArray(liste) ? liste : null;
        } catch (erreur) {
            return null;
        }
    };

    // Amorce la liste avec les réservations de démonstration du thème
    // (window.ppData.reservationsDemo). Elles sont (re)semées quand la
    // liste n'a jamais été amorcée, ou qu'elle est vide alors que le
    // marqueur de version ne correspond pas — c'est ce qui restaure les
    // exemples après les avoir tous supprimés en testant. Une liste non
    // vide (vraies demandes) n'est jamais écrasée, et un état vide obtenu
    // sous la version courante est conservé (le marqueur correspond).
    var amorcerReservations = function () {
        var version = null;
        try {
            version = localStorage.getItem(RESERVATIONS_SEED_CLE);
        } catch (erreur) {
            version = null;
        }
        var stockees = lireReservationsBrut();
        var aJour = (version === RESERVATIONS_DEMO_VERSION);

        if (!aJour && (stockees === null || stockees.length === 0)) {
            stockees = (window.ppData && Array.isArray(window.ppData.reservationsDemo))
                ? JSON.parse(JSON.stringify(window.ppData.reservationsDemo))
                : [];
            ecrireReservations(stockees);
        }
        try {
            localStorage.setItem(RESERVATIONS_SEED_CLE, RESERVATIONS_DEMO_VERSION);
        } catch (erreur) {
            // Stockage indisponible : les exemples vivront le temps de la page
        }
        return stockees === null ? [] : stockees;
    };

    // Page Mes réservations : onglets À venir / Passées et cartes
    // reconstruites depuis le localStorage ; trois états basculés par
    // l'attribut hidden (visiteur, aucune réservation, liste).
    var reservationsGrille = document.getElementById('reservations-grid');

    if (reservationsGrille) {
        var resaConnexion = document.getElementById('reservations-connexion');
        var resaVide = document.getElementById('reservations-vide');
        var resaListe = document.getElementById('reservations-liste');
        var resaCompte = document.getElementById('reservations-compte');
        var resaOngletVide = document.getElementById('reservations-onglet-vide');
        var resaTabs = Array.prototype.slice.call(document.querySelectorAll('.reservations-tab'));
        var resaFiltre = 'a-venir';
        var rendreReservationsPage;

        // Neutralise le HTML des valeurs relues depuis le localStorage :
        // elles sont injectées dans des attributs (src, alt, href) et du texte.
        var echapperResa = function (texte) {
            return String(texte == null ? '' : texte)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        };

        // Une demande est « passée » quand sa date (JJ/MM/AAAA) est
        // antérieure à aujourd'hui ; une date absente ou illisible est
        // traitée comme à venir (demande en attente).
        var estPassee = function (resa) {
            var m = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(resa.date || '');
            if (!m) {
                return false;
            }
            var jour = new Date(parseInt(m[3], 10), parseInt(m[2], 10) - 1, parseInt(m[1], 10));
            var aujourdHui = new Date();
            aujourdHui.setHours(0, 0, 0, 0);
            return jour < aujourdHui;
        };

        // Valeur numérique d'une date JJ/MM/AAAA pour le tri (0 si illisible)
        var valeurDate = function (resa) {
            var m = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(resa.date || '');
            return m ? new Date(parseInt(m[3], 10), parseInt(m[2], 10) - 1, parseInt(m[1], 10)).getTime() : 0;
        };

        // Sécurise le lien relu du localStorage : on autorise les URL
        // http(s) (permaliens WordPress absolus) et les liens relatifs,
        // mais on écarte les schémas dangereux (javascript:, data:...) au
        // profit du catalogue des biens.
        var lienSur = function (lien) {
            var repli = (window.ppData && window.ppData.catalogueUrl) || '#';
            lien = String(lien || '');
            if (!lien) {
                return repli;
            }
            var schema = /^([a-z][a-z0-9+.-]*):/i.exec(lien);
            if (schema && !/^https?$/i.test(schema[1])) {
                return repli; // schéma non autorisé
            }
            return lien;
        };

        var construireCarteResa = function (resa, passee) {
            var carte = document.createElement('article');
            carte.className = 'reservation-card' + (passee ? ' reservation-card--passee' : '');

            var lien = echapperResa(lienSur(resa.lien));
            var dateCreneau = echapperResa((resa.date || '') + (resa.creneau ? ' · ' + resa.creneau : ''));

            // Statut : demande en attente pour une réservation à venir,
            // séjour terminé pour une réservation passée.
            var statut = passee
                ? '<span class="tag reservation-card__statut">Terminée</span>'
                : '<span class="tag tag--top-vente reservation-card__statut">En attente de confirmation</span>';

            var html =
                '<a class="reservation-card__media" href="' + lien + '">' +
                    '<img src="' + echapperResa(resa.image) + '" alt="' + echapperResa(resa.alt) + '">' +
                    statut +
                '</a>' +
                '<div class="reservation-card__body">' +
                    '<div class="reservation-card__head">' +
                        '<h3 class="reservation-card__title"><a href="' + lien + '">' + echapperResa(resa.titre) + '</a></h3>';

            if (resa.hote) {
                html += '<p class="reservation-card__hote">Proposé par ' + echapperResa(resa.hote) + '</p>';
            }

            html += '</div>' +
                '<dl class="reservation-card__infos">' +
                    '<div class="reservation-card__info">' +
                        '<dt>Date et créneau</dt>' +
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/></svg>' +
                        '<dd>' + dateCreneau + '</dd>' +
                    '</div>' +
                    '<div class="reservation-card__info">' +
                        '<dt>Invités</dt>' +
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>' +
                        '<dd>' + echapperResa(resa.invites) + '</dd>' +
                    '</div>';

            if (resa.formule) {
                html += '<div class="reservation-card__info">' +
                    '<dt>Formule</dt>' +
                    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>' +
                    '<dd>' + echapperResa(resa.formule) + '</dd>' +
                '</div>';
            }

            html += '</dl>' +
                '<div class="reservation-card__foot">';

            if (resa.total) {
                html += '<p class="reservation-card__total">Total<strong>' + echapperResa(resa.total) + '</strong></p>';
            }

            html += '<div class="reservation-card__actions">' +
                '<a class="btn btn-tertiary btn-small" href="' + lien + '">' + (passee ? 'Réserver à nouveau' : 'Voir l\'annonce') + '</a>' +
                (passee ? '' : '<button type="button" class="btn btn-tertiary btn-small reservation-card__annuler">Annuler la demande</button>') +
                '</div>' +
            '</div>' +
            '</div>';

            carte.innerHTML = html;

            // Annuler la demande : on demande d'abord confirmation via la
            // pop-up (évite une suppression par erreur). Le retrait effectif
            // n'a lieu qu'après validation, dans le pop-up ci-dessous.
            var annuler = carte.querySelector('.reservation-card__annuler');
            if (annuler) {
                annuler.addEventListener('click', function () {
                    ouvrirAnnulation(resa);
                });
            }

            return carte;
        };

        // ---- Pop-up de confirmation d'annulation -----------------------
        // Le bouton d'une carte mémorise la demande visée puis ouvre la
        // pop-up ; « Conserver » (ou la croix / le fond / Échap) referme
        // sans rien changer, « Annuler la demande » retire la réservation.
        var popupAnnuler = document.getElementById('popup-annuler-resa');
        var popupAnnulerTexte = document.getElementById('popup-annuler-texte');
        var resaAAnnuler = null;

        var ouvrirAnnulation = function (resa) {
            resaAAnnuler = resa;
            if (popupAnnulerTexte) {
                popupAnnulerTexte.textContent = 'La demande pour « ' + resa.titre +
                    ' » sera définitivement retirée de vos réservations. Cette action est irréversible.';
            }
            if (popupAnnuler) {
                popupAnnuler.hidden = false;
            }
        };

        var fermerAnnulation = function () {
            resaAAnnuler = null;
            if (popupAnnuler) {
                popupAnnuler.hidden = true;
            }
        };

        if (popupAnnuler) {
            popupAnnuler.querySelectorAll('[data-resa-annuler-fermer]').forEach(function (bouton) {
                bouton.addEventListener('click', fermerAnnulation);
            });

            // Clic sur le fond sombre : fermeture sans annuler
            popupAnnuler.addEventListener('click', function (event) {
                if (event.target === popupAnnuler) {
                    fermerAnnulation();
                }
            });

            var confirmerAnnulation = popupAnnuler.querySelector('[data-resa-annuler-confirmer]');
            if (confirmerAnnulation) {
                confirmerAnnulation.addEventListener('click', function () {
                    if (!resaAAnnuler) {
                        return;
                    }
                    var id = resaAAnnuler.id;
                    ecrireReservations(lireReservations().filter(function (r) {
                        return r.id !== id;
                    }));
                    montrerToast('Demande de réservation annulée.', false);
                    fermerAnnulation();
                    rendreReservationsPage();
                });
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !popupAnnuler.hidden) {
                    fermerAnnulation();
                }
            });
        }

        var majOngletsAria = function () {
            resaTabs.forEach(function (tab) {
                var actif = tab.getAttribute('data-filtre') === resaFiltre;
                tab.classList.toggle('is-active', actif);
                tab.setAttribute('aria-selected', String(actif));
            });
        };

        rendreReservationsPage = function () {
            var connecte = estConnecte();
            // Une fois connecté, la liste est amorcée avec les demandes de
            // démonstration au premier affichage ; ensuite elle reflète le
            // localStorage (vraies demandes du tunnel, annulations...).
            var reservations = connecte ? amorcerReservations() : [];

            if (resaConnexion) {
                resaConnexion.hidden = connecte;
            }
            if (resaVide) {
                resaVide.hidden = !connecte || reservations.length > 0;
            }
            if (resaListe) {
                resaListe.hidden = reservations.length === 0;
            }

            if (resaCompte) {
                if (!connecte) {
                    resaCompte.textContent = 'Vos réservations vous attendent après connexion';
                } else if (reservations.length === 0) {
                    resaCompte.textContent = 'Aucune réservation pour le moment';
                } else if (reservations.length === 1) {
                    resaCompte.textContent = '1 réservation';
                } else {
                    resaCompte.textContent = reservations.length + ' réservations';
                }
            }

            if (reservations.length === 0) {
                reservationsGrille.innerHTML = '';
                return;
            }

            // Filtre selon l'onglet, puis tri : les demandes à venir de la
            // plus proche à la plus lointaine, les passées de la plus
            // récente à la plus ancienne.
            var passeesOnglet = resaFiltre === 'passees';
            var liste = reservations.filter(function (r) {
                return estPassee(r) === passeesOnglet;
            }).sort(function (a, b) {
                return passeesOnglet ? valeurDate(b) - valeurDate(a) : valeurDate(a) - valeurDate(b);
            });

            majOngletsAria();

            reservationsGrille.innerHTML = '';
            liste.forEach(function (resa) {
                reservationsGrille.appendChild(construireCarteResa(resa, passeesOnglet));
            });

            reservationsGrille.hidden = liste.length === 0;
            if (resaOngletVide) {
                resaOngletVide.hidden = liste.length > 0;
                resaOngletVide.textContent = passeesOnglet
                    ? 'Aucune réservation passée pour l\'instant.'
                    : 'Aucune réservation à venir. Vos prochaines demandes s\'afficheront ici.';
            }
        };

        resaTabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                resaFiltre = tab.getAttribute('data-filtre');
                rendreReservationsPage();
            });
        });

        // La connexion / déconnexion simulée rejoue l'affichage : à la
        // connexion, les demandes de démonstration apparaissent ; à la
        // déconnexion, on repasse sur l'invitation à se connecter.
        document.addEventListener('pp-connexion', rendreReservationsPage);
        document.addEventListener('pp-deconnexion', rendreReservationsPage);

        rendreReservationsPage();
    }

    // =========================================================
    // MESSAGERIE INTERNE (page Messages)
    // Boîte de réception locataire ↔ hôte, sur le même principe que
    // les favoris et les réservations : connexion simulée, données
    // dans le localStorage. Au premier affichage connecté, on amorce
    // la boîte avec les conversations de démonstration fournies par
    // le thème (window.ppData.messagerie). Trois états sont basculés
    // par l'attribut hidden ; l'envoi d'un message reçoit une réponse
    // simulée de l'hôte (aucun compte réel : projet fictif).
    // L'architecture cible (tables Conversation / Message) figure
    // dans schema-bdd.html pour la soutenance.
    // =========================================================
    var messagerie = document.getElementById('messagerie');

    if (messagerie) {
        var MESSAGES_CLE = 'pp-messages';
        var msgConnexion = document.getElementById('messages-connexion');
        var msgVide = document.getElementById('messages-vide');
        var msgCompte = document.getElementById('messages-compte');
        var msgConvos = document.getElementById('messagerie-convos');
        var msgFilTete = document.getElementById('messagerie-fil-tete');
        var msgFilPhoto = document.getElementById('messagerie-fil-photo');
        var msgFilHote = document.getElementById('messagerie-fil-hote');
        var msgFilBien = document.getElementById('messagerie-fil-bien');
        var msgMessages = document.getElementById('messagerie-messages');
        var msgVideFil = document.getElementById('messagerie-vide-fil');
        var msgSaisie = document.getElementById('messagerie-saisie');
        var msgChamp = document.getElementById('messagerie-champ');
        var msgEnvoyer = msgSaisie ? msgSaisie.querySelector('.messagerie__envoyer') : null;
        var msgRetour = document.getElementById('messagerie-retour');

        var filActif = null;    // id de la conversation ouverte
        var minuteurHote = null; // réponse simulée de l'hôte en attente

        // Neutralise le HTML des valeurs relues du localStorage avant
        // de les injecter dans la page.
        var echapperMsg = function (texte) {
            return String(texte == null ? '' : texte)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        };

        var lireMessagerie = function () {
            try {
                var liste = JSON.parse(localStorage.getItem(MESSAGES_CLE));
                return Array.isArray(liste) ? liste : null;
            } catch (erreur) {
                return null;
            }
        };

        var ecrireMessagerie = function (liste) {
            try {
                localStorage.setItem(MESSAGES_CLE, JSON.stringify(liste));
            } catch (erreur) {
                // Stockage indisponible : les échanges vivront le temps de la page
            }
        };

        // Amorce la boîte de réception à partir des conversations de
        // démonstration du thème, une seule fois (tant que la clé est
        // absente). Chaque conversation reçoit un indicateur « non lu »
        // si son dernier message vient de l'hôte.
        var amorcerMessagerie = function () {
            var stockees = lireMessagerie();
            if (stockees !== null) {
                return stockees; // déjà amorcée (même si l'utilisateur a tout supprimé)
            }
            var seed = (window.ppData && Array.isArray(window.ppData.messagerie))
                ? JSON.parse(JSON.stringify(window.ppData.messagerie))
                : [];
            seed.forEach(function (conv) {
                var dernier = conv.messages[conv.messages.length - 1];
                conv.lu = !(dernier && dernier.de === 'hote');
            });
            ecrireMessagerie(seed);
            return seed;
        };

        // Libellé horaire court d'un message envoyé à l'instant
        var horodatageMaintenant = function () {
            var d = new Date();
            var hh = ('0' + d.getHours()).slice(-2);
            var mm = ('0' + d.getMinutes()).slice(-2);
            return "Aujourd'hui " + hh + ':' + mm;
        };

        // Construit une entrée de la liste des conversations
        var construireConvo = function (conv) {
            var dernier = conv.messages[conv.messages.length - 1];
            var apercu = dernier ? dernier.texte : '';
            var li = document.createElement('li');
            var bouton = document.createElement('button');
            bouton.type = 'button';
            bouton.className = 'messagerie__convo' + (conv.id === filActif ? ' is-active' : '') + (conv.lu ? '' : ' is-nonlu');
            bouton.setAttribute('data-conv', conv.id);
            bouton.innerHTML =
                '<img class="messagerie__convo-photo" src="' + echapperMsg(conv.photo) + '" alt="">' +
                '<span class="messagerie__convo-corps">' +
                    '<span class="messagerie__convo-tete">' +
                        '<span class="messagerie__convo-hote">' + echapperMsg(conv.hote) + '</span>' +
                        '<span class="messagerie__convo-heure">' + echapperMsg(dernier ? dernier.label : '') + '</span>' +
                    '</span>' +
                    '<span class="messagerie__convo-bien">' + echapperMsg(conv.bienTitre) + '</span>' +
                    '<span class="messagerie__convo-apercu">' + echapperMsg(apercu) + '</span>' +
                '</span>' +
                (conv.lu ? '' : '<span class="messagerie__convo-pastille" aria-label="Non lu"></span>');
            bouton.addEventListener('click', function () {
                ouvrirFil(conv.id);
            });
            li.appendChild(bouton);
            return li;
        };

        // Construit une bulle de message
        var construireBulle = function (message) {
            var moi = message.de === 'moi';
            var div = document.createElement('div');
            div.className = 'messagerie__msg messagerie__msg--' + (moi ? 'moi' : 'hote');
            div.innerHTML =
                '<div class="messagerie__bulle">' + echapperMsg(message.texte) + '</div>' +
                '<span class="messagerie__heure">' + echapperMsg(message.label) + '</span>';
            return div;
        };

        // Sécurise un lien ou une photo relus du localStorage : http(s)
        // et les chemins relatifs passent, tout autre schéma (javascript:,
        // data:...) est écarté ; même garde que favoris et réservations.
        var lienMsgSur = function (lien, repli) {
            lien = String(lien || '');
            var schema = /^([a-z][a-z0-9+.-]*):/i.exec(lien);
            if (!lien || (schema && !/^https?$/i.test(schema[1]))) {
                return repli;
            }
            return lien;
        };

        // Affiche le fil d'une conversation dans le panneau de droite
        var rendreFil = function (conv) {
            if (msgFilTete) { msgFilTete.hidden = false; }
            if (msgVideFil) { msgVideFil.hidden = true; }
            if (msgSaisie) { msgSaisie.hidden = false; }
            if (msgFilPhoto) { msgFilPhoto.src = lienMsgSur(conv.photo, ''); }
            if (msgFilHote) { msgFilHote.textContent = conv.hote; }
            if (msgFilBien) {
                msgFilBien.textContent = conv.bienTitre;
                msgFilBien.href = lienMsgSur(conv.bienLien, (window.ppData && window.ppData.catalogueUrl) || '#');
            }
            msgMessages.innerHTML = '';
            conv.messages.forEach(function (message) {
                msgMessages.appendChild(construireBulle(message));
            });
            msgMessages.scrollTop = msgMessages.scrollHeight;
        };

        var trouverConv = function (liste, id) {
            for (var i = 0; i < liste.length; i++) {
                if (liste[i].id === id) {
                    return liste[i];
                }
            }
            return null;
        };

        // Ouvre une conversation : la marque lue, affiche son fil,
        // bascule en vue « fil » sur mobile.
        var ouvrirFil = function (id) {
            var liste = lireMessagerie() || [];
            var conv = trouverConv(liste, id);
            if (!conv) {
                return;
            }
            filActif = id;
            if (!conv.lu) {
                conv.lu = true;
                ecrireMessagerie(liste);
            }
            messagerie.classList.add('is-thread-open');
            rendreFil(conv);
            rendreListe(liste);
            if (msgChamp) {
                msgChamp.focus();
            }
        };

        // (Re)construit la liste des conversations, triée par activité
        var rendreListe = function (liste) {
            msgConvos.innerHTML = '';
            liste.slice().sort(function (a, b) {
                return (b.maj || 0) - (a.maj || 0);
            }).forEach(function (conv) {
                msgConvos.appendChild(construireConvo(conv));
            });
        };

        // Réponse simulée de l'hôte après l'envoi d'un message
        var reponsesHote = [
            "Merci pour votre message ! Je vous réponds dès que possible.",
            "Bien reçu, je regarde ça et je reviens vers vous très vite.",
            "Avec plaisir ! N'hésitez pas si vous avez d'autres questions.",
            "Parfait, c'est noté. Je vous confirme les détails sous peu."
        ];

        var repondreHote = function (id) {
            var liste = lireMessagerie() || [];
            var conv = trouverConv(liste, id);
            if (!conv) {
                return;
            }
            // Indicateur « l'hôte écrit… » uniquement si le fil est ouvert
            var typing = null;
            if (filActif === id && msgMessages) {
                typing = document.createElement('div');
                typing.className = 'messagerie__typing';
                typing.innerHTML = '<span></span><span></span><span></span>';
                msgMessages.appendChild(typing);
                msgMessages.scrollTop = msgMessages.scrollHeight;
            }
            minuteurHote = setTimeout(function () {
                if (typing && typing.parentNode) {
                    typing.parentNode.removeChild(typing);
                }
                var liste2 = lireMessagerie() || [];
                var conv2 = trouverConv(liste2, id);
                if (!conv2) {
                    return;
                }
                var texte = reponsesHote[Math.floor(Math.random() * reponsesHote.length)];
                conv2.messages.push({ de: 'hote', texte: texte, label: horodatageMaintenant() });
                conv2.maj = Date.now();
                conv2.lu = (filActif === id); // lu si le fil est ouvert à cet instant
                ecrireMessagerie(liste2);
                if (filActif === id) {
                    rendreFil(conv2);
                }
                rendreListe(liste2);
                majCompteur(liste2);
            }, 1600);
        };

        // Envoi d'un message par l'utilisateur
        var envoyerMessage = function () {
            var texte = msgChamp ? msgChamp.value.trim() : '';
            if (!texte || !filActif) {
                return;
            }
            var liste = lireMessagerie() || [];
            var conv = trouverConv(liste, filActif);
            if (!conv) {
                return;
            }
            conv.messages.push({ de: 'moi', texte: texte, label: horodatageMaintenant() });
            conv.maj = Date.now();
            conv.lu = true;
            ecrireMessagerie(liste);
            msgChamp.value = '';
            msgChamp.style.height = 'auto';
            if (msgEnvoyer) {
                msgEnvoyer.disabled = true;
            }
            rendreFil(conv);
            rendreListe(liste);
            clearTimeout(minuteurHote);
            repondreHote(filActif);
        };

        // Compteur de conversations dans l'intro
        var majCompteur = function (liste) {
            if (!msgCompte) {
                return;
            }
            if (!estConnecte()) {
                msgCompte.textContent = 'Vos conversations vous attendent après connexion';
                return;
            }
            var nonlus = liste.filter(function (c) { return !c.lu; }).length;
            if (liste.length === 0) {
                msgCompte.textContent = 'Aucune conversation pour le moment';
            } else if (nonlus > 0) {
                msgCompte.textContent = liste.length + (liste.length > 1 ? ' conversations' : ' conversation')
                    + ' · ' + nonlus + (nonlus > 1 ? ' non lues' : ' non lue');
            } else {
                msgCompte.textContent = liste.length + (liste.length > 1 ? ' conversations' : ' conversation');
            }
        };

        // Bascule des trois états de la page
        var rendreMessageriePage = function () {
            var connecte = estConnecte();
            var liste = connecte ? amorcerMessagerie() : [];

            if (msgConnexion) { msgConnexion.hidden = connecte; }
            if (msgVide) { msgVide.hidden = !connecte || liste.length > 0; }
            messagerie.hidden = !connecte || liste.length === 0;

            majCompteur(liste);

            if (!connecte || liste.length === 0) {
                filActif = null;
                messagerie.classList.remove('is-thread-open');
                return;
            }

            rendreListe(liste);

            // Desktop : ouvrir d'office la conversation la plus récente
            // pour ne pas laisser le panneau de droite vide.
            if (window.matchMedia && window.matchMedia('(min-width: 1024px)').matches) {
                var premiere = liste.slice().sort(function (a, b) {
                    return (b.maj || 0) - (a.maj || 0);
                })[0];
                if (premiere && !filActif) {
                    ouvrirFil(premiere.id);
                }
            }
        };

        // Champ de saisie : agrandissement automatique + activation du bouton
        if (msgChamp) {
            msgChamp.addEventListener('input', function () {
                msgChamp.style.height = 'auto';
                msgChamp.style.height = Math.min(msgChamp.scrollHeight, 120) + 'px';
                if (msgEnvoyer) {
                    msgEnvoyer.disabled = msgChamp.value.trim() === '';
                }
            });
            // Entrée pour envoyer, Maj+Entrée pour un saut de ligne
            msgChamp.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    envoyerMessage();
                }
            });
        }

        if (msgSaisie) {
            msgSaisie.addEventListener('submit', function (event) {
                event.preventDefault();
                envoyerMessage();
            });
        }

        if (msgRetour) {
            msgRetour.addEventListener('click', function () {
                messagerie.classList.remove('is-thread-open');
                filActif = null;
                rendreListe(lireMessagerie() || []);
            });
        }

        // Se met à jour au fil des connexions / déconnexions, comme
        // les pages favoris et réservations.
        document.addEventListener('pp-connexion', rendreMessageriePage);
        document.addEventListener('pp-deconnexion', function () {
            clearTimeout(minuteurHote);
            rendreMessageriePage();
        });

        rendreMessageriePage();
    }

    rafraichirCoeurs();

    // Page produit : galerie photos. Cliquer une vignette l'échange
    // avec la grande photo, la mosaïque garde donc toutes les photos.
    var galeriePhoto = document.getElementById('galerie-photo');
    var vignettes = Array.prototype.slice.call(document.querySelectorAll('.galerie__vignette'));

    if (galeriePhoto && vignettes.length) {
        vignettes.forEach(function (vignette) {
            vignette.addEventListener('click', function () {
                var img = vignette.querySelector('img');
                if (!img) {
                    return;
                }
                var src = galeriePhoto.getAttribute('src');
                var alt = galeriePhoto.alt;
                galeriePhoto.src = img.getAttribute('src');
                galeriePhoto.alt = img.alt;
                img.src = src;
                img.alt = alt;
            });
        });
    }

    // Page produit : actions de l'en-tête et de la barre galerie mobile.
    // Partager copie le lien de la page (les boutons icône seule
    // signalent la copie par un changement de couleur) ; Favori
    // enregistre l'annonce comme le coeur des cartes, les boutons
    // restent synchronisés via coeursFavoris.
    var partagerBoutons = Array.prototype.slice.call(document.querySelectorAll('.js-partager'));

    if (partagerBoutons.length) {
        // Copie le lien de la page. navigator.clipboard n'existe qu'en
        // contexte sécurisé (https ou localhost) : quand le site est servi
        // en HTTP simple (domaine .local de Local), on retombe sur une
        // zone de texte temporaire + execCommand('copy'). Renvoie une
        // promesse pour enchaîner le retour visuel de la même façon.
        var copierLien = function (texte) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                return navigator.clipboard.writeText(texte);
            }
            return new Promise(function (resolve, reject) {
                var champ = document.createElement('textarea');
                champ.value = texte;
                champ.setAttribute('readonly', '');
                champ.style.position = 'fixed';
                champ.style.top = '-1000px';
                champ.style.opacity = '0';
                document.body.appendChild(champ);
                champ.select();
                try {
                    var ok = document.execCommand('copy');
                    document.body.removeChild(champ);
                    if (ok) { resolve(); } else { reject(); }
                } catch (erreur) {
                    document.body.removeChild(champ);
                    reject(erreur);
                }
            });
        };

        partagerBoutons.forEach(function (partager) {
            var partagerLibelle = partager.querySelector('span');
            partager.addEventListener('click', function () {
                var lien = window.location.href;
                copierLien(lien).then(function () {
                    if (partagerLibelle) {
                        partagerLibelle.textContent = 'Lien copié !';
                    }
                    partager.classList.add('is-copie');
                    setTimeout(function () {
                        if (partagerLibelle) {
                            partagerLibelle.textContent = 'Partager';
                        }
                        partager.classList.remove('is-copie');
                    }, 2000);
                }).catch(function () {
                    // Dernier recours : partage natif (mobile) sinon une
                    // invite manuelle pour que le lien reste récupérable.
                    if (navigator.share) {
                        navigator.share({ url: lien }).catch(function () {});
                    } else {
                        window.prompt('Copiez le lien de cette annonce :', lien);
                    }
                });
            });
        });
    }

    var favsEntete = Array.prototype.slice.call(document.querySelectorAll('.produit-action--fav'));

    if (favsEntete.length) {
        var annonceFiche = annonceDepuisFiche();
        if (annonceFiche) {
            favsEntete.forEach(function (favEntete) {
                coeursFavoris.push({ bouton: favEntete, annonce: annonceFiche });
                favEntete.addEventListener('click', function () {
                    demanderFavori(annonceFiche);
                });
            });
            rafraichirCoeurs();
        }
    }

    // Page produit : pop-up « Écrire à l'hôte ». Le bouton de la carte
    // hôte ouvre une fenêtre où l'on rédige un message ; la soumission
    // envoie réellement un e-mail à l'hôte via FormSubmit (même service
    // que la demande de réservation), puis affiche une confirmation à la
    // place du formulaire. Fermeture par la croix, le voile ou Échap.
    var messageHoteBtns = Array.prototype.slice.call(document.querySelectorAll('.js-message-hote'));
    var messageHotePopup = document.getElementById('message-hote-popup');

    if (messageHoteBtns.length && messageHotePopup) {
        var messageHoteForm = messageHotePopup.querySelector('.login-popup__form');
        var messageHoteTexte = document.getElementById('message-hote-texte');
        var messageHoteEmail = document.getElementById('message-hote-email');
        var messageHotePrenom = document.getElementById('message-hote-prenom');
        var messageHoteSubmit = messageHotePopup.querySelector('.login-popup__submit');
        var messageHoteStatut = document.getElementById('message-hote-statut');
        var messageHoteClose = messageHotePopup.querySelector('.login-popup__close');
        // Même alias FormSubmit que la demande de réservation : l'adresse
        // réelle de l'hôte n'apparaît pas en clair dans le code publié.
        var MESSAGE_HOTE_ENDPOINT = 'https://formsubmit.co/ajax/26aa38cbbdb858a800ab9b41ca816ab2';

        var fermerMessageHote = function () {
            messageHotePopup.hidden = true;
        };

        // Le bouton d'envoi reste grisé tant que l'e-mail et le message
        // ne sont pas renseignés (mêmes règles souples que le reste du site).
        var majMessageHoteSubmit = function () {
            if (!messageHoteSubmit) {
                return;
            }
            var emailOk = messageHoteEmail && messageHoteEmail.value.indexOf('@') > 0;
            var messageOk = messageHoteTexte && messageHoteTexte.value.trim() !== '';
            messageHoteSubmit.disabled = !(emailOk && messageOk);
        };

        messageHoteBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                messageHotePopup.hidden = false;
                if (messageHoteTexte) {
                    messageHoteTexte.focus();
                }
            });
        });

        if (messageHoteClose) {
            messageHoteClose.addEventListener('click', fermerMessageHote);
        }

        messageHotePopup.addEventListener('click', function (event) {
            if (event.target === messageHotePopup) {
                fermerMessageHote();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !messageHotePopup.hidden) {
                fermerMessageHote();
            }
        });

        [messageHoteEmail, messageHoteTexte, messageHotePrenom].forEach(function (champ) {
            if (champ) {
                champ.addEventListener('input', majMessageHoteSubmit);
            }
        });
        majMessageHoteSubmit();

        if (messageHoteForm) {
            messageHoteForm.addEventListener('submit', function (event) {
                event.preventDefault();

                var hote = messageHoteForm.getAttribute('data-hote') || 'l\'hôte';
                var annonce = messageHoteForm.getAttribute('data-annonce') || 'Espace Pool Party';

                if (messageHoteSubmit) {
                    messageHoteSubmit.disabled = true;
                    messageHoteSubmit.textContent = 'Envoi en cours...';
                }

                var message = {
                    _subject: 'Pool Party : nouveau message pour ' + hote + ' (' + annonce + ')',
                    _template: 'table',
                    _captcha: 'false',
                    email: messageHoteEmail ? messageHoteEmail.value : '',
                    prenom: messageHotePrenom ? messageHotePrenom.value : '',
                    annonce: annonce,
                    message: messageHoteTexte ? messageHoteTexte.value : ''
                };

                // Le site restant fictif, la confirmation s'affiche même si
                // le service est injoignable (fetch en échec), comme la
                // demande de réservation.
                var afficherEnvoye = function () {
                    var champs = messageHoteForm.querySelector('.login-popup__fields');
                    if (champs) {
                        champs.hidden = true;
                    }
                    if (messageHoteSubmit) {
                        messageHoteSubmit.hidden = true;
                    }
                    if (messageHoteStatut) {
                        messageHoteStatut.hidden = false;
                        messageHoteStatut.textContent = 'Message envoyé ! ' + hote +
                            ' vient de le recevoir par e-mail et vous répondra sous 24h maximum.';
                    }
                };

                fetch(MESSAGE_HOTE_ENDPOINT, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(message)
                }).catch(function (erreur) {
                    console.warn('Envoi du message à l\'hôte impossible :', erreur);
                }).finally(afficherEnvoye);
            });
        }
    }

    // Page produit : pop-up de la galerie complète, ouvert par le
    // bouton Afficher toutes les photos posé sur la mosaïque, et en
    // mobile par les photos du carrousel ou le compteur.
    var galeriePopup = document.getElementById('galerie-popup');
    var galerieOuvreurs = Array.prototype.slice.call(
        document.querySelectorAll('.galerie__toutes, .galerie__diapo, .galerie__compteur')
    );

    if (galeriePopup && galerieOuvreurs.length) {
        var galerieFermer = galeriePopup.querySelector('.popup__close');

        galerieOuvreurs.forEach(function (ouvreur) {
            ouvreur.addEventListener('click', function () {
                galeriePopup.hidden = false;
            });
        });

        if (galerieFermer) {
            galerieFermer.addEventListener('click', function () {
                galeriePopup.hidden = true;
            });
        }

        // Clic sur le voile sombre ou touche Échap : fermeture
        galeriePopup.addEventListener('click', function (event) {
            if (event.target === galeriePopup) {
                galeriePopup.hidden = true;
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !galeriePopup.hidden) {
                galeriePopup.hidden = true;
            }
        });
    }

    // Page produit : Lire la suite déplie le reste de la description
    var descToggle = document.querySelector('.description__toggle');
    var descSuite = document.getElementById('description-suite');

    if (descToggle && descSuite) {
        descToggle.addEventListener('click', function () {
            var ouvrir = descSuite.hidden;
            descSuite.hidden = !ouvrir;
            descToggle.textContent = ouvrir ? 'Réduire' : 'Lire la suite';
            descToggle.setAttribute('aria-expanded', String(ouvrir));
        });
    }

    // Page produit : carrousel photos mobile. Le défilement met à
    // jour le point actif et le compteur 1 / 5 (maquette mobile).
    var galerieCarrousel = document.querySelector('.galerie__carrousel');

    if (galerieCarrousel) {
        var galeriePoints = Array.prototype.slice.call(document.querySelectorAll('.galerie__point'));
        var galerieCompteur = document.querySelector('.galerie__compteur');
        var galerieDiapos = galerieCarrousel.children.length;

        galerieCarrousel.addEventListener('scroll', function () {
            var index = Math.round(galerieCarrousel.scrollLeft / galerieCarrousel.clientWidth);
            index = Math.max(0, Math.min(index, galerieDiapos - 1));

            galeriePoints.forEach(function (point, i) {
                point.classList.toggle('is-active', i === index);
            });

            if (galerieCompteur) {
                galerieCompteur.textContent = (index + 1) + ' / ' + galerieDiapos;
            }
        }, { passive: true });
    }

    // Page produit : Voir plus déplie les cartes caractéristiques
    // masquées en mobile (la maquette n'en montre que deux).
    var caracsToggle = document.querySelector('.caracs__toggle');
    var caracsCartes = document.getElementById('caracs-cartes');

    if (caracsToggle && caracsCartes) {
        caracsToggle.addEventListener('click', function () {
            var ouvrir = !caracsCartes.classList.contains('is-ouvert');
            caracsCartes.classList.toggle('is-ouvert', ouvrir);
            caracsToggle.textContent = ouvrir ? 'Voir moins' : 'Voir plus';
            caracsToggle.setAttribute('aria-expanded', String(ouvrir));
        });
    }

    // Page produit : "Afficher les commentaires" ouvre une pop-up modale
    // (même habillage que la connexion) qui liste tous les avis ; la liste
    // défile à l'intérieur de la carte. Fermeture par la croix, le voile
    // sombre ou la touche Échap, avec retour du focus sur le bouton.
    var avisPlus = document.querySelector('.avis-plus');
    var avisPopup = document.getElementById('avis-popup');

    if (avisPlus && avisPopup) {
        var avisPopupFermer = avisPopup.querySelector('.avis-popup__close');

        // « Lire la suite » : n'apparaît que si le texte est réellement
        // tronqué (écrêté à 4 lignes en CSS). Se mesure une fois la pop-up
        // visible, donc appelé à l'ouverture et après un dévoilement.
        var majLireLaSuite = function () {
            avisPopup.querySelectorAll('.avis-popup__liste .commentaire').forEach(function (art) {
                if (art.hidden) {
                    return;
                }
                var texte = art.querySelector('.commentaire__texte');
                var bouton = art.querySelector('.commentaire__lire');
                if (!texte || !bouton || texte.classList.contains('is-ouvert')) {
                    return;
                }
                bouton.hidden = texte.scrollHeight - texte.clientHeight <= 2;
            });
        };

        var ouvrirAvisPopup = function () {
            avisPopup.hidden = false;
            majLireLaSuite();
            if (avisPopupFermer) {
                avisPopupFermer.focus();
            }
        };

        var fermerAvisPopup = function () {
            avisPopup.hidden = true;
            avisPlus.focus();
        };

        avisPlus.addEventListener('click', ouvrirAvisPopup);

        if (avisPopupFermer) {
            avisPopupFermer.addEventListener('click', fermerAvisPopup);
        }

        // Bouton du bas : dévoile les commentaires restants dans la pop-up.
        var avisPopupPlus = avisPopup.querySelector('.avis-popup__plus');
        if (avisPopupPlus) {
            avisPopupPlus.addEventListener('click', function () {
                avisPopup.querySelectorAll('.avis-popup__liste .commentaire--extra').forEach(function (el) {
                    el.hidden = false;
                });
                avisPopupPlus.hidden = true;
                majLireLaSuite();
            });
        }

        // Bascule « Lire la suite » / « Réduire » sur chaque avis.
        avisPopup.querySelectorAll('.commentaire__lire').forEach(function (bouton) {
            bouton.addEventListener('click', function () {
                var texte = bouton.parentElement.querySelector('.commentaire__texte');
                if (!texte) {
                    return;
                }
                var ouvert = texte.classList.toggle('is-ouvert');
                bouton.textContent = ouvert ? 'Réduire' : 'Lire la suite';
            });
        });

        // Clic sur le voile sombre : fermeture
        avisPopup.addEventListener('click', function (event) {
            if (event.target === avisPopup) {
                fermerAvisPopup();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !avisPopup.hidden) {
                fermerAvisPopup();
            }
        });
    }

    // Tranches horaires proposées pour chaque formule, partagées entre
    // le panneau de la fiche produit et la pop-up de la page réservation
    var CRENEAUX_PAR_FORMULE = {
        'heure': ['9h - 10h', '10h - 11h', '11h - 12h', '14h - 15h', '15h - 16h', '16h - 17h', '17h - 18h', '18h - 19h', '19h - 20h'],
        'demi-journee': ['Matin 9h-13h', 'Après-midi 14h-18h', 'Soirée 18h-22h'],
        'journee': ['Journée 9h-17h', 'Journée 10h-18h', 'Journée 12h-20h']
    };

    // Date au format JJ/MM/AAAA
    var formaterDateFr = function (d) {
        return ('0' + d.getDate()).slice(-2) + '/' + ('0' + (d.getMonth() + 1)).slice(-2) + '/' + d.getFullYear();
    };

    // Vraie date JJ/MM/AAAA non passée : les valeurs préremplies des
    // gabarits vieillissent, on les remplace alors par la date du jour
    var dateResaValide = function (valeur) {
        var m = /^(\d{2})\/(\d{2})\/(\d{4})$/.exec(valeur || '');
        if (!m) {
            return false;
        }
        var date = new Date(parseInt(m[3], 10), parseInt(m[2], 10) - 1, parseInt(m[1], 10));
        var aujourdhui = new Date();
        aujourdhui.setHours(0, 0, 0, 0);
        return date.getTime() >= aujourdhui.getTime();
    };

    // Page produit : panneau de réservation. La formule, le créneau et
    // les compteurs de voyageurs pilotent le récapitulatif TTC ;
    // Réserver poursuit vers la page Confirmer et payer avec le choix
    // dans l'URL.
    var resaForm = document.querySelector('.resa');

    if (resaForm) {
        var resaDate = document.getElementById('resa-date');
        var resaCreneauInput = document.getElementById('resa-creneau');
        var formules = Array.prototype.slice.call(resaForm.querySelectorAll('.resa-formule'));
        var steppers = Array.prototype.slice.call(resaForm.querySelectorAll('.resa-stepper'));
        var voyageurs = { adultes: 2, enfants: 2, bebes: 0 };
        // Capacité réelle du bien, posée par le gabarit (repli 6) ;
        // adultes + enfants, les bébés ne comptent pas
        var CAPACITE_PISCINE = parseInt(resaForm.getAttribute('data-capacite'), 10) || 6;
        voyageurs.adultes = Math.min(voyageurs.adultes, CAPACITE_PISCINE);
        voyageurs.enfants = Math.min(voyageurs.enfants, Math.max(0, CAPACITE_PISCINE - voyageurs.adultes));

        var resaLibelle = resaForm.querySelector('[data-resa="libelle"]');
        var resaSousTotal = resaForm.querySelector('[data-resa="sous-total"]');
        var resaFrais = resaForm.querySelector('[data-resa="frais"]');
        var resaTotal = resaForm.querySelector('[data-resa="total"]');
        var resaEcolo = resaForm.querySelector('[data-resa="ecolo"]');
        // Barre de réservation mobile, fixée en bas de l'écran (hors formulaire)
        var resaTotalBarre = document.querySelector('[data-resa="total-barre"]');
        var resaBarreBtn = document.querySelector('.resa-barre__btn');

        // Formate un montant : deux décimales et virgule ("13,50€")
        var formatMontant = function (montant) {
            return montant.toFixed(2).replace('.', ',') + '€';
        };

        // Récapitulatif : la formule à l'heure se calcule par personne,
        // les forfaits demi-journée et journée sont privatifs
        var majRecapResa = function () {
            var formule = resaForm.querySelector('.resa-formule.is-active');
            if (!formule) {
                return;
            }
            var prix = parseFloat(formule.getAttribute('data-prix')) || 0;
            var type = formule.getAttribute('data-formule');
            var occupants = voyageurs.adultes + voyageurs.enfants;
            var sousTotal;

            if (type === 'heure') {
                sousTotal = prix * occupants;
                resaLibelle.textContent = '1 heure x ' + occupants + ' pers.';
            } else {
                sousTotal = prix;
                resaLibelle.textContent = type === 'journee' ? 'Journée privative' : 'Demi-journée privative';
            }

            var frais = sousTotal * 0.15;
            resaSousTotal.textContent = formatMontant(sousTotal);
            resaFrais.textContent = formatMontant(frais);
            resaTotal.textContent = formatMontant(sousTotal + frais);
            resaEcolo.textContent = formatMontant(sousTotal * 0.01);
            if (resaTotalBarre) {
                resaTotalBarre.textContent = formatMontant(sousTotal + frais);
            }
        };

        formules.forEach(function (formule) {
            formule.addEventListener('click', function () {
                formules.forEach(function (autre) {
                    autre.classList.toggle('is-active', autre === formule);
                    autre.setAttribute('aria-pressed', String(autre === formule));
                });
                // La plage de créneaux dépend de la formule : on régénère
                // la liste et on repart sur la première tranche disponible.
                majCreneauxFormule(formule.getAttribute('data-formule'));
                majRecapResa();
            });
        });

        // Compteurs Adultes / Enfants / Bébé : bornes propres à chaque
        // compteur et capacité totale de la piscine
        var majSteppers = function () {
            var occupants = voyageurs.adultes + voyageurs.enfants;
            steppers.forEach(function (stepper) {
                var nom = stepper.getAttribute('data-compte');
                var min = parseInt(stepper.getAttribute('data-min'), 10) || 0;
                var max = parseInt(stepper.getAttribute('data-max'), 10) || 9;

                stepper.querySelector('.resa-stepper__valeur').textContent = String(voyageurs[nom]);
                stepper.querySelector('[data-action="moins"]').disabled = voyageurs[nom] <= min;
                var plein = (nom === 'adultes' || nom === 'enfants') && occupants >= CAPACITE_PISCINE;
                stepper.querySelector('[data-action="plus"]').disabled = voyageurs[nom] >= max || plein;
            });
        };

        steppers.forEach(function (stepper) {
            var nom = stepper.getAttribute('data-compte');
            stepper.querySelector('[data-action="moins"]').addEventListener('click', function () {
                voyageurs[nom] -= 1;
                majSteppers();
                majRecapResa();
            });
            stepper.querySelector('[data-action="plus"]').addEventListener('click', function () {
                voyageurs[nom] += 1;
                majSteppers();
                majRecapResa();
            });
        });

        // Champ Date : le calendrier partagé écrit "15/07/2026, 9h - 12h" ;
        // le créneau a son propre champ, on ne garde donc que la date
        if (resaDate) {
            // La date préremplie du gabarit peut être déjà passée : on
            // repart alors sur la date du jour
            if (!dateResaValide(resaDate.value)) {
                resaDate.value = formaterDateFr(new Date());
            }
            resaDate.addEventListener('change', function () {
                resaDate.value = resaDate.value.split(',')[0];
            });
        }

        // Liste déroulante du créneau horaire. Les tranches proposées
        // suivent la formule choisie : créneaux d'une heure « à l'heure »,
        // demi-journées de 4 h, journées complètes. Le HTML de départ ne
        // porte que les tranches de la demi-journée ; on régénère la liste
        // à chaque changement de formule.
        var creneauChamp = resaForm.querySelector('.resa__creneau');
        var creneauListe = document.getElementById('resa-creneau-liste');

        // Reconstruit la liste déroulante pour la formule donnée. Le
        // paramètre « prefere » permet de conserver la tranche déjà
        // affichée si elle existe encore (au chargement) ; sinon on
        // repositionne sur la première tranche de la formule.
        var majCreneauxFormule = function (type, prefere) {
            if (!creneauListe || !resaCreneauInput) {
                return;
            }
            var creneaux = CRENEAUX_PAR_FORMULE[type] || CRENEAUX_PAR_FORMULE['demi-journee'];
            var actif = prefere ? creneaux.indexOf(prefere) : -1;
            if (actif < 0) {
                actif = 0;
            }
            creneauListe.innerHTML = '';
            creneaux.forEach(function (libelle, index) {
                var li = document.createElement('li');
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item' + (index === actif ? ' is-active' : '');
                btn.textContent = libelle;
                btn.addEventListener('click', function () {
                    resaCreneauInput.value = libelle;
                    creneauListe.querySelectorAll('.dropdown-item').forEach(function (autre) {
                        autre.classList.toggle('is-active', autre === btn);
                    });
                    creneauListe.hidden = true;
                });
                li.appendChild(btn);
                creneauListe.appendChild(li);
            });
            resaCreneauInput.value = creneaux[actif];
        };

        if (creneauChamp && creneauListe && resaCreneauInput) {
            resaCreneauInput.addEventListener('click', function () {
                creneauListe.hidden = !creneauListe.hidden;
            });

            document.addEventListener('click', function (event) {
                if (!creneauListe.hidden && !creneauChamp.contains(event.target)) {
                    creneauListe.hidden = true;
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !creneauListe.hidden) {
                    creneauListe.hidden = true;
                }
            });

            // Créneaux initiaux cohérents avec la formule active au
            // chargement, en gardant la tranche déjà présente dans le champ.
            var formuleInitiale = resaForm.querySelector('.resa-formule.is-active');
            majCreneauxFormule(
                formuleInitiale ? formuleInitiale.getAttribute('data-formule') : 'demi-journee',
                resaCreneauInput.value.trim()
            );
        }

        // La réservation se poursuit sur la page Confirmer et payer :
        // la formule choisie (type et prix), la date, le créneau et les
        // compteurs de voyageurs passent dans l'URL pour préremplir le
        // récapitulatif du paiement.
        resaForm.addEventListener('submit', function (event) {
            event.preventDefault();

            var params = new URLSearchParams();
            // Identifiant du bien réservé (posé par single-bien.php) :
            // la page réservation charge ainsi le bon espace côté serveur
            // et l'annonce nommée dans l'e-mail à l'hôte correspond au bien.
            // On le nomme « bien_id » (et non « bien ») pour ne pas entrer
            // en collision avec le query var réservé du type de contenu
            // « bien », qui ferait renvoyer une 404 à la page réservation.
            var bienId = resaForm.getAttribute('data-bien-id');
            if (bienId) {
                params.set('bien_id', bienId);
            }
            var formuleActive = resaForm.querySelector('.resa-formule.is-active');
            if (formuleActive) {
                params.set('formule', formuleActive.getAttribute('data-formule'));
                params.set('prix', formuleActive.getAttribute('data-prix'));
            }
            if (resaDate && resaDate.value) {
                params.set('date', resaDate.value);
            }
            if (resaCreneauInput && resaCreneauInput.value) {
                params.set('creneau', resaCreneauInput.value);
            }
            params.set('adultes', String(voyageurs.adultes));
            params.set('enfants', String(voyageurs.enfants));
            params.set('bebes', String(voyageurs.bebes));
            // ppData.reservationUrl : permalien WordPress de la page
            // réservation (/reservation/) ; repli sur reservation.html en
            // statique. Sans cette base, le lien menait à une 404.
            var resaBase = (window.ppData && window.ppData.reservationUrl) || 'reservation.html';
            var resaSep = resaBase.indexOf('?') === -1 ? '?' : '&';
            window.location.href = resaBase + resaSep + params.toString();
        });

        // Le bouton Réserver de la barre mobile déclenche la même
        // soumission que celui du panneau : les choix en cours (formule,
        // date, créneau, voyageurs) partent vers la page de réservation
        if (resaBarreBtn) {
            resaBarreBtn.addEventListener('click', function () {
                if (typeof resaForm.requestSubmit === 'function') {
                    resaForm.requestSubmit();
                } else {
                    resaForm.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
                }
            });
        }

        // État initial : compteurs synchronisés et récapitulatif calculé
        majSteppers();
        majRecapResa();
    }

    // Page réservation (Confirmer et payer) : récapitulatif vivant,
    // pop-ups de modification du créneau et des invités, garantie
    // annulation en option et choix du mode de paiement.
    var checkoutForm = document.querySelector('.checkout');

    if (checkoutForm) {
        var ckPrixGarantie = parseFloat(checkoutForm.getAttribute('data-garantie')) || 0;
        // Capacité réelle du bien, posée par le gabarit (repli 6) ;
        // adultes + enfants, les bébés ne comptent pas
        var CAPACITE_MAX = parseInt(checkoutForm.getAttribute('data-capacite'), 10) || 6;

        // Mêmes formules que le panneau de la page produit : la formule
        // à l'heure se calcule par personne, les forfaits sont privatifs
        var FORMULES = {
            'heure': { libelle: '1 heure', parPersonne: true, prix: 8 },
            'demi-journee': { libelle: 'Demi-journée privative', prix: 90 },
            'journee': { libelle: 'Journée privative', prix: 170 }
        };
        var formuleType = 'demi-journee';

        // Raccourci : tous les éléments marqués data-checkout de la page
        var ck = function (nom) {
            return document.querySelector('[data-checkout="' + nom + '"]');
        };

        var champDate = document.getElementById('checkout-date');
        var champCreneau = document.getElementById('checkout-creneau');
        var garantieBtn = ck('garantie-btn');
        var garantieActive = false;

        // Réservation réservée aux membres : les coordonnées viennent du
        // compte (identité mémorisée à la connexion / inscription). On
        // remplit le résumé et on adapte le bouton d'envoi selon l'état ;
        // la bascule des blocs visiteur / connecté se fait en CSS via la
        // classe is-connected du body.
        var lireIdentite = function () {
            var id = { prenom: '', email: '' };
            try {
                id.prenom = sessionStorage.getItem('pp-prenom') || '';
                id.email = sessionStorage.getItem('pp-email') || '';
            } catch (erreur) {}
            return id;
        };

        var majEtatCompte = function () {
            var connecte = document.body.classList.contains('is-connected');
            var identite = lireIdentite();
            var resume = ck('identite');
            if (resume) {
                if (identite.prenom && identite.email) {
                    resume.textContent = identite.prenom + ' · ' + identite.email;
                } else {
                    resume.textContent = identite.email || identite.prenom || 'Connecté à votre compte';
                }
            }
            var boutonSubmit = checkoutForm.querySelector('.checkout-submit');
            if (boutonSubmit && !boutonSubmit.disabled) {
                boutonSubmit.textContent = connecte ? 'Demander la réservation' : 'Se connecter pour réserver';
            }
        };

        majEtatCompte();
        document.addEventListener('pp-connexion', majEtatCompte);
        document.addEventListener('pp-deconnexion', majEtatCompte);

        // Compteurs de la pop-up Invités : mêmes valeurs de départ que
        // le panneau de la page produit
        var invites = { adultes: 2, enfants: 2, bebes: 0 };

        // Formate un montant : deux décimales et virgule ("103,50€"),
        // comme le récapitulatif de la page produit
        var formatMontant = function (montant) {
            return montant.toFixed(2).replace('.', ',') + '€';
        };

        // Recalcule toutes les sommes affichées : détail du prix du
        // récapitulatif et montants des deux échéances de paiement
        var majTotaux = function () {
            var formule = FORMULES[formuleType];
            var occupants = invites.adultes + invites.enfants;
            var sousTotal = formule.parPersonne ? formule.prix * occupants : formule.prix;
            var frais = sousTotal * 0.15;
            var total = sousTotal + frais + (garantieActive ? ckPrixGarantie : 0);

            ck('libelle').textContent = formule.parPersonne
                ? formule.libelle + ' x ' + occupants + ' pers.'
                : formule.libelle;
            ck('sous-total').textContent = formatMontant(sousTotal);
            ck('frais').textContent = formatMontant(frais);
            ck('ligne-garantie').hidden = !garantieActive;
            ck('total').textContent = formatMontant(total);
            ck('ecolo').textContent = formatMontant(sousTotal * 0.01);
            ck('montant-comptant').textContent = formatMontant(total);
            // Trois versements à somme exacte : les deux derniers sont
            // arrondis au centime, le premier absorbe la différence
            var tiers = Math.round((total / 3) * 100) / 100;
            ck('montant-tiers').textContent = formatMontant(tiers);
            ck('montant-premier').textContent = formatMontant(Math.round((total - 2 * tiers) * 100) / 100);
        };

        // Le récapitulatif affiche la date et le créneau choisis
        var majRecapDate = function () {
            var date = champDate ? champDate.value : '';
            var creneau = champCreneau ? champCreneau.value : '';
            ck('recap-date').textContent = date + (creneau ? ', ' + creneau : '');
        };

        // Libellé du récapitulatif : "2 adultes, 1 enfant, 1 bébé"
        var majRecapInvites = function () {
            var parts = [invites.adultes + (invites.adultes > 1 ? ' adultes' : ' adulte')];
            if (invites.enfants > 0) {
                parts.push(invites.enfants + (invites.enfants > 1 ? ' enfants' : ' enfant'));
            }
            if (invites.bebes > 0) {
                parts.push(invites.bebes + (invites.bebes > 1 ? ' bébés' : ' bébé'));
            }
            ck('recap-invites').textContent = parts.join(', ');
        };

        // Préremplissage depuis l'URL : le panneau de la page produit
        // transmet la formule, la date, le créneau et les voyageurs
        var paramsUrl = new URLSearchParams(window.location.search);
        if (paramsUrl.get('formule') && FORMULES[paramsUrl.get('formule')]) {
            formuleType = paramsUrl.get('formule');
            // Le prix n'est accepté qu'avec une formule explicite et
            // dans une fourchette plausible (il part dans la demande
            // envoyée à l'hôte)
            var prixUrl = parseFloat(paramsUrl.get('prix'));
            if (!isNaN(prixUrl) && prixUrl >= 1 && prixUrl <= 500) {
                FORMULES[formuleType].prix = prixUrl;
            }
        }
        // La date reçue doit être bien formée ET non passée
        if (champDate && dateResaValide(paramsUrl.get('date'))) {
            champDate.value = paramsUrl.get('date');
        }
        if (champCreneau && paramsUrl.get('creneau')) {
            champCreneau.value = paramsUrl.get('creneau');
        }
        ['adultes', 'enfants', 'bebes'].forEach(function (nom) {
            var valeur = parseInt(paramsUrl.get(nom), 10);
            if (!isNaN(valeur) && valeur >= 0) {
                invites[nom] = valeur;
            }
        });
        // Au moins un adulte, et les plafonds des compteurs (data-max)
        // s'appliquent aussi aux valeurs venues de l'URL
        invites.adultes = Math.min(Math.max(1, invites.adultes), CAPACITE_MAX);
        invites.enfants = Math.min(invites.enfants, Math.max(0, CAPACITE_MAX - invites.adultes));
        invites.bebes = Math.min(invites.bebes, 4);

        // Le calendrier partagé écrit "15/07/2026, 9h - 12h" dans le
        // champ Date ; le créneau a sa propre liste, on ne garde que la date
        if (champDate) {
            // La date préremplie du gabarit peut être déjà passée : on
            // repart alors sur la date du jour
            if (!dateResaValide(champDate.value)) {
                champDate.value = formaterDateFr(new Date());
            }
            champDate.addEventListener('change', function () {
                champDate.value = champDate.value.split(',')[0];
            });
        }

        // Liste déroulante du créneau dans la pop-up Dates : même
        // mécanique que la pilule de la page produit
        var creneauPill = document.querySelector('.checkout-creneau__pill');
        var creneauListeCk = document.getElementById('checkout-creneau-liste');

        if (creneauPill && creneauListeCk && champCreneau) {
            // Le HTML de départ ne porte que les tranches demi-journée :
            // la liste est régénérée selon la formule reçue dans l'URL,
            // comme sur la fiche produit. Un créneau étranger à la
            // formule (URL modifiée) retombe sur la première tranche.
            var creneauxCk = CRENEAUX_PAR_FORMULE[formuleType] || CRENEAUX_PAR_FORMULE['demi-journee'];
            if (creneauxCk.indexOf(champCreneau.value) === -1) {
                champCreneau.value = creneauxCk[0];
            }
            creneauListeCk.innerHTML = '';
            creneauxCk.forEach(function (libelle) {
                var li = document.createElement('li');
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-item' + (libelle === champCreneau.value ? ' is-active' : '');
                btn.textContent = libelle;
                btn.addEventListener('click', function () {
                    champCreneau.value = libelle;
                    creneauListeCk.querySelectorAll('.dropdown-item').forEach(function (autre) {
                        autre.classList.toggle('is-active', autre === btn);
                    });
                    creneauListeCk.hidden = true;
                    creneauPill.classList.remove('is-open');
                });
                li.appendChild(btn);
                creneauListeCk.appendChild(li);
            });

            creneauPill.addEventListener('click', function () {
                var ouvrir = creneauListeCk.hidden;
                creneauListeCk.hidden = !ouvrir;
                creneauPill.classList.toggle('is-open', ouvrir);
            });

            document.addEventListener('click', function (event) {
                if (!creneauListeCk.hidden && !creneauPill.contains(event.target) && !creneauListeCk.contains(event.target)) {
                    creneauListeCk.hidden = true;
                    creneauPill.classList.remove('is-open');
                }
            });
        }

        // ---- Pop-ups (dates et invités) --------------------------------
        // Ouverture par les boutons Modifier, fermeture par la croix,
        // Annuler, un clic sur le fond ou Échap. Enregistrer reporte le
        // choix dans le récapitulatif ; toute fermeture sans enregistrer
        // restaure l'état précédent.
        var etatAvantPopup = null;

        var compteurs = Array.prototype.slice.call(document.querySelectorAll('#popup-invites .compteur'));

        // Active ou grise les boutons + et - selon les bornes de chaque
        // compteur et la capacité totale de l'espace
        var majCompteurs = function () {
            var occupants = invites.adultes + invites.enfants;
            compteurs.forEach(function (compteur) {
                var nom = compteur.getAttribute('data-compteur');
                var min = parseInt(compteur.getAttribute('data-min'), 10) || 0;
                var max = parseInt(compteur.getAttribute('data-max'), 10) || 99;
                var valeur = invites[nom];

                compteur.querySelector('.compteur__valeur').textContent = String(valeur);
                compteur.querySelector('[data-action="moins"]').disabled = valeur <= min;
                var plein = (nom === 'adultes' || nom === 'enfants') && occupants >= CAPACITE_MAX;
                compteur.querySelector('[data-action="plus"]').disabled = valeur >= max || plein;
            });
        };

        compteurs.forEach(function (compteur) {
            var nom = compteur.getAttribute('data-compteur');
            compteur.querySelector('[data-action="moins"]').addEventListener('click', function () {
                invites[nom] -= 1;
                majCompteurs();
            });
            compteur.querySelector('[data-action="plus"]').addEventListener('click', function () {
                invites[nom] += 1;
                majCompteurs();
            });
        });

        var ouvrirPopup = function (overlay) {
            // Mémorise l'état courant pour pouvoir annuler
            etatAvantPopup = {
                date: champDate ? champDate.value : '',
                creneau: champCreneau ? champCreneau.value : '',
                invites: { adultes: invites.adultes, enfants: invites.enfants, bebes: invites.bebes }
            };
            overlay.hidden = false;
            majCompteurs();
        };

        var fermerPopup = function (overlay, enregistrer) {
            if (!enregistrer && etatAvantPopup) {
                // Fermeture sans enregistrer : on restaure l'état mémorisé
                if (champDate) {
                    champDate.value = etatAvantPopup.date;
                }
                if (champCreneau) {
                    champCreneau.value = etatAvantPopup.creneau;
                }
                invites = etatAvantPopup.invites;
            }
            if (enregistrer) {
                if (overlay.id === 'popup-dates') {
                    majRecapDate();
                }
                if (overlay.id === 'popup-invites') {
                    majRecapInvites();
                    // La formule à l'heure se recalcule par personne
                    majTotaux();
                }
            }
            // Referme aussi le calendrier resté ouvert dans la pop-up
            overlay.querySelectorAll('.calendar').forEach(function (calendrier) {
                calendrier.hidden = true;
            });
            overlay.hidden = true;
            etatAvantPopup = null;
        };

        document.querySelectorAll('[data-popup-ouvrir]').forEach(function (bouton) {
            bouton.addEventListener('click', function () {
                var overlay = document.getElementById(bouton.getAttribute('data-popup-ouvrir'));
                if (overlay) {
                    ouvrirPopup(overlay);
                }
            });
        });

        document.querySelectorAll('.popup-overlay').forEach(function (overlay) {
            overlay.querySelectorAll('[data-popup-fermer]').forEach(function (bouton) {
                bouton.addEventListener('click', function () {
                    fermerPopup(overlay, false);
                });
            });

            overlay.querySelectorAll('[data-popup-valider]').forEach(function (bouton) {
                bouton.addEventListener('click', function () {
                    fermerPopup(overlay, true);
                });
            });

            // Clic sur le fond sombre : fermeture sans enregistrer
            overlay.addEventListener('click', function (event) {
                if (event.target === overlay) {
                    fermerPopup(overlay, false);
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }
            document.querySelectorAll('.popup-overlay').forEach(function (overlay) {
                if (!overlay.hidden) {
                    fermerPopup(overlay, false);
                }
            });
        });

        // ---- Garantie annulation ---------------------------------------
        if (garantieBtn) {
            garantieBtn.addEventListener('click', function () {
                garantieActive = !garantieActive;
                garantieBtn.classList.toggle('is-active', garantieActive);
                garantieBtn.setAttribute('aria-pressed', String(garantieActive));
                garantieBtn.textContent = garantieActive ? 'Retirer' : 'Ajouter';
                majTotaux();
            });
        }

        // ---- Mode de paiement ------------------------------------------
        // Les champs de carte ne servent à rien quand PayPal est choisi
        var champsCarte = ck('champs-carte');
        document.querySelectorAll('input[name="paiement"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (champsCarte) {
                    champsCarte.hidden = radio.value !== 'carte' && radio.checked;
                }
            });
        });

        // ---- Envoi de la demande de réservation --------------------------
        // Modèle Airbnb : la soumission n'est pas une confirmation mais une
        // demande. Un e-mail est réellement envoyé à l'hôte (via le service
        // FormSubmit, sans backend) pour qu'il confirme la disponibilité de
        // son espace ; le locataire est prévenu qu'il ne sera débité qu'après
        // cette confirmation. Le site restant fictif, l'écran de demande
        // envoyée s'affiche même si le service est injoignable.
        // Alias FormSubmit de l'adresse de l'hôte : évite d'exposer
        // l'adresse e-mail en clair dans le code source publié.
        var HOTE_EMAIL_ENDPOINT = 'https://formsubmit.co/ajax/26aa38cbbdb858a800ab9b41ca816ab2';

        checkoutForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // Réservation réservée aux membres : sans compte on n'envoie
            // rien, on ouvre la fenêtre de connexion. La demande repart
            // toute seule une fois connecté (nouveau clic sur le bouton).
            if (!document.body.classList.contains('is-connected')) {
                if (typeof ouvrirConnexion === 'function') {
                    ouvrirConnexion();
                }
                return;
            }

            var bouton = checkoutForm.querySelector('.checkout-submit');
            var note = checkoutForm.querySelector('.checkout-note');
            var identite = lireIdentite();
            var message = document.getElementById('checkout-message');
            var echeance = checkoutForm.querySelector('input[name="echeance"]:checked');
            var paiement = checkoutForm.querySelector('input[name="paiement"]:checked');
            var garantieBtn = ck('garantie-btn');

            if (bouton) {
                bouton.disabled = true;
                bouton.textContent = 'Envoi de la demande...';
            }

            // Contenu de l'e-mail reçu par l'hôte : le récapitulatif complet
            // de la demande, avec l'adresse du locataire (issue du compte)
            // en répondre à.
            var demande = {
                _subject: 'Pool Party : nouvelle demande de réservation de ' + identite.prenom,
                _template: 'table',
                _captcha: 'false',
                email: identite.email,
                prenom: identite.prenom,
                // Nom de l'espace réservé, posé par page-reservation.php
                // (data-annonce) d'après le bien transmis dans l'URL ;
                // repli générique si la page est ouverte sans bien.
                annonce: checkoutForm.getAttribute('data-annonce') || 'Espace Pool Party',
                date_et_creneau: ck('recap-date').textContent,
                invites: ck('recap-invites').textContent,
                formule: ck('libelle').textContent,
                total: ck('total').textContent,
                echeance: echeance && echeance.value === 'trois-fois' ? 'Paiement en 3 fois' : 'Paiement comptant',
                mode_de_paiement: paiement && paiement.value === 'paypal' ? 'PayPal' : 'Carte bancaire',
                garantie_annulation: garantieBtn && garantieBtn.getAttribute('aria-pressed') === 'true' ? 'Oui' : 'Non',
                message_pour_l_hote: message && message.value ? message.value : '(aucun message)'
            };

            var afficherDemandeEnvoyee = function () {
                var hote = checkoutForm.getAttribute('data-hote') || 'L\'hôte';

                // Mémorise la demande pour la page Mes réservations : elle
                // y apparaît « en attente de confirmation » (modèle Airbnb).
                // Les informations sont relues du récapitulatif à l'écran.
                var media = document.querySelector('.recap__annonce img');
                var lienAnnonce = document.querySelector('.checkout-retour');
                enregistrerReservation({
                    id: 'r' + Date.now(),
                    titre: checkoutForm.getAttribute('data-annonce') || 'Espace Pool Party',
                    image: media ? media.getAttribute('src') : '',
                    alt: media ? media.getAttribute('alt') : '',
                    lien: lienAnnonce ? lienAnnonce.getAttribute('href') : '',
                    hote: hote,
                    date: champDate ? champDate.value : '',
                    creneau: champCreneau ? champCreneau.value : '',
                    invites: ck('recap-invites') ? ck('recap-invites').textContent : '',
                    formule: ck('libelle') ? ck('libelle').textContent : '',
                    total: ck('total') ? ck('total').textContent : '',
                    statut: 'en-attente'
                });

                var messageEnvoye = hote + ' vient de recevoir un e-mail pour confirmer la disponibilité de son espace le ' +
                    ck('recap-date').textContent + '. Sa réponse vous parviendra sous 24h maximum et vous ne serez débité qu\'après sa confirmation.';

                // Verrouille le formulaire : la demande est partie, on
                // empêche un second envoi et on l'indique sur le bouton.
                if (bouton) {
                    bouton.disabled = true;
                    bouton.textContent = 'Demande envoyée';
                }

                // Confirmation dans une pop-up modale (voir page-reservation.php).
                var popupConfirmation = document.getElementById('popup-confirmation');
                var texteConfirmation = ck('confirmation-texte');
                if (popupConfirmation && texteConfirmation) {
                    texteConfirmation.textContent = messageEnvoye;
                    popupConfirmation.hidden = false;
                    ppTrackGA('reservation_complete', { page: window.location.pathname });
                    var actionConfirmation = popupConfirmation.querySelector('.popup__actions .btn-primary');
                    if (actionConfirmation) {
                        actionConfirmation.focus();
                    }
                } else {
                    // Repli si la pop-up est absente : message en ligne.
                    var confirmation = document.createElement('p');
                    confirmation.className = 'checkout-confirmation';
                    confirmation.setAttribute('role', 'status');
                    confirmation.textContent = 'Demande envoyée ! ' + messageEnvoye;
                    if (bouton) {
                        bouton.replaceWith(confirmation);
                    }
                    if (note) {
                        note.remove();
                    }
                }
            };

            // L'e-mail à l'hôte part en arrière-plan (FormSubmit) : on ne
            // bloque plus la confirmation sur la réponse du service, qui
            // peut tarder. La demande est déjà mémorisée ; l'écran de
            // confirmation apparaît juste après un court délai, le temps
            // que le bouton « Envoi de la demande... » soit perçu.
            fetch(HOTE_EMAIL_ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(demande)
            }).catch(function (erreur) {
                console.warn('Envoi de l\'e-mail à l\'hôte impossible :', erreur);
            });

            setTimeout(afficherDemandeEnvoyee, 350);
        });

        // État initial : récapitulatif calculé et libellés synchronisés
        majTotaux();
        majRecapDate();
        majRecapInvites();
    }

    // Page contact : pilules select (profil et objet de la demande).
    // Le clic ouvre la liste sous la pilule ; choisir un item l'écrit
    // dans le champ, comme la pilule de tri de la page catégorie.
    document.querySelectorAll('.contact-select').forEach(function (select) {
        var pill = select.querySelector('.contact-select__pill');
        var input = pill.querySelector('input');
        var chevron = pill.querySelector('.input-search__chevron');
        var liste = select.querySelector('.contact-select__liste');
        var items = Array.prototype.slice.call(liste.querySelectorAll('.dropdown-item'));

        var fermerSelect = function () {
            liste.hidden = true;
            pill.classList.remove('is-open');
            chevron.setAttribute('aria-expanded', 'false');
        };

        pill.addEventListener('click', function () {
            var ouvrir = liste.hidden;
            liste.hidden = !ouvrir;
            pill.classList.toggle('is-open', ouvrir);
            chevron.setAttribute('aria-expanded', String(ouvrir));
        });

        items.forEach(function (item) {
            item.addEventListener('click', function () {
                input.value = item.textContent.trim();
                items.forEach(function (autre) {
                    autre.classList.toggle('is-active', autre === item);
                });
                fermerSelect();
            });
        });

        // Fermeture au clic ailleurs sur la page ou avec Échap
        document.addEventListener('click', function (event) {
            if (!liste.hidden && !select.contains(event.target)) {
                fermerSelect();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !liste.hidden) {
                fermerSelect();
            }
        });
    });

    // Page contact : compteur de caractères du message (0/1000)
    var contactMessage = document.getElementById('contact-message');
    var contactCompteur = document.querySelector('.contact-form__compteur');

    if (contactMessage && contactCompteur) {
        var majCompteur = function () {
            contactCompteur.textContent = contactMessage.value.length + '/' +
                contactMessage.maxLength + ' caractères';
        };
        contactMessage.addEventListener('input', majCompteur);
        majCompteur();
    }

    // Page contact : le nom du fichier choisi remplace le libellé
    // du lien Joindre un fichier
    var contactFichier = document.querySelector('.contact-form__fichier input[type="file"]');
    var contactFichierNom = document.querySelector('.contact-form__fichier-nom');

    if (contactFichier && contactFichierNom) {
        var libelleFichier = contactFichierNom.textContent;
        contactFichier.addEventListener('change', function () {
            contactFichierNom.textContent = contactFichier.files.length
                ? contactFichier.files[0].name
                : libelleFichier;
        });
    }

    // Pages contact et FAQ : accordéon des questions fréquentes.
    // Chaque question ouvre ou referme sa réponse indépendamment.
    document.querySelectorAll('.faq-item__question button').forEach(function (bouton) {
        bouton.addEventListener('click', function () {
            var reponse = document.getElementById(bouton.getAttribute('aria-controls'));
            var ouvert = bouton.getAttribute('aria-expanded') === 'true';
            bouton.setAttribute('aria-expanded', String(!ouvert));
            if (reponse) {
                reponse.hidden = ouvert;
            }
        });
    });

    // Page FAQ : recherche instantanée dans les questions.
    // Le champ filtre les items par question et réponse, masque les
    // thèmes vides, ouvre les correspondances et affiche un message
    // quand rien ne correspond. La comparaison ignore les accents.
    var faqRecherche = document.getElementById('faq-recherche');

    if (faqRecherche) {
        var faqSections = document.querySelectorAll('.faq-section');
        var faqVide = document.getElementById('faq-vide');

        var normaliser = function (texte) {
            return texte.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        };

        var basculerItem = function (item, ouvrir) {
            var bouton = item.querySelector('.faq-item__question button');
            var reponse = document.getElementById(bouton.getAttribute('aria-controls'));
            bouton.setAttribute('aria-expanded', String(ouvrir));
            if (reponse) {
                reponse.hidden = !ouvrir;
            }
        };

        var filtrerFaq = function () {
            var requete = normaliser(faqRecherche.value.trim());
            var totalVisibles = 0;

            faqSections.forEach(function (section) {
                var visiblesSection = 0;

                section.querySelectorAll('.faq-item').forEach(function (item) {
                    var correspond = requete === '' || normaliser(item.textContent).indexOf(requete) !== -1;
                    item.hidden = !correspond;
                    if (correspond) {
                        visiblesSection += 1;
                        // Les réponses trouvées s'ouvrent pendant la recherche
                        // et se referment quand le champ se vide
                        basculerItem(item, requete !== '');
                    }
                });

                section.hidden = visiblesSection === 0;
                totalVisibles += visiblesSection;
            });

            if (faqVide) {
                faqVide.hidden = totalVisibles > 0;
            }
        };

        faqRecherche.addEventListener('input', filtrerFaq);

        // Le filtrage est instantané : la soumission ne recharge pas la page
        faqRecherche.closest('form').addEventListener('submit', function (event) {
            event.preventDefault();
            filtrerFaq();
        });
    }

    // Page contact : pas d'envoi réel (projet fictif), une confirmation
    // remplace le formulaire une fois les champs requis validés.
    var contactForm = document.querySelector('.contact-form');

    if (contactForm) {
        contactForm.addEventListener('submit', function (event) {
            event.preventDefault();

            var confirmation = document.createElement('p');
            confirmation.className = 'contact-form__confirmation';
            confirmation.setAttribute('role', 'status');
            confirmation.textContent = 'Merci pour votre message ! Notre équipe vous répond sous 24 heures.';
            contactForm.replaceWith(confirmation);
        });
    }

    // Page devenir partenaire : aucun envoi réel, la soumission de la
    // candidature remplace le formulaire par une confirmation.
    var partenaireForm = document.querySelector('.partenaire-form');

    if (partenaireForm) {
        partenaireForm.addEventListener('submit', function (event) {
            event.preventDefault();

            var confirmation = document.createElement('p');
            confirmation.className = 'partenaire-form__confirmation';
            confirmation.setAttribute('role', 'status');
            confirmation.textContent = 'Merci pour votre candidature ! Votre interlocuteur dédié vous recontacte sous 48 heures ouvrées.';
            partenaireForm.replaceWith(confirmation);
        });
    }

    // Page proposer : parcours de création d'annonce (modèle Airbnb).
    // Un écran par question ; la barre du bas porte le cycle de vie
    // des 3 étapes (une barre de progression par étape) et la
    // navigation Retour / Suivant. Le bouton Suivant reste grisé tant
    // que l'écran courant n'est pas valide.
    var parcours = document.getElementById('proposer-parcours');

    if (parcours) {
        var ecrans = Array.prototype.slice.call(parcours.querySelectorAll('.proposer-ecran'));
        var navRetour = document.querySelector('[data-nav="retour"]');
        var navSuivant = document.querySelector('[data-nav="suivant"]');
        var indexCourant = 0;
        // Écran le plus loin déjà atteint : on ne peut sauter (via le
        // cycle de vie) qu'à une étape déjà débloquée, pas en avant
        var maxAtteint = 0;

        // Cycle de vie regroupé : le tunnel est découpé en grandes étapes,
        // chacune rassemblant plusieurs écrans qui défilent sur la même page.
        // indexCourant est l'index du GROUPE courant (et groupes.length = état
        // publié, l'écran de confirmation). Le rail (une pastille par étape)
        // est construit ici puis mis à jour à chaque changement de groupe.
        var stepperEl = document.getElementById('pp-steps');
        var groupes = [
            { id: 'compte',    label: 'Compte',              ecrans: ['compte'] },
            { id: 'espace',    label: 'Votre espace',        ecrans: ['type', 'acces', 'adresse', 'capacite'] },
            { id: 'annonce',   label: 'Votre annonce',       ecrans: ['equipements', 'photos', 'titre', 'description'] },
            { id: 'offre',     label: 'Réservation & tarif', ecrans: ['reservation', 'disponibilites', 'prix', 'regles'] },
            { id: 'versement', label: 'Versement',           ecrans: ['versement'] },
            { id: 'apercu',    label: 'Aperçu',              ecrans: ['recap'] }
        ];

        if (stepperEl) {
            groupes.forEach(function (groupe, i) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'pp-step';
                btn.innerHTML =
                    '<span class="pp-step__marker">' +
                        '<span class="pp-step__num">' + (i + 1) + '</span>' +
                        '<svg class="pp-step__check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>' +
                    '</span>' +
                    '<span class="pp-step__label">' + groupe.label + '</span>';
                btn.addEventListener('click', function () {
                    // On ne saute qu'à une étape déjà atteinte
                    if (i <= maxAtteint) {
                        afficherGroupe(i);
                        if (bandeauReprise) {
                            bandeauReprise.hidden = true;
                        }
                    }
                });
                groupe.el = btn;
                stepperEl.appendChild(btn);
            });
        }

        // Met à jour l'état du cycle de vie : étapes passées cochées,
        // étape courante en avant, étapes non atteintes non cliquables.
        var majStepper = function () {
            groupes.forEach(function (groupe, i) {
                if (!groupe.el) {
                    return;
                }
                groupe.el.classList.toggle('is-done', i < indexCourant);
                groupe.el.classList.toggle('is-active', i === indexCourant);
                groupe.el.disabled = i > maxAtteint;
                groupe.el.setAttribute('aria-current', i === indexCourant ? 'step' : 'false');
            });
            // Garde l'étape courante visible dans le rail (le scroll de la
            // page est de toute façon remis en haut juste après)
            var courante = groupes[Math.min(indexCourant, groupes.length - 1)];
            if (courante && courante.el) {
                courante.el.scrollIntoView({ block: 'nearest', inline: 'center' });
            }
        };

        // Un groupe est franchissable quand tous ses écrans sont valides
        var groupeValide = function (groupe) {
            return groupe.ecrans.every(function (nom) {
                return ecranEstValide(nom);
            });
        };
        // Écran compte : le remplissage automatique du navigateur ne
        // déclenche pas toujours l'événement input, on revalide donc
        // périodiquement tant que cet écran est affiché
        var compteVeille = null;
        var compteTouche = false;

        // État de l'annonce, rempli au fil des écrans puis relu par
        // le récapitulatif. Aucune donnée n'est envoyée (projet fictif).
        var annonce = {
            choix: {},
            multi: {},
            compteurs: {},
            photos: []
        };

        // --- Validation écran par écran : conditionne le bouton Suivant
        var champCompte = {
            prenom: document.getElementById('compte-prenom'),
            nom: document.getElementById('compte-nom'),
            email: document.getElementById('compte-email'),
            password: document.getElementById('compte-password'),
            cgu: parcours.querySelector('[data-ecran="compte"] input[name="cgu"]')
        };
        var champAdresse = {
            voie: document.getElementById('adresse-voie'),
            cp: document.getElementById('adresse-cp'),
            commune: document.getElementById('adresse-commune')
        };
        var champTitre = document.getElementById('annonce-titre');
        var champDescription = document.getElementById('annonce-description');
        var champPrix = document.getElementById('annonce-prix');
        var attestation = document.getElementById('attestation-securite');
        var champVersement = {
            titulaire: document.getElementById('versement-titulaire'),
            iban: document.getElementById('versement-iban')
        };

        var ecranEstValide = function (nom) {
            switch (nom) {
                case 'compte':
                    return Boolean(champCompte.prenom.value.trim() &&
                        champCompte.nom.value.trim() &&
                        champCompte.email.value.indexOf('@') > 0 &&
                        champCompte.password.value.length >= 8 &&
                        champCompte.cgu.checked);
                case 'type':
                case 'acces':
                case 'reservation':
                    return Boolean(annonce.choix[nom]);
                case 'adresse':
                    return Boolean(champAdresse.voie.value.trim() &&
                        champAdresse.cp.value.trim() &&
                        champAdresse.commune.value.trim());
                case 'photos':
                    return annonce.photos.length >= 5;
                case 'titre':
                    return Boolean(champTitre.value.trim());
                case 'description':
                    return Boolean(champDescription.value.trim());
                case 'prix':
                    return parseInt(champPrix.value, 10) >= 1;
                case 'regles':
                    return attestation.checked;
                case 'disponibilites':
                    return (annonce.multi.jours || []).length >= 1;
                case 'versement':
                    return Boolean(champVersement.titulaire.value.trim() &&
                        champVersement.iban.value.replace(/\s/g, '').length >= 15);
                default:
                    // Écrans d'introduction, capacité, équipements, récap
                    return true;
            }
        };

        var libelleGroupe = function (i) {
            if (groupes[i].id === 'apercu') {
                return 'Publier mon annonce';
            }
            return 'Continuer';
        };

        // Liste ce qui empêche de quitter l'écran compte, pour l'afficher
        // plutôt que de griser le bouton sans explication
        var messageManque = parcours.querySelector('[data-manque]');

        // Sur l'écran compte, le bouton Continuer vit DANS la carte (plus
        // visible au ras du formulaire) ; celui de la barre du bas est alors
        // masqué. Ce bouton pilote la même navigation que le tunnel.
        var boutonCarteCompte = parcours.querySelector('[data-nav="suivant-carte"]');

        var manquesCompte = function () {
            var manques = [];
            if (!champCompte.prenom.value.trim()) {
                manques.push('votre prénom');
            }
            if (!champCompte.nom.value.trim()) {
                manques.push('votre nom');
            }
            if (champCompte.email.value.indexOf('@') <= 0) {
                manques.push('une adresse e-mail valide');
            }
            if (champCompte.password.value.length < 8) {
                manques.push('un mot de passe de 8 caractères minimum');
            }
            if (!champCompte.cgu.checked) {
                manques.push('la case des conditions d\'utilisation');
            }
            return manques;
        };

        // L'utilisateur est engagé dès qu'un champ porte une valeur,
        // qu'elle vienne du clavier ou du remplissage automatique du
        // navigateur (qui ne déclenche pas toujours d'événement)
        var compteEngage = function () {
            return compteTouche ||
                Boolean(champCompte.prenom.value ||
                    champCompte.nom.value ||
                    champCompte.email.value ||
                    champCompte.password.value ||
                    champCompte.cgu.checked);
        };

        var majNavigation = function () {
            var estFin = indexCourant === groupes.length;
            var estCompte = !estFin && groupes[indexCourant].id === 'compte';

            navRetour.hidden = indexCourant === 0 || estFin;
            // Sur l'étape Compte, le Continuer de la barre est masqué au profit
            // de celui de la carte (l'attribut hidden garde sa place, la mention
            // reste centrée)
            navSuivant.hidden = estFin || estCompte;
            if (!estFin) {
                navSuivant.textContent = libelleGroupe(indexCourant);
                navSuivant.disabled = !groupeValide(groupes[indexCourant]);
            }

            if (boutonCarteCompte) {
                boutonCarteCompte.disabled = !groupeValide(groupes[0]);
            }

            if (messageManque) {
                if (estCompte && !groupeValide(groupes[0])) {
                    var manques = manquesCompte();
                    // Dès l'arrivée (formulaire vierge) : invitation neutre en
                    // gris ; une fois qu'un champ est renseigné : liste corail
                    // de ce qui manque
                    var detaille = compteEngage() && manques.length;
                    messageManque.hidden = false;
                    messageManque.classList.toggle('proposer-manque--info', !detaille);
                    messageManque.textContent = detaille
                        ? 'Pour continuer, il manque : ' + manques.join(', ') + '.'
                        : 'Complétez le formulaire pour continuer.';
                } else {
                    messageManque.hidden = true;
                    messageManque.classList.remove('proposer-manque--info');
                    messageManque.textContent = '';
                }
            }

            // Le champ mot de passe se signale dès qu'il est trop court
            if (estCompte) {
                champCompte.password.classList.toggle('has-error',
                    champCompte.password.value !== '' &&
                    champCompte.password.value.length < 8);
            }
        };

        // Le récapitulatif relit l'état de l'annonce juste avant affichage
        var remplirRecap = function () {
            var lireRecap = function (cle) {
                return parcours.querySelector('[data-recap="' + cle + '"]');
            };
            var prixHeure = parseInt(champPrix.value, 10) || 0;

            lireRecap('titre').textContent = champTitre.value.trim() || 'Votre annonce';
            lireRecap('commune').textContent = champAdresse.commune.value.trim() || 'Votre commune';
            lireRecap('type').textContent = annonce.choix.type || 'Votre espace';
            lireRecap('invites').textContent = (annonce.compteurs.invites || 8) + ' personnes max';
            lireRecap('prix').textContent = Math.round(prixHeure * 1.15) + ' €/ h';

            if (annonce.photos.length) {
                lireRecap('photo').src = annonce.photos[0].url;
            }
        };

        var afficherGroupe = function (index) {
            // groupes.length = état publié (on affiche alors l'écran de fin)
            indexCourant = Math.min(Math.max(index, 0), groupes.length);
            maxAtteint = Math.max(maxAtteint, indexCourant);

            var estFin = indexCourant === groupes.length;
            var ecransVisibles = estFin ? ['fin'] : groupes[indexCourant].ecrans;

            ecrans.forEach(function (ecran) {
                ecran.hidden = ecransVisibles.indexOf(ecran.getAttribute('data-ecran')) === -1;
            });

            // Le récapitulatif se relit à l'affichage de l'étape Aperçu
            if (!estFin && ecransVisibles.indexOf('recap') !== -1) {
                remplirRecap();
            }

            // Annonce publiée : le brouillon n'a plus lieu d'être
            if (estFin) {
                try {
                    localStorage.removeItem('poolparty-brouillon');
                } catch (erreur) {
                    // Stockage indisponible : rien à effacer
                }
            }

            // Veille anti-autofill : tant que l'étape Compte est affichée,
            // on revalide régulièrement au cas où le navigateur remplisse
            // les champs sans déclencher d'événement input
            if (!estFin && groupes[indexCourant].id === 'compte') {
                if (!compteVeille) {
                    compteVeille = setInterval(function () {
                        majNavigation();
                    }, 800);
                }
            } else if (compteVeille) {
                clearInterval(compteVeille);
                compteVeille = null;
            }

            majStepper();
            majNavigation();
            window.scrollTo(0, 0);
        };

        navSuivant.addEventListener('click', function () {
            afficherGroupe(indexCourant + 1);
            if (bandeauReprise) {
                bandeauReprise.hidden = true;
            }
        });

        // Bouton Continuer de la carte (écran compte) : même effet que Suivant
        if (boutonCarteCompte) {
            boutonCarteCompte.addEventListener('click', function () {
                afficherGroupe(indexCourant + 1);
                if (bandeauReprise) {
                    bandeauReprise.hidden = true;
                }
            });
        }

        navRetour.addEventListener('click', function () {
            afficherGroupe(indexCourant - 1);
            if (bandeauReprise) {
                bandeauReprise.hidden = true;
            }
        });

        // --- Brouillon : Enregistrer et quitter mémorise le parcours
        // dans le navigateur (localStorage) ; au retour sur la page le
        // brouillon est restauré et un bandeau propose de repartir de
        // zéro. Les photos, elles, ne survivent pas au rechargement :
        // la reprise est plafonnée à l'écran photos si besoin.
        var CLE_BROUILLON = 'poolparty-brouillon';
        var bandeauReprise = document.querySelector('.proposer-reprise');
        var indexInitial = 0;

        var brouillon = null;
        try {
            brouillon = JSON.parse(localStorage.getItem(CLE_BROUILLON));
        } catch (erreur) {
            brouillon = null;
        }

        if (brouillon) {
            // Champs texte, e-mail, nombre et curseurs (jamais le mot de passe)
            Object.keys(brouillon.champs || {}).forEach(function (id) {
                var champ = document.getElementById(id);
                if (champ) {
                    champ.value = brouillon.champs[id];
                }
            });

            // Cases à cocher et interrupteurs (règles, CGU, attestation)
            Object.keys(brouillon.cases || {}).forEach(function (nom) {
                var caseCochee = parcours.querySelector('input[type="checkbox"][name="' + nom + '"], input[type="checkbox"][id="' + nom + '"]');
                if (caseCochee) {
                    caseCochee.checked = Boolean(brouillon.cases[nom]);
                }
            });

            annonce.choix = brouillon.choix || {};
            annonce.multi = brouillon.multi || {};
            annonce.compteurs = brouillon.compteurs || {};

            // Réapplique l'état sélectionné des cartes de choix
            Array.prototype.slice.call(parcours.querySelectorAll('[data-choix-groupe]')).forEach(function (groupe) {
                var nomGroupe = groupe.getAttribute('data-choix-groupe');
                var multiple = groupe.hasAttribute('data-multi');
                Array.prototype.slice.call(groupe.querySelectorAll('[data-valeur]')).forEach(function (carte) {
                    var valeur = carte.getAttribute('data-valeur');
                    var actif = multiple
                        ? (annonce.multi[nomGroupe] || []).indexOf(valeur) > -1
                        : annonce.choix[nomGroupe] === valeur;
                    carte.classList.toggle('is-selected', actif);
                    carte.setAttribute('aria-pressed', String(actif));
                });
            });

            // Réécrit la valeur affichée des compteurs avant leur initialisation
            Array.prototype.slice.call(parcours.querySelectorAll('.compteur')).forEach(function (compteur) {
                var nom = compteur.getAttribute('data-compteur');
                if (annonce.compteurs[nom] !== undefined) {
                    compteur.querySelector('.compteur__valeur').textContent = String(annonce.compteurs[nom]);
                }
            });

            // Reprise au plus tard à l'étape Annonce (qui contient les photos,
            // dont les aperçus ne survivent pas au rechargement)
            var indexAnnonce = groupes.findIndex(function (groupe) {
                return groupe.id === 'annonce';
            });
            indexInitial = Math.min(brouillon.index || 0, indexAnnonce);

            if (bandeauReprise) {
                bandeauReprise.hidden = false;
            }
        }

        var lienQuitter = document.querySelector('.js-quitter');
        if (lienQuitter) {
            lienQuitter.addEventListener('click', function () {
                // Annonce déjà publiée : ne pas recréer de brouillon
                if (indexCourant === groupes.length) {
                    return;
                }

                var champs = {};
                Array.prototype.slice.call(parcours.querySelectorAll('input[id], textarea[id]')).forEach(function (champ) {
                    if (champ.type === 'password' || champ.type === 'file' || champ.type === 'checkbox') {
                        return;
                    }
                    champs[champ.id] = champ.value;
                });

                var cases = {};
                Array.prototype.slice.call(parcours.querySelectorAll('input[type="checkbox"]')).forEach(function (caseCochee) {
                    cases[caseCochee.name || caseCochee.id] = caseCochee.checked;
                });

                try {
                    localStorage.setItem(CLE_BROUILLON, JSON.stringify({
                        index: indexCourant,
                        champs: champs,
                        cases: cases,
                        choix: annonce.choix,
                        multi: annonce.multi,
                        compteurs: annonce.compteurs
                    }));
                } catch (erreur) {
                    // Stockage indisponible : on quitte sans brouillon
                }
                // La navigation du lien vers l'accueil suit son cours
            });
        }

        var boutonRecommencer = document.querySelector('[data-reprise="reset"]');
        if (boutonRecommencer) {
            boutonRecommencer.addEventListener('click', function () {
                try {
                    localStorage.removeItem(CLE_BROUILLON);
                } catch (erreur) {
                    // Stockage indisponible : rien à effacer
                }
                window.location.reload();
            });
        }

        // Les formulaires du parcours ne se soumettent jamais : la
        // navigation passe uniquement par les boutons Retour / Suivant
        // (évite un rechargement de page sur la touche Entrée)
        Array.prototype.slice.call(parcours.querySelectorAll('.proposer-form')).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
            });
        });

        // --- Écran compte : validation en direct + oeil du mot de passe.
        // On écoute input et change : le remplissage automatique du
        // navigateur ne déclenche parfois que le second.
        var toucherCompte = function () {
            compteTouche = true;
            majNavigation();
        };

        [champCompte.prenom, champCompte.nom, champCompte.email, champCompte.password].forEach(function (champ) {
            champ.addEventListener('input', toucherCompte);
            champ.addEventListener('change', toucherCompte);
        });
        champCompte.cgu.addEventListener('change', toucherCompte);

        var oeilCompte = parcours.querySelector('[data-ecran="compte"] .form-field__eye');
        if (oeilCompte) {
            oeilCompte.addEventListener('click', function () {
                var visible = champCompte.password.type === 'text';
                champCompte.password.type = visible ? 'password' : 'text';
                oeilCompte.setAttribute('aria-pressed', String(!visible));
            });
        }

        // --- Écran adresse : validation en direct
        [champAdresse.voie, champAdresse.cp, champAdresse.commune].forEach(function (champ) {
            champ.addEventListener('input', majNavigation);
        });

        // --- Cartes de choix : sélection simple ou multiple selon le groupe
        Array.prototype.slice.call(parcours.querySelectorAll('[data-choix-groupe]')).forEach(function (groupe) {
            var nomGroupe = groupe.getAttribute('data-choix-groupe');
            var multiple = groupe.hasAttribute('data-multi');
            var cartes = Array.prototype.slice.call(groupe.querySelectorAll('[data-valeur]'));

            // Ne pas écraser une liste restaurée depuis le brouillon
            if (multiple && !annonce.multi[nomGroupe]) {
                annonce.multi[nomGroupe] = [];
            }

            cartes.forEach(function (carte) {
                carte.addEventListener('click', function () {
                    var valeur = carte.getAttribute('data-valeur');

                    if (multiple) {
                        var actif = carte.classList.toggle('is-selected');
                        carte.setAttribute('aria-pressed', String(actif));
                        var liste = annonce.multi[nomGroupe];
                        if (actif) {
                            liste.push(valeur);
                        } else {
                            // Garde : indexOf à -1 supprimerait le dernier élément
                            var position = liste.indexOf(valeur);
                            if (position !== -1) {
                                liste.splice(position, 1);
                            }
                        }
                    } else {
                        cartes.forEach(function (autre) {
                            autre.classList.toggle('is-selected', autre === carte);
                            autre.setAttribute('aria-pressed', String(autre === carte));
                        });
                        annonce.choix[nomGroupe] = valeur;
                    }

                    majNavigation();
                });
            });
        });

        // --- Compteurs de capacité (invités, vestiaires, salles d'eau)
        Array.prototype.slice.call(parcours.querySelectorAll('.compteur')).forEach(function (compteur) {
            var nom = compteur.getAttribute('data-compteur');
            var mini = parseInt(compteur.getAttribute('data-min'), 10);
            var maxi = parseInt(compteur.getAttribute('data-max'), 10);
            var valeurSpan = compteur.querySelector('.compteur__valeur');
            var btnMoins = compteur.querySelector('[data-action="moins"]');
            var btnPlus = compteur.querySelector('[data-action="plus"]');
            var valeur = parseInt(valeurSpan.textContent, 10);

            var majValeur = function () {
                valeurSpan.textContent = String(valeur);
                btnMoins.disabled = valeur <= mini;
                btnPlus.disabled = valeur >= maxi;
                annonce.compteurs[nom] = valeur;
            };

            btnMoins.addEventListener('click', function () {
                valeur = Math.max(mini, valeur - 1);
                majValeur();
            });

            btnPlus.addEventListener('click', function () {
                valeur = Math.min(maxi, valeur + 1);
                majValeur();
            });

            majValeur();
        });

        // --- Photos : aperçus locaux, la première sert de couverture.
        // Aucun envoi vers un serveur : les fichiers restent dans le
        // navigateur le temps de la session (projet fictif).
        var photosInput = document.getElementById('photos-input');
        var photosGrid = parcours.querySelector('.photos-grid');
        var photosCompteur = parcours.querySelector('.photos-compteur');

        if (photosInput && photosGrid) {
            var rendrePhotos = function () {
                photosGrid.innerHTML = '';

                annonce.photos.forEach(function (photo, index) {
                    var item = document.createElement('li');
                    item.className = 'photo-item';

                    var img = document.createElement('img');
                    img.src = photo.url;
                    img.alt = 'Photo ' + (index + 1) + ' de votre espace';
                    item.appendChild(img);

                    if (index === 0) {
                        var badge = document.createElement('span');
                        badge.className = 'photo-item__badge';
                        badge.textContent = 'Photo de couverture';
                        item.appendChild(badge);
                    }

                    var suppr = document.createElement('button');
                    suppr.type = 'button';
                    suppr.className = 'photo-item__suppr';
                    suppr.setAttribute('aria-label', 'Supprimer la photo ' + (index + 1));
                    suppr.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>';
                    suppr.addEventListener('click', function () {
                        URL.revokeObjectURL(photo.url);
                        annonce.photos.splice(index, 1);
                        rendrePhotos();
                    });
                    item.appendChild(suppr);

                    photosGrid.appendChild(item);
                });

                var total = annonce.photos.length;
                if (total === 0) {
                    photosCompteur.textContent = '0 photo ajoutée sur 5 minimum';
                } else if (total < 5) {
                    photosCompteur.textContent = total + ' photo' + (total > 1 ? 's' : '') + ' ajoutée' + (total > 1 ? 's' : '') + ' sur 5 minimum';
                } else {
                    photosCompteur.textContent = total + ' photos ajoutées, vous pouvez continuer';
                }

                majNavigation();
            };

            photosInput.addEventListener('change', function () {
                Array.prototype.slice.call(photosInput.files).forEach(function (fichier) {
                    annonce.photos.push({ url: URL.createObjectURL(fichier) });
                });
                // Permet de resélectionner les mêmes fichiers si besoin
                photosInput.value = '';
                rendrePhotos();
            });
        }

        // --- Titre et description : compteur de caractères en direct
        Array.prototype.slice.call(parcours.querySelectorAll('[data-compteur-texte]')).forEach(function (span) {
            var champ = document.getElementById(span.getAttribute('data-compteur-texte'));
            champ.addEventListener('input', function () {
                span.textContent = String(champ.value.length);
                majNavigation();
            });
            // Valeur initiale (utile après restauration d'un brouillon)
            span.textContent = String(champ.value.length);
        });

        // --- Prix : recalcul du revenu et du prix affiché (frais 15 %)
        if (champPrix) {
            var prixRevenu = parcours.querySelector('[data-prix="revenu"]');
            var prixAffiche = parcours.querySelector('[data-prix="affiche"]');

            var majPrix = function () {
                // Saisie strictement numérique : on retire tout caractère
                // qui n'est pas un chiffre (lettres, « e », signes, espaces,
                // y compris via un collage) et on plafonne à 3 chiffres.
                var nettoye = champPrix.value.replace(/\D/g, '').slice(0, 3);
                if (nettoye !== champPrix.value) {
                    champPrix.value = nettoye;
                }
                var valeur = parseInt(champPrix.value, 10) || 0;
                prixRevenu.textContent = valeur + ' €';
                prixAffiche.textContent = Math.round(valeur * 1.15) + ' €';
                majNavigation();
            };

            // Bloque en amont les touches non numériques pour que rien
            // d'autre qu'un chiffre n'apparaisse (les touches de contrôle
            // et les raccourcis clavier restent autorisés).
            champPrix.addEventListener('keydown', function (event) {
                if (event.ctrlKey || event.metaKey || event.altKey) {
                    return;
                }
                var controles = ['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'];
                if (controles.indexOf(event.key) !== -1) {
                    return;
                }
                if (!/^[0-9]$/.test(event.key)) {
                    event.preventDefault();
                }
            });

            champPrix.addEventListener('input', majPrix);
            majPrix();
        }

        // --- Aperçu de l'annonce : « Voir mon annonce » (écran de fin)
        // ouvre une fiche récapitulant ce que verront les invités,
        // construite à partir des données saisies. Le projet étant
        // fictif, aucune annonce n'existe réellement en base : on ne
        // renvoie donc pas vers le catalogue mais vers cet aperçu.
        var apercuOverlay = document.getElementById('apercu-annonce');
        var apercuOuvrir = parcours.querySelector('[data-apercu-ouvrir]');

        if (apercuOverlay && apercuOuvrir) {
            var lireApercu = function (cle) {
                return apercuOverlay.querySelector('[data-apercu="' + cle + '"]');
            };

            var remplirApercu = function () {
                var prixHeure = parseInt(champPrix.value, 10) || 0;

                lireApercu('titre').textContent = champTitre.value.trim() || 'Votre annonce';
                lireApercu('commune').textContent = champAdresse.commune.value.trim() || 'Votre commune';
                lireApercu('type').textContent = annonce.choix.type || 'Votre espace';
                lireApercu('invites').textContent = (annonce.compteurs.invites || 8) + ' personnes max';
                lireApercu('prix').textContent = Math.round(prixHeure * 1.15) + ' €/ h';

                if (annonce.photos.length) {
                    lireApercu('photo').src = annonce.photos[0].url;
                }

                // Description : masquée tant qu'elle est vide
                var description = champDescription.value.trim();
                var descriptionBloc = lireApercu('description-bloc');
                if (description) {
                    lireApercu('description').textContent = description;
                    descriptionBloc.hidden = false;
                } else {
                    descriptionBloc.hidden = true;
                }

                // Équipements de confort et de sécurité réunis
                var equipements = (annonce.multi.equipements || []).concat(annonce.multi.securite || []);
                var equipementsBloc = lireApercu('equipements-bloc');
                var equipementsListe = lireApercu('equipements');
                equipementsListe.innerHTML = '';
                if (equipements.length) {
                    equipements.forEach(function (nom) {
                        var item = document.createElement('li');
                        item.textContent = nom;
                        equipementsListe.appendChild(item);
                    });
                    equipementsBloc.hidden = false;
                } else {
                    equipementsBloc.hidden = true;
                }
            };

            var fermerApercu = function () {
                apercuOverlay.hidden = true;
            };

            apercuOuvrir.addEventListener('click', function () {
                remplirApercu();
                apercuOverlay.hidden = false;
            });

            var apercuFermer = apercuOverlay.querySelector('.popup__close');
            if (apercuFermer) {
                apercuFermer.addEventListener('click', fermerApercu);
            }

            // Clic sur le voile sombre ou touche Échap : fermeture
            apercuOverlay.addEventListener('click', function (event) {
                if (event.target === apercuOverlay) {
                    fermerApercu();
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && !apercuOverlay.hidden) {
                    fermerApercu();
                }
            });
        }

        // --- Disponibilités : double curseur horaire, même mécanique
        // que le créneau du calendrier (deux poignées, segment vert
        // repositionné entre les deux, une heure d'écart minimum)
        var dispoHoraire = parcours.querySelector('.dispo-horaire');

        if (dispoHoraire) {
            var dispoValeur = dispoHoraire.querySelector('.range-double__value');
            var dispoFill = dispoHoraire.querySelector('.range-double__fill');
            var dispoDebut = document.getElementById('dispo-debut');
            var dispoFin = document.getElementById('dispo-fin');

            var libelleHeureDispo = function (heure) {
                return (heure === 24 ? '00' : heure) + 'h';
            };

            var majDispo = function () {
                var debut = parseInt(dispoDebut.value, 10);
                var fin = parseInt(dispoFin.value, 10);
                var mini = parseInt(dispoDebut.min, 10);
                var plage = parseInt(dispoDebut.max, 10) - mini;

                dispoFill.style.left = (((debut - mini) / plage) * 100) + '%';
                dispoFill.style.width = (((fin - debut) / plage) * 100) + '%';
                dispoValeur.textContent = libelleHeureDispo(debut) + ' - ' + libelleHeureDispo(fin);
            };

            dispoDebut.addEventListener('input', function () {
                var borne = parseInt(dispoFin.value, 10) - 1;
                if (parseInt(dispoDebut.value, 10) > borne) {
                    dispoDebut.value = borne;
                }
                majDispo();
            });

            dispoFin.addEventListener('input', function () {
                var borne = parseInt(dispoDebut.value, 10) + 1;
                if (parseInt(dispoFin.value, 10) < borne) {
                    dispoFin.value = borne;
                }
                majDispo();
            });

            majDispo();
        }

        // --- Versement : validation en direct du titulaire et de l'IBAN
        if (champVersement.titulaire && champVersement.iban) {
            [champVersement.titulaire, champVersement.iban].forEach(function (champ) {
                champ.addEventListener('input', majNavigation);
            });
        }

        // --- Attestation de sécurité : condition de l'écran règles
        if (attestation) {
            attestation.addEventListener('change', majNavigation);
        }

        // Étape initiale : celle du brouillon restauré, sinon la première
        afficherGroupe(indexInitial);
    }

    // =============================================================
    // BANDEAU COOKIES
    // Conforme aux recommandations CNIL : accepter, refuser ou
    // personnaliser sont proposés au même niveau, le refus est aussi
    // simple que l'acceptation. Le choix est conservé six mois dans
    // localStorage puis le bandeau se raffiche. Le lien "Gérer mes
    // cookies" du pied de page permet de changer d'avis à tout moment.
    // Aucun cookie tiers n'est réellement déposé (projet fictif) : le
    // consentement est simplement mémorisé.
    // =============================================================
    var cookiesBanner = document.getElementById('cookies-banner');

    if (cookiesBanner) {
        var CLE_COOKIES = 'pp-cookies';
        var DUREE_CONSENTEMENT_MS = 1000 * 60 * 60 * 24 * 182; // 6 mois

        // Le bandeau est construit ici pour rester identique sur toutes
        // les pages : le HTML ne contient que le conteneur vide.
        cookiesBanner.setAttribute('role', 'region');
        cookiesBanner.setAttribute('aria-label', 'Gestion des cookies');
        cookiesBanner.innerHTML =
            '<div class="cookies-banner__inner">' +
                '<div class="cookies-banner__texte">' +
                    '<h2 class="cookies-banner__titre" tabindex="-1">Vos cookies, vos choix</h2>' +
                    '<p>Nous utilisons des cookies pour mesurer l\'audience du site et retenir vos préférences de navigation. Vous pouvez tout accepter, tout refuser ou choisir catégorie par catégorie, puis changer d\'avis à tout moment depuis le lien Gérer mes cookies du pied de page. <a href="mentions-legales.html#cookies">En savoir plus</a></p>' +
                '</div>' +
                '<fieldset class="cookies-banner__prefs" hidden>' +
                    '<legend>Choisir par catégorie</legend>' +
                    '<label class="cookies-banner__categorie">' +
                        '<input type="checkbox" checked disabled>' +
                        '<span><strong>Strictement nécessaires</strong>Session et sécurité du site : toujours actifs, ils ne demandent pas de consentement.</span>' +
                    '</label>' +
                    '<label class="cookies-banner__categorie">' +
                        '<input type="checkbox" id="cookies-audience">' +
                        '<span><strong>Mesure d\'audience</strong>Statistiques de visite (Google Analytics 4) pour comprendre l\'usage du site et l\'améliorer.</span>' +
                    '</label>' +
                    '<label class="cookies-banner__categorie">' +
                        '<input type="checkbox" id="cookies-preferences">' +
                        '<span><strong>Préférences</strong>Mémorisation de vos recherches récentes et de vos favoris.</span>' +
                    '</label>' +
                '</fieldset>' +
                '<div class="cookies-banner__actions">' +
                    '<button type="button" class="btn cookies-banner__btn cookies-banner__btn--contour" data-cookies="personnaliser">Personnaliser</button>' +
                    '<button type="button" class="btn cookies-banner__btn cookies-banner__btn--contour" data-cookies="enregistrer" hidden>Enregistrer mes choix</button>' +
                    '<button type="button" class="btn cookies-banner__btn cookies-banner__btn--plein" data-cookies="refuser">Tout refuser</button>' +
                    '<button type="button" class="btn cookies-banner__btn cookies-banner__btn--plein" data-cookies="accepter">Tout accepter</button>' +
                '</div>' +
            '</div>';

        var cookiesTitre = cookiesBanner.querySelector('.cookies-banner__titre');
        var cookiesPrefs = cookiesBanner.querySelector('.cookies-banner__prefs');
        var caseAudience = cookiesBanner.querySelector('#cookies-audience');
        var casePreferences = cookiesBanner.querySelector('#cookies-preferences');
        var btnPersonnaliser = cookiesBanner.querySelector('[data-cookies="personnaliser"]');
        var btnEnregistrer = cookiesBanner.querySelector('[data-cookies="enregistrer"]');

        // Lecture du consentement mémorisé ; null si absent, illisible
        // ou expiré (la CNIL recommande de redemander après 6 mois)
        var lireConsentement = function () {
            try {
                var consentement = JSON.parse(localStorage.getItem(CLE_COOKIES));
                if (!consentement || !consentement.horodatage) {
                    return null;
                }
                if (Date.now() - consentement.horodatage > DUREE_CONSENTEMENT_MS) {
                    return null;
                }
                return consentement;
            } catch (erreur) {
                return null;
            }
        };

        var fermerBandeau = function () {
            cookiesBanner.classList.remove('is-visible');
        };

        // ouvrirPanneau : true pour arriver directement sur le choix
        // par catégorie (réouverture depuis le pied de page)
        var ouvrirBandeau = function (ouvrirPanneau) {
            var consentement = lireConsentement();
            caseAudience.checked = Boolean(consentement && consentement.audience);
            casePreferences.checked = Boolean(consentement && consentement.preferences);
            cookiesPrefs.hidden = !ouvrirPanneau;
            btnPersonnaliser.hidden = Boolean(ouvrirPanneau);
            btnEnregistrer.hidden = !ouvrirPanneau;
            cookiesBanner.classList.add('is-visible');
        };

        var enregistrerConsentement = function (audience, preferences) {
            try {
                localStorage.setItem(CLE_COOKIES, JSON.stringify({
                    audience: audience,
                    preferences: preferences,
                    horodatage: Date.now()
                }));
            } catch (erreur) {
                // Stockage indisponible : le bandeau reviendra, sans bloquer
            }
            fermerBandeau();
        };

        cookiesBanner.addEventListener('click', function (event) {
            var bouton = event.target.closest('[data-cookies]');
            if (!bouton) {
                return;
            }

            var action = bouton.getAttribute('data-cookies');

            if (action === 'accepter') {
                enregistrerConsentement(true, true);
            } else if (action === 'refuser') {
                enregistrerConsentement(false, false);
            } else if (action === 'enregistrer') {
                enregistrerConsentement(caseAudience.checked, casePreferences.checked);
            } else if (action === 'personnaliser') {
                cookiesPrefs.hidden = false;
                btnPersonnaliser.hidden = true;
                btnEnregistrer.hidden = false;
            }
        });

        // Lien "Gérer mes cookies" annoncé par les mentions légales :
        // injecté ici, sous "Politique des cookies" dans le pied de
        // page, car il n'a d'effet qu'avec JS
        var lienPolitique = document.querySelector('.footer-col a[href$="#cookies"]');

        if (lienPolitique && lienPolitique.closest('li')) {
            var itemGerer = document.createElement('li');
            var btnGerer = document.createElement('button');
            btnGerer.type = 'button';
            btnGerer.className = 'footer-cookies-btn';
            btnGerer.textContent = 'Gérer mes cookies';
            btnGerer.addEventListener('click', function () {
                ouvrirBandeau(true);
                cookiesTitre.focus();
            });
            itemGerer.appendChild(btnGerer);
            lienPolitique.closest('li').insertAdjacentElement('afterend', itemGerer);
        }

        // Première visite ou consentement expiré : afficher le bandeau
        if (!lireConsentement()) {
            ouvrirBandeau(false);
        }
    }

});

/* =============================================================
   Bloc temoignages de l'accueil : une SEULE rangee.
   On n'affiche que les cartes entieres qui tiennent sur la largeur
   disponible, on masque les autres (attribut hidden). Recalcule au
   redimensionnement. La largeur des cartes est fixee par le CSS
   (.temoignages-track .card-temoignage). Jamais de deuxieme ligne,
   jamais de carte coupee.
   ============================================================= */
document.addEventListener('DOMContentLoaded', function () {
    var piste = document.querySelector('.temoignages-track');
    if (!piste) {
        return;
    }
    var cartes = Array.prototype.slice.call(piste.querySelectorAll('.card-temoignage'));
    if (cartes.length < 2) {
        return;
    }

    // Largeur mini d'une carte, à garder synchronisée avec le flex-basis
    // du CSS (.temoignages-track .card-temoignage). Sert à décider combien
    // de cartes tiennent ; les cartes visibles s'élargissent ensuite pour
    // remplir la ligne (flex-grow), donc on ne mesure pas leur largeur rendue.
    var LARGEUR_MINI_CARTE = 250;

    function ajusterTemoignages() {
        var styles = window.getComputedStyle(piste);
        var espace = parseFloat(styles.columnGap || styles.gap) || 20;
        var largeurDispo = piste.clientWidth;
        if (!largeurDispo) {
            // Mise en page pas encore stabilisée : on réessaie au prochain rendu
            window.requestAnimationFrame(ajusterTemoignages);
            return;
        }
        // Nombre de cartes qui tiennent : n cartes + (n-1) écarts
        var nombre = Math.floor((largeurDispo + espace) / (LARGEUR_MINI_CARTE + espace));
        nombre = Math.max(1, Math.min(cartes.length, nombre));
        cartes.forEach(function (carte, index) {
            carte.hidden = index >= nombre;
        });
    }

    // On recalcule à plusieurs moments car au DOMContentLoaded la mise en
    // page responsive (marges, polices) n'est pas toujours stabilisée et
    // la largeur mesurée serait fausse.
    window.requestAnimationFrame(ajusterTemoignages);
    window.addEventListener('load', ajusterTemoignages);

    var minuterie;
    window.addEventListener('resize', function () {
        clearTimeout(minuterie);
        minuterie = setTimeout(ajusterTemoignages, 100);
    });
});





/* =============================================================
   POOL PARTY - MODULE ACCESSIBILITÉ
   Bouton flottant + panneau "Paramètres d'accessibilité" présent
   sur toutes les pages. Réglages pensés pour la dyslexie, la
   malvoyance, le daltonisme et la sensibilité au mouvement.

   Le bouton (API popover) et le panneau (élément dialog) sont
   rendus dans la "top layer" du navigateur : ils restent fixes et
   cliquables même si une extension ou un script pose un transform
   sur la page (cas Google Translate), ce qui piégerait un simple
   position:fixed. Ils passent aussi au-dessus de la barre d'admin
   WordPress. Ils sont ajoutés sous <html> (hors <body>) pour ne
   jamais subir le zoom appliqué au contenu.

   Conformité RGAA / WCAG : dialogue modal natif (piège de focus et
   touche Échap gérés par le navigateur), états ARIA, focus visible,
   persistance des choix.
   ============================================================= */
(function () {
    'use strict';

    var CLE = 'pp-a11y';
    var racine = document.documentElement;

    var ICONES = {
        acces: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="4" r="1.7"/><path d="M5.5 8.5h13"/><path d="M12 6.4V13"/><path d="M8 20l4-7 4 7"/></svg>',
        fermer: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12M18 6L6 18"/></svg>',
        taille: '<svg viewBox="0 0 24 24" fill="currentColor"><text x="0.5" y="19" font-size="19" font-weight="700" font-family="Poppins, sans-serif">A</text><text x="13.5" y="19" font-size="12" font-weight="700" font-family="Poppins, sans-serif">a</text></svg>',
        espacement: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 5h18M3 12h18M3 19h18"/></svg>',
        police: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 7V5h14v2M12 5v14M9 19h6"/></svg>',
        contraste: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 1 0 18z" fill="currentColor" stroke="none"/></svg>',
        gris: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 0 0 18 6 6 0 0 1 0-18z" fill="currentColor" stroke="none"/></svg>',
        liens: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 15l6-6"/><path d="M11 7l1-1a4 4 0 0 1 6 6l-1 1"/><path d="M13 17l-1 1a4 4 0 0 1-6-6l1-1"/></svg>',
        curseur: '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M5 3l13 7.5-5.2 1.1 2.7 6.4-2.6 1.1-2.7-6.4L5 17z"/></svg>',
        guide: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="10" width="18" height="4" rx="1" fill="currentColor" stroke="none"/><path d="M3 5h18" opacity="0.45"/><path d="M3 19h18" opacity="0.45"/></svg>',
        image: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="15" rx="2"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="M4 17l4.5-4.5 3 3"/><path d="M3 3l18 18"/></svg>',
        anim: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M10 9v6M14 9v6"/></svg>',
        reset: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10a8 8 0 1 1 1 5"/><path d="M4 5v5h5"/></svg>',
        check: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5 9-11"/></svg>'
    };

    var INTERRUPTEURS = [
        { cle: 'spacing',   label: 'Espacement du texte',       icone: 'espacement' },
        { cle: 'legible',   label: 'Police plus lisible',       icone: 'police' },
        { cle: 'contrast',  label: 'Contraste renforcé',        icone: 'contraste' },
        { cle: 'grayscale', label: 'Niveaux de gris',           icone: 'gris' },
        { cle: 'links',     label: 'Souligner les liens',       icone: 'liens' },
        { cle: 'cursor',    label: 'Grand curseur',             icone: 'curseur' },
        { cle: 'guide',     label: 'Guide de lecture',          icone: 'guide' },
        { cle: 'noimg',     label: 'Masquer les images',        icone: 'image' },
        { cle: 'noanim',    label: 'Désactiver les animations', icone: 'anim' }
    ];

    var etatDefaut = {
        size: 0, spacing: false, legible: false, contrast: false,
        grayscale: false, links: false, cursor: false, guide: false,
        noimg: false, noanim: false
    };

    var etat = charger();

    function charger() {
        try {
            var brut = localStorage.getItem(CLE);
            if (!brut) { return copier(etatDefaut); }
            var lu = JSON.parse(brut);
            var res = copier(etatDefaut);
            Object.keys(res).forEach(function (k) {
                if (typeof lu[k] !== 'undefined') { res[k] = lu[k]; }
            });
            return res;
        } catch (e) {
            return copier(etatDefaut);
        }
    }

    function enregistrer() {
        try { localStorage.setItem(CLE, JSON.stringify(etat)); } catch (e) {}
    }

    function copier(obj) {
        var c = {};
        Object.keys(obj).forEach(function (k) { c[k] = obj[k]; });
        return c;
    }

    var guideEl = null;

    function deplacerGuide(evt) {
        if (!guideEl) { return; }
        var y = (typeof evt.clientY === 'number') ? evt.clientY : 0;
        guideEl.style.transform = 'translateY(' + (y - 23) + 'px)';
    }

    function appliquerClasses() {
        racine.classList.toggle('pp-a11y-size-1', etat.size === 1);
        racine.classList.toggle('pp-a11y-size-2', etat.size === 2);
        INTERRUPTEURS.forEach(function (t) {
            racine.classList.toggle('pp-a11y-' + t.cle, !!etat[t.cle]);
        });
        if (guideEl) { guideEl.hidden = !etat.guide; }
        document.removeEventListener('mousemove', deplacerGuide);
        if (etat.guide) { document.addEventListener('mousemove', deplacerGuide); }
    }

    // Application immédiate (avant construction) pour éviter tout
    // clignotement au chargement des pages.
    appliquerClasses();

    var fab = null;
    var panneau = null;

    function initialiser() {
        if (document.querySelector('.pp-a11y-fab')) { return; }

        // Bouton flottant, rendu en top layer via l'API popover.
        fab = document.createElement('button');
        fab.type = 'button';
        fab.className = 'pp-a11y-fab';
        fab.setAttribute('popover', 'manual');
        fab.setAttribute('aria-haspopup', 'dialog');
        fab.setAttribute('aria-expanded', 'false');
        fab.setAttribute('aria-controls', 'pp-a11y-panel');
        fab.setAttribute('aria-label', "Ouvrir les paramètres d'accessibilité");
        fab.innerHTML = ICONES.acces;

        // Panneau : dialogue natif (piège de focus + Échap gérés par
        // le navigateur, rendu en top layer).
        panneau = document.createElement('dialog');
        panneau.className = 'pp-a11y-panel';
        panneau.id = 'pp-a11y-panel';
        panneau.setAttribute('aria-labelledby', 'pp-a11y-title');
        panneau.innerHTML = construireContenu();

        // Guide de lecture
        guideEl = document.createElement('div');
        guideEl.className = 'pp-a11y-guidebar';
        guideEl.setAttribute('aria-hidden', 'true');
        guideEl.hidden = !etat.guide;

        racine.appendChild(fab);
        racine.appendChild(panneau);
        racine.appendChild(guideEl);

        // Affiche le bouton en top layer ; repli en bouton fixe simple
        // si l'API popover n'est pas disponible (très anciens navigateurs).
        try {
            fab.showPopover();
        } catch (e) {
            fab.removeAttribute('popover');
        }

        var boutonsTaille = panneau.querySelectorAll('.pp-a11y-size-btn');
        var interrupteurs = panneau.querySelectorAll('.pp-a11y-toggle');
        var boutonFermer = panneau.querySelector('.pp-a11y-close');
        var reset = panneau.querySelector('.pp-a11y-btn--reset');
        var termine = panneau.querySelector('.pp-a11y-btn--done');

        Array.prototype.forEach.call(boutonsTaille, function (btn) {
            btn.addEventListener('click', function () {
                etat.size = parseInt(btn.getAttribute('data-size'), 10) || 0;
                appliquerClasses();
                rafraichir();
                enregistrer();
            });
        });

        Array.prototype.forEach.call(interrupteurs, function (btn) {
            btn.addEventListener('click', function () {
                var cle = btn.getAttribute('data-cle');
                etat[cle] = !etat[cle];
                appliquerClasses();
                rafraichir();
                enregistrer();
            });
        });

        reset.addEventListener('click', function () {
            etat = copier(etatDefaut);
            appliquerClasses();
            rafraichir();
            enregistrer();
        });

        fab.addEventListener('click', ouvrir);
        boutonFermer.addEventListener('click', fermerPanneau);
        termine.addEventListener('click', fermerPanneau);

        // Clic sur le fond sombre du dialogue (::backdrop) : fermeture.
        panneau.addEventListener('click', function (evt) {
            if (evt.target === panneau) { fermerPanneau(); }
        });

        // Échap ou fermeture native : on restaure le bouton et le focus.
        panneau.addEventListener('close', apresFermeture);

        initialiser.refs = {
            boutonsTaille: boutonsTaille,
            interrupteurs: interrupteurs
        };

        rafraichir();
    }

    function construireContenu() {
        var html = '';

        html += '<div class="pp-a11y-head">';
        html += '<span class="pp-a11y-head__icon" aria-hidden="true">' + ICONES.acces + '</span>';
        html += '<h2 class="pp-a11y-title" id="pp-a11y-title">Paramètres d\'accessibilité</h2>';
        html += '<button type="button" class="pp-a11y-close" aria-label="Fermer les paramètres d\'accessibilité">' + ICONES.fermer + '</button>';
        html += '</div>';

        html += '<div class="pp-a11y-body">';

        html += '<fieldset class="pp-a11y-group">';
        html += '<legend class="pp-a11y-group__legend">Confort de lecture</legend>';
        html += '<div class="pp-a11y-sizes" role="group" aria-label="Taille du texte">';
        html += '<span class="pp-a11y-sizes__icon" aria-hidden="true">' + ICONES.taille + '</span>';
        html += '<span class="pp-a11y-sizes__label" id="pp-a11y-size-label">Taille du texte</span>';
        html += '<span class="pp-a11y-sizes__set">';
        html += '<button type="button" class="pp-a11y-size-btn" data-size="0" aria-pressed="false" aria-label="Taille normale">A</button>';
        html += '<button type="button" class="pp-a11y-size-btn" data-size="1" aria-pressed="false" aria-label="Grande taille">A</button>';
        html += '<button type="button" class="pp-a11y-size-btn" data-size="2" aria-pressed="false" aria-label="Très grande taille">A</button>';
        html += '</span></div>';
        html += interrupteurHTML('spacing') + interrupteurHTML('legible');
        html += '</fieldset>';

        html += '<fieldset class="pp-a11y-group">';
        html += '<legend class="pp-a11y-group__legend">Vision et couleurs</legend>';
        html += interrupteurHTML('contrast') + interrupteurHTML('grayscale') + interrupteurHTML('links');
        html += '</fieldset>';

        html += '<fieldset class="pp-a11y-group">';
        html += '<legend class="pp-a11y-group__legend">Navigation et confort</legend>';
        html += interrupteurHTML('cursor') + interrupteurHTML('guide') + interrupteurHTML('noimg') + interrupteurHTML('noanim');
        html += '</fieldset>';

        html += '</div>';

        html += '<div class="pp-a11y-foot">';
        html += '<button type="button" class="pp-a11y-btn pp-a11y-btn--reset">' + ICONES.reset + '<span>Réinitialiser</span></button>';
        html += '<button type="button" class="pp-a11y-btn pp-a11y-btn--done">' + ICONES.check + '<span>Fermer</span></button>';
        html += '</div>';

        return html;
    }

    function interrupteurHTML(cle) {
        var cfg = null;
        for (var i = 0; i < INTERRUPTEURS.length; i++) {
            if (INTERRUPTEURS[i].cle === cle) { cfg = INTERRUPTEURS[i]; break; }
        }
        if (!cfg) { return ''; }
        var h = '';
        h += '<button type="button" class="pp-a11y-toggle" role="switch" aria-checked="false" data-cle="' + cfg.cle + '">';
        h += '<span class="pp-a11y-toggle__icon" aria-hidden="true">' + ICONES[cfg.icone] + '</span>';
        h += '<span class="pp-a11y-toggle__label">' + cfg.label + '</span>';
        h += '<span class="pp-a11y-switch" aria-hidden="true"></span>';
        h += '</button>';
        return h;
    }

    function rafraichir() {
        if (!initialiser.refs) { return; }
        var refs = initialiser.refs;
        Array.prototype.forEach.call(refs.boutonsTaille, function (btn) {
            var actif = (parseInt(btn.getAttribute('data-size'), 10) || 0) === etat.size;
            btn.setAttribute('aria-pressed', String(actif));
        });
        Array.prototype.forEach.call(refs.interrupteurs, function (btn) {
            var cle = btn.getAttribute('data-cle');
            btn.setAttribute('aria-checked', String(!!etat[cle]));
        });
    }

    var focusPrecedent = null;

    function ouvrir() {
        if (!panneau) { return; }
        focusPrecedent = document.activeElement;
        // Le bouton s'efface pendant l'ouverture pour ne pas gêner.
        try { fab.hidePopover(); } catch (e) {}
        racine.classList.add('pp-a11y-lock');
        try {
            panneau.showModal();
        } catch (e) {
            // Repli : ouverture non modale si showModal indisponible.
            panneau.setAttribute('open', '');
        }
        fab.setAttribute('aria-expanded', 'true');
        var close = panneau.querySelector('.pp-a11y-close');
        if (close) { close.focus(); }
    }

    function fermerPanneau() {
        if (!panneau) { return; }
        if (panneau.open) {
            panneau.close(); // déclenche l'événement close -> apresFermeture
        } else {
            apresFermeture();
        }
    }

    function apresFermeture() {
        racine.classList.remove('pp-a11y-lock');
        fab.setAttribute('aria-expanded', 'false');
        try { fab.showPopover(); } catch (e) {}
        if (focusPrecedent && typeof focusPrecedent.focus === 'function' && document.contains(focusPrecedent)) {
            focusPrecedent.focus();
        } else if (fab && typeof fab.focus === 'function') {
            fab.focus();
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialiser);
    } else {
        initialiser();
    }
})();

// =============================================================
// MODALE — module générique d'ouverture / fermeture
// Pilote toutes les pop-ups bâties sur la base .modale (global.css).
// Aucune configuration par pop-up : on relie un déclencheur à sa
// fenêtre avec des attributs data-*.
//   Ouvrir : <button data-modale-open="id-de-l-overlay">…</button>
//   Fermer : la croix .modale__fermer, tout élément [data-modale-close],
//            un clic sur le voile sombre, ou la touche Échap.
// Depuis un autre script : window.ppModale.ouvrir('id') / .fermer('id')
// =============================================================
(function () {
    // Mémorise le déclencheur pour lui rendre le focus à la fermeture
    var declencheur = null;

    var trouverOverlay = function (ref) {
        if (!ref) {
            return null;
        }
        return typeof ref === 'string' ? document.getElementById(ref) : ref;
    };

    var ouvrir = function (ref, origine) {
        var overlay = trouverOverlay(ref);
        if (!overlay) {
            return;
        }
        declencheur = origine || document.activeElement;
        overlay.hidden = false;
        // Focus vers la fenêtre pour la navigation clavier
        var carte = overlay.querySelector('.modale');
        var cible = overlay.querySelector('.modale__fermer') || carte;
        if (cible) {
            if (cible === carte && !carte.hasAttribute('tabindex')) {
                carte.setAttribute('tabindex', '-1');
            }
            cible.focus();
        }
    };

    var fermer = function (ref) {
        var overlay = trouverOverlay(ref);
        if (!overlay || overlay.hidden) {
            return;
        }
        overlay.hidden = true;
        if (declencheur && typeof declencheur.focus === 'function') {
            declencheur.focus();
        }
        declencheur = null;
    };

    // Délégation : un seul écouteur couvre aussi les pop-ups ajoutées après coup
    document.addEventListener('click', function (event) {
        var origine = event.target.closest('[data-modale-open]');
        if (origine) {
            event.preventDefault();
            ouvrir(origine.getAttribute('data-modale-open'), origine);
            return;
        }

        var fermeture = event.target.closest('.modale__fermer, [data-modale-close]');
        if (fermeture) {
            event.preventDefault();
            fermer(fermeture.closest('.modale-overlay'));
            return;
        }

        // Clic sur le voile sombre (en dehors de la carte)
        if (event.target.classList.contains('modale-overlay')) {
            fermer(event.target);
        }
    });

    // Échap ferme la fenêtre ouverte
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }
        var ouverte = document.querySelector('.modale-overlay:not([hidden])');
        if (ouverte) {
            fermer(ouverte);
        }
    });

    // API publique pour piloter une modale depuis un autre script
    window.ppModale = { ouvrir: ouvrir, fermer: fermer };
})();
