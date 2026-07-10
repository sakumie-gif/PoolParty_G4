<?php
/**
 * Gabarit des pages catégorie de biens : /categorie/{slug}/
 * (piscine, jacuzzi, spa, sauna, hammam). Sans ce fichier, WordPress
 * retombait sur index.php et affichait une page nue (titre + extrait
 * bruts, sans grille ni filtres).
 *
 * Il réutilise entièrement le gabarit du catalogue (archive-bien.php),
 * qui gère déjà le cas d'une page catégorie (is_tax) : nom de la
 * catégorie dans le fil d'Ariane et l'en-tête, pilule de filtre active.
 * WordPress a déjà restreint la requête principale aux biens du terme
 * courant, la boucle affiche donc uniquement cette catégorie.
 */

require get_template_directory() . '/archive-bien.php';
