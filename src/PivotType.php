<?php

namespace AcMarche\Pivot;

class PivotType
{
    /**
     * http://pivot.tourismewallonie.be/index.php/9-pivot-gest-pc/142-types-de-fiches-pivot
     * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr;fmt=json
     */
    /**
     * Hôtel
     * Root 1
     * Code HTL
     */
    public const HOTEL = 1;
    /**
     * Gîte
     * Root 1
     * Code GIT
     */
    public const GITE = 2;
    /**
     * Chambre d’hôtes
     * Root 1
     * Code CHB
     */
    public const CHAMBRE_D_HOTES = 3;
    /**
     * Meublé
     * Root 1
     * Code MBL
     */
    public const MEUBLE = 4;
    /**
     * Camping
     * Root 1
     * Code CMP
     */
    public const CAMPING = 5;
    /**
     * Budget Holiday
     * Root 1
     * Code BDG
     */
    public const BUDGET_HOLIDAY = 6;
    /**
     * Village de vacances
     * Root 1
     * Code VLG
     */
    public const VILLAGE_DE_VACANCES = 7;
    /**
     * Itinéraire
     * Root 1
     * Code ITB
     */
    public const ITINERAIRE = 8;
    /**
     * Événement
     * Root 1
     * Code EVT
     */
    public const EVENEMENT = 9;
    /**
     * Information de parcours
     * Root
     * Code IFP
     */
    public const INFORMATION_DE_PARCOURS = 10;
    /**
     * Découverte et Divertissement
     * Root 1
     * Code ALD
     */
    public const DECOUVERTE_ET_DIVERTISSEMENT = 11;
    /**
     * Guide touristique
     * Root 1
     * Code GTR
     */
    public const GUIDE_TOURISTIQUE = 12;
    /**
     * Article
     * Root 1
     * Code ART
     */
    public const ARTICLE = 13;
    /**
     * Organisme touristique
     * Root 1
     * Code OGT
     */
    public const ORGANISME_TOURISTIQUE = 14;
    /**
     * Forfait individuel
     * Root 1
     * Code FTI
     */
    public const FORFAIT_INDIVIDUEL = 15;
    /**
     * Forfait groupe
     * Root 1
     * Code FTG
     */
    public const FORFAIT_GROUPE = 16;
    /**
     * Agence de voyage
     * Root 1
     * Code AGV
     */
    public const AGENCE_DE_VOYAGE = 17;
    /**
     * MICE - Infrastructure
     * Root 1
     * Code MIF
     */
    public const MICE_INFRASTRUCTURE = 18;
    /**
     * MICE - Organisateur
     * Root 1
     * Code MOG
     */
    public const MICE_ORGANISATEUR = 19;
    /**
     * MICE - Prestataire
     * Root 1
     * Code MPR
     */
    public const MICE_PRESTATAIRE = 20;
    /**
     * MICE - Divertissement
     * Root 1
     * Code MDV
     */
    public const MICE_DIVERTISSEMENT = 21;
    /**
     * MICE - Animation
     * Root 1
     * Code MAM
     */
    public const MICE_ANIMATION = 22;
    /**
     * Contact public
     * Root 1
     * Code CTP
     */
    public const CONTACT_PUBLIC = 23;
    /**
     * Salle
     * Root
     * Code SAL
     */
    public const SALLE = 24;
    /**
     * Autre hébergement
     * Root 1
     * Code ATH
     */
    public const AUTRE_HEBERGEMENT = 25;
    /**
     * Aire pour motor-homes
     * Root 1
     * Code AMH
     */
    public const AIRE_POUR_MOTOR_HOMES = 26;
    /**
     * Structure associative
     * Root 1
     * Code ASC
     */
    public const STRUCTURE_ASSOCIATIVE = 27;
    /**
     * Endroit de camp
     * Root 1
     * Code EDC
     */
    public const ENDROIT_DE_CAMP = 28;
    /**
     * Point nœud
     * Root 1
     * Code PND
     */
    public const POINT_NOEUD = 29;
    /**
     * Tronçon
     * Root 1
     * Code TRN
     */
    public const TRONCON = 30;
    /**
     * Produit touristique
     * Root 1
     * Code PTR
     */
    public const PRODUIT_TOURISTIQUE = 31;
    /**
     * Aire de bivouac
     * Root 1
     * Code ABV
     */
    public const AIRE_DE_BIVOUAC = 32;
    /**
     * Zone de fermeture
     * Root 1
     * Code ZFM
     */
    public const ZONE_DE_FERMETURE = 33;
    /**
     * Groupe d’événements
     * Root 1
     * Code EGP
     */
    public const GROUPE_D_EVENEMENTS = 256;
    /**
     * Animation pédagogique
     * Root 1
     * Code APD
     */
    public const ANIMATION_PEDAGOGIQUE = 257;
    /**
     * Producteur
     * Root 1
     * Code PRD
     */
    public const PRODUCTEUR = 258;
    /**
     * Artisan
     * Root 1
     * Code ATS
     */
    public const ARTISAN = 259;
    /**
     * Boutique de terroir
     * Root 1
     * Code BTQ
     */
    public const BOUTIQUE_DE_TERROIR = 260;
    /**
     * Restauration
     * Root 1
     * Code RST
     */
    public const RESTAURATION = 261;
    /**
     * Recette
     * Root 1
     * Code RCT
     */
    public const RECETTE = 262;
    /**
     * Structure événementielle
     * Root 1
     * Code SMN
     */
    public const STRUCTURE_EVENEMENTIELLE = 263;
    /**
     * Produit de terroir
     * Root 1
     * Code PDT
     */
    public const PRODUIT_DE_TERROIR = 267;
    /**
     * Média
     * Root
     * Code ANX
     */
    public const MEDIA = 268;
    /**
     * Point d’intérêt
     * Root 1
     * Code POI
     */
    public const POINT_D_INTERET = 269;
    /**
     * Hébergements
     * Root 1
     * Code GHB
     */
    public const HEBERGEMENTS = 270;
    /**
     * Contact privé
     * Root 1
     * Code CTD
     */
    public const CONTACT_PRIVE = 513;
    /**
     * Suivi de contact
     * Root
     * Code SCT
     */
    public const SUIVI_DE_CONTACT = 514;
    /**
     * Requête
     * Root
     * Code QRY
     */
    public const REQUETE = 768;
    /**
     * Prototype
     * Root
     * Code PRO
     */
    public const PROTOTYPE = 769;
    /**
     * Modèle
     * Root
     * Code TPL
     */
    public const MODELE = 770;
}