<?php

namespace AcMarche\Pivot;

use AcMarche\Pivot\Utils\GenerateClass;

/**
 * @see GenerateClass
 * http://pivot.tourismewallonie.be/index.php/9-pivot-gest-pc/142-types-de-fiches-pivot
 * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr;fmt=json
 */
enum PivotTypeEnum: int
{
    /**
     * Hôtel
     * Root 1
     * Code HTL
     */
    case HOTEL = 1;
    /**
     * Gîte
     * Root 1
     * Code GIT
     */
    case GITE = 2;
    /**
     * Chambre d’hôtes
     * Root 1
     * Code CHB
     */
    case CHAMBRE_D_HOTES = 3;
    /**
     * Meublé
     * Root 1
     * Code MBL
     */
    case MEUBLE = 4;
    /**
     * Camping
     * Root 1
     * Code CMP
     */
    case CAMPING = 5;
    /**
     * Budget Holiday
     * Root 1
     * Code BDG
     */
    case BUDGET_HOLIDAY = 6;
    /**
     * Village de vacances
     * Root 1
     * Code VLG
     */
    case VILLAGE_DE_VACANCES = 7;
    /**
     * Itinéraire
     * Root 1
     * Code ITB
     */
    case ITINERAIRE = 8;
    /**
     * Événement
     * Root 1
     * Code EVT
     */
    case EVENEMENT = 9;
    /**
     * Information de parcours
     * Root
     * Code IFP
     */
    case INFORMATION_DE_PARCOURS = 10;
    /**
     * Découverte et Divertissement
     * Root 1
     * Code ALD
     */
    case DECOUVERTE_ET_DIVERTISSEMENT = 11;
    /**
     * Guide touristique
     * Root 1
     * Code GTR
     */
    case GUIDE_TOURISTIQUE = 12;
    /**
     * Article
     * Root 1
     * Code ART
     */
    case ARTICLE = 13;
    /**
     * Organisme touristique
     * Root 1
     * Code OGT
     */
    case ORGANISME_TOURISTIQUE = 14;
    /**
     * Forfait individuel
     * Root 1
     * Code FTI
     */
    case FORFAIT_INDIVIDUEL = 15;
    /**
     * Forfait groupe
     * Root 1
     * Code FTG
     */
    case FORFAIT_GROUPE = 16;
    /**
     * Agence de voyage
     * Root 1
     * Code AGV
     */
    case AGENCE_DE_VOYAGE = 17;
    /**
     * MICE - Infrastructure
     * Root 1
     * Code MIF
     */
    case MICE_INFRASTRUCTURE = 18;
    /**
     * MICE - Organisateur
     * Root 1
     * Code MOG
     */
    case MICE_ORGANISATEUR = 19;
    /**
     * MICE - Prestataire
     * Root 1
     * Code MPR
     */
    case MICE_PRESTATAIRE = 20;
    /**
     * MICE - Divertissement
     * Root 1
     * Code MDV
     */
    case MICE_DIVERTISSEMENT = 21;
    /**
     * MICE - Animation
     * Root 1
     * Code MAM
     */
    case MICE_ANIMATION = 22;
    /**
     * Contact public
     * Root 1
     * Code CTP
     */
    case CONTACT_PUBLIC = 23;
    /**
     * Salle
     * Root
     * Code SAL
     */
    case SALLE = 24;
    /**
     * Autre hébergement
     * Root 1
     * Code ATH
     */
    case AUTRE_HEBERGEMENT = 25;
    /**
     * Aire pour motor-homes
     * Root 1
     * Code AMH
     */
    case AIRE_POUR_MOTOR_HOMES = 26;
    /**
     * Structure associative
     * Root 1
     * Code ASC
     */
    case STRUCTURE_ASSOCIATIVE = 27;
    /**
     * Endroit de camp
     * Root 1
     * Code EDC
     */
    case ENDROIT_DE_CAMP = 28;
    /**
     * Point nœud
     * Root 1
     * Code PND
     */
    case POINT_NOEUD = 29;
    /**
     * Tronçon
     * Root 1
     * Code TRN
     */
    case TRONCON = 30;
    /**
     * Produit touristique
     * Root 1
     * Code PTR
     */
    case PRODUIT_TOURISTIQUE = 31;
    /**
     * Aire de bivouac
     * Root 1
     * Code ABV
     */
    case AIRE_DE_BIVOUAC = 32;
    /**
     * Zone de fermeture
     * Root 1
     * Code ZFM
     */
    case ZONE_DE_FERMETURE = 33;
    /**
     * Groupe d’événements
     * Root 1
     * Code EGP
     */
    case GROUPE_D_EVENEMENTS = 256;
    /**
     * Animation pédagogique
     * Root 1
     * Code APD
     */
    case ANIMATION_PEDAGOGIQUE = 257;
    /**
     * Producteur
     * Root 1
     * Code PRD
     */
    case PRODUCTEUR = 258;
    /**
     * Artisan
     * Root 1
     * Code ATS
     */
    case ARTISAN = 259;
    /**
     * Boutique de terroir
     * Root 1
     * Code BTQ
     */
    case BOUTIQUE_DE_TERROIR = 260;
    /**
     * Restauration
     * Root 1
     * Code RST
     */
    case RESTAURATION = 261;
    /**
     * Recette
     * Root 1
     * Code RCT
     */
    case RECETTE = 262;
    /**
     * Structure événementielle
     * Root 1
     * Code SMN
     */
    case STRUCTURE_EVENEMENTIELLE = 263;
    /**
     * Produit de terroir
     * Root 1
     * Code PDT
     */
    case PRODUIT_DE_TERROIR = 267;
    /**
     * Média
     * Root
     * Code ANX
     */
    case MEDIA = 268;
    /**
     * Point d’intérêt
     * Root 1
     * Code POI
     */
    case POINT_D_INTERET = 269;
    /**
     * Hébergements
     * Root 1
     * Code GHB
     */
    case HEBERGEMENTS = 270;
    /**
     * Contact privé
     * Root 1
     * Code CTD
     */
    case CONTACT_PRIVE = 513;
    /**
     * Suivi de contact
     * Root
     * Code SCT
     */
    case SUIVI_DE_CONTACT = 514;
    /**
     * Requête
     * Root
     * Code QRY
     */
    case REQUETE = 768;
    /**
     * Prototype
     * Root
     * Code PRO
     */
    case PROTOTYPE = 769;
    /**
     * Modèle
     * Root
     * Code TPL
     */
    case MODELE = 770;
}