<?php

namespace AcMarche\Pivot\Spec;

use AcMarche\Pivot\Entities\Urn\Urn;

class UrnTypeList: int
{
    public static function HOTEL(): Urn
    {
        return new Urn(
            'urn:typ:1', 'HTL', 1, false, 'Type', 'Hôtel', true
        );
    }

    public static function GITE(): Urn
    {
        return new Urn(
            'urn:typ:2', 'GIT', 2, false, 'Type', 'Gîte', true
        );
    }

    public static function CHAMBRE_D_HOTES(): Urn
    {
        return new Urn(
            'urn:typ:3', 'CHB', 3, false, 'Type', 'Chambre d’hôtes', true
        );
    }

    public static function MEUBLE(): Urn
    {
        return new Urn(
            'urn:typ:4', 'MBL', 4, false, 'Type', 'Meublé', true
        );
    }

    public static function CAMPING(): Urn
    {
        return new Urn(
            'urn:typ:5', 'CMP', 5, false, 'Type', 'Camping', true
        );
    }

    public static function BUDGET_HOLIDAY(): Urn
    {
        return new Urn(
            'urn:typ:6', 'BDG', 6, false, 'Type', 'Budget Holiday', true
        );
    }

    public static function VILLAGE_DE_VACANCES(): Urn
    {
        return new Urn(
            'urn:typ:7', 'VLG', 7, false, 'Type', 'Village de vacances', true
        );
    }

    public static function ITINERAIRE(): Urn
    {
        return new Urn(
            'urn:typ:8', 'ITB', 8, false, 'Type', 'Itinéraire', true
        );
    }

    public static function EVENEMENT(): Urn
    {
        return new Urn(
            'urn:typ:9', 'EVT', 9, false, 'Type', 'Événement', true
        );
    }

    public static function INFORMATION_DE_PARCOURS(): Urn
    {
        return new Urn(
            'urn:typ:10', 'IFP', 10, false, 'Type', 'Information de parcours', false
        );
    }

    public static function DECOUVERTE_ET_DIVERTISSEMENT(): Urn
    {
        return new Urn(
            'urn:typ:11', 'ALD', 11, false, 'Type', 'Découverte et Divertissement', true
        );
    }

    public static function GUIDE_TOURISTIQUE(): Urn
    {
        return new Urn(
            'urn:typ:12', 'GTR', 12, false, 'Type', 'Guide touristique', true
        );
    }

    public static function ARTICLE(): Urn
    {
        return new Urn(
            'urn:typ:13', 'ART', 13, false, 'Type', 'Article', true
        );
    }

    public static function ORGANISME_TOURISTIQUE(): Urn
    {
        return new Urn(
            'urn:typ:14', 'OGT', 14, false, 'Type', 'Organisme touristique', true
        );
    }

    public static function FORFAIT_INDIVIDUEL(): Urn
    {
        return new Urn(
            'urn:typ:15', 'FTI', 15, false, 'Type', 'Forfait individuel', true
        );
    }

    public static function FORFAIT_GROUPE(): Urn
    {
        return new Urn(
            'urn:typ:16', 'FTG', 16, false, 'Type', 'Forfait groupe', true
        );
    }

    public static function AGENCE_DE_VOYAGE(): Urn
    {
        return new Urn(
            'urn:typ:17', 'AGV', 17, false, 'Type', 'Agence de voyage', true
        );
    }

    public static function MICE_INFRASTRUCTURE(): Urn
    {
        return new Urn(
            'urn:typ:18', 'MIF', 18, false, 'Type', 'MICE - Infrastructure', true
        );
    }

    public static function MICE_ORGANISATEUR(): Urn
    {
        return new Urn(
            'urn:typ:19', 'MOG', 19, false, 'Type', 'MICE - Organisateur', true
        );
    }

    public static function MICE_PRESTATAIRE(): Urn
    {
        return new Urn(
            'urn:typ:20', 'MPR', 20, false, 'Type', 'MICE - Prestataire', true
        );
    }

    public static function MICE_DIVERTISSEMENT(): Urn
    {
        return new Urn(
            'urn:typ:21', 'MDV', 21, false, 'Type', 'MICE - Divertissement', true
        );
    }

    public static function MICE_ANIMATION(): Urn
    {
        return new Urn(
            'urn:typ:22', 'MAM', 22, false, 'Type', 'MICE - Animation', true
        );
    }

    public static function CONTACT_PUBLIC(): Urn
    {
        return new Urn(
            'urn:typ:23', 'CTP', 23, false, 'Type', 'Contact public', true
        );
    }

    public static function SALLE(): Urn
    {
        return new Urn(
            'urn:typ:24', 'SAL', 24, false, 'Type', 'Salle', false
        );
    }

    public static function AUTRE_HEBERGEMENT(): Urn
    {
        return new Urn(
            'urn:typ:25', 'ATH', 25, false, 'Type', 'Autre hébergement', true
        );
    }

    public static function AIRE_POUR_MOTOR_HOMES(): Urn
    {
        return new Urn(
            'urn:typ:26', 'AMH', 26, false, 'Type', 'Aire pour motor-homes', true
        );
    }

    public static function STRUCTURE_ASSOCIATIVE(): Urn
    {
        return new Urn(
            'urn:typ:27', 'ASC', 27, false, 'Type', 'Structure associative', true
        );
    }

    public static function ENDROIT_DE_CAMP(): Urn
    {
        return new Urn(
            'urn:typ:28', 'EDC', 28, false, 'Type', 'Endroit de camp', true
        );
    }

    public static function POINT_NOEUD(): Urn
    {
        return new Urn(
            'urn:typ:29', 'PND', 29, false, 'Type', 'Point nœud', true
        );
    }

    public static function TRONCON(): Urn
    {
        return new Urn(
            'urn:typ:30', 'TRN', 30, false, 'Type', 'Tronçon', true
        );
    }

    public static function PRODUIT_TOURISTIQUE(): Urn
    {
        return new Urn(
            'urn:typ:31', 'PTR', 31, false, 'Type', 'Produit touristique', true
        );
    }

    public static function AIRE_DE_BIVOUAC(): Urn
    {
        return new Urn(
            'urn:typ:32', 'ABV', 32, false, 'Type', 'Aire de bivouac', true
        );
    }

    public static function ZONE_DE_FERMETURE(): Urn
    {
        return new Urn(
            'urn:typ:33', 'ZFM', 33, false, 'Type', 'Zone de fermeture', true
        );
    }

    public static function GROUPE_D_EVENEMENTS(): Urn
    {
        return new Urn(
            'urn:typ:256', 'EGP', 256, false, 'Type', 'Groupe d’événements', true
        );
    }

    public static function ANIMATION_PEDAGOGIQUE(): Urn
    {
        return new Urn(
            'urn:typ:257', 'APD', 257, false, 'Type', 'Animation pédagogique', true
        );
    }

    public static function PRODUCTEUR(): Urn
    {
        return new Urn(
            'urn:typ:258', 'PRD', 258, false, 'Type', 'Producteur', true
        );
    }

    public static function ARTISAN(): Urn
    {
        return new Urn(
            'urn:typ:259', 'ATS', 259, false, 'Type', 'Artisan', true
        );
    }

    public static function BOUTIQUE_DE_TERROIR(): Urn
    {
        return new Urn(
            'urn:typ:260', 'BTQ', 260, false, 'Type', 'Boutique de terroir', true
        );
    }

    public static function RESTAURATION(): Urn
    {
        return new Urn(
            'urn:typ:261', 'RST', 261, false, 'Type', 'Restauration', true
        );
    }

    public static function RECETTE(): Urn
    {
        return new Urn(
            'urn:typ:262', 'RCT', 262, false, 'Type', 'Recette', true
        );
    }

    public static function STRUCTURE_EVENEMENTIELLE(): Urn
    {
        return new Urn(
            'urn:typ:263', 'SMN', 263, false, 'Type', 'Structure événementielle', true
        );
    }

    public static function PRODUIT_DE_TERROIR(): Urn
    {
        return new Urn(
            'urn:typ:267', 'PDT', 267, false, 'Type', 'Produit de terroir', true
        );
    }

    public static function MEDIA(): Urn
    {
        return new Urn(
            'urn:typ:268', 'ANX', 268, false, 'Type', 'Média', false
        );
    }

    public static function POINT_D_INTERET(): Urn
    {
        return new Urn(
            'urn:typ:269', 'POI', 269, false, 'Type', 'Point d’intérêt', true
        );
    }

    public static function HEBERGEMENTS(): Urn
    {
        return new Urn(
            'urn:typ:270', 'GHB', 270, false, 'Type', 'Hébergements', true
        );
    }

    public static function CONTACT_PRIVE(): Urn
    {
        return new Urn(
            'urn:typ:513', 'CTD', 513, false, 'Type', 'Contact privé', true
        );
    }

    public static function SUIVI_DE_CONTACT(): Urn
    {
        return new Urn(
            'urn:typ:514', 'SCT', 514, false, 'Type', 'Suivi de contact', false
        );
    }

    public static function REQUETE(): Urn
    {
        return new Urn(
            'urn:typ:768', 'QRY', 768, false, 'Type', 'Requête', false
        );
    }

    public static function PROTOTYPE(): Urn
    {
        return new Urn(
            'urn:typ:769', 'PRO', 769, false, 'Type', 'Prototype', false
        );
    }

    public static function MODELE(): Urn
    {
        return new Urn(
            'urn:typ:770', 'TPL', 770, false, 'Type', 'Modèle', false
        );
    }
}