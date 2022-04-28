<?php

namespace AcMarche\Pivot\Spec;

use AcMarche\Pivot\Entities\Urn\Urn;

/**
 * @see GenerateClass
 */
class UrnTypeList
{
    public static function hotel(): Urn
    {
        return new Urn(
            'urn:typ:1', 'HTL', 1, false, 'Type', 'Hôtel', '1'
        );
    }

    public static function gite(): Urn
    {
        return new Urn(
            'urn:typ:2', 'GIT', 2, false, 'Type', 'Gîte', '1'
        );
    }

    public static function chambreDHotes(): Urn
    {
        return new Urn(
            'urn:typ:3', 'CHB', 3, false, 'Type', 'Chambre d’hôtes', '1'
        );
    }

    public static function meuble(): Urn
    {
        return new Urn(
            'urn:typ:4', 'MBL', 4, false, 'Type', 'Meublé', '1'
        );
    }

    public static function camping(): Urn
    {
        return new Urn(
            'urn:typ:5', 'CMP', 5, false, 'Type', 'Camping', '1'
        );
    }

    public static function budgetHoliday(): Urn
    {
        return new Urn(
            'urn:typ:6', 'BDG', 6, false, 'Type', 'Budget Holiday', '1'
        );
    }

    public static function villageDeVacances(): Urn
    {
        return new Urn(
            'urn:typ:7', 'VLG', 7, false, 'Type', 'Village de vacances', '1'
        );
    }

    public static function itineraire(): Urn
    {
        return new Urn(
            'urn:typ:8', 'ITB', 8, false, 'Type', 'Itinéraire', '1'
        );
    }

    public static function evenement(): Urn
    {
        return new Urn(
            'urn:typ:9', 'EVT', 9, false, 'Type', 'Événement', '1'
        );
    }

    public static function informationDeParcours(): Urn
    {
        return new Urn(
            'urn:typ:10', 'IFP', 10, false, 'Type', 'Information de parcours', ''
        );
    }

    public static function decouverteEtDivertissement(): Urn
    {
        return new Urn(
            'urn:typ:11', 'ALD', 11, false, 'Type', 'Découverte et Divertissement', '1'
        );
    }

    public static function guideTouristique(): Urn
    {
        return new Urn(
            'urn:typ:12', 'GTR', 12, false, 'Type', 'Guide touristique', '1'
        );
    }

    public static function article(): Urn
    {
        return new Urn(
            'urn:typ:13', 'ART', 13, false, 'Type', 'Article', '1'
        );
    }

    public static function organismeTouristique(): Urn
    {
        return new Urn(
            'urn:typ:14', 'OGT', 14, false, 'Type', 'Organisme touristique', '1'
        );
    }

    public static function forfaitIndividuel(): Urn
    {
        return new Urn(
            'urn:typ:15', 'FTI', 15, false, 'Type', 'Forfait individuel', '1'
        );
    }

    public static function forfaitGroupe(): Urn
    {
        return new Urn(
            'urn:typ:16', 'FTG', 16, false, 'Type', 'Forfait groupe', '1'
        );
    }

    public static function agenceDeVoyage(): Urn
    {
        return new Urn(
            'urn:typ:17', 'AGV', 17, false, 'Type', 'Agence de voyage', '1'
        );
    }

    public static function mICEInfrastructure(): Urn
    {
        return new Urn(
            'urn:typ:18', 'MIF', 18, false, 'Type', 'MICE - Infrastructure', '1'
        );
    }

    public static function mICEOrganisateur(): Urn
    {
        return new Urn(
            'urn:typ:19', 'MOG', 19, false, 'Type', 'MICE - Organisateur', '1'
        );
    }

    public static function mICEPrestataire(): Urn
    {
        return new Urn(
            'urn:typ:20', 'MPR', 20, false, 'Type', 'MICE - Prestataire', '1'
        );
    }

    public static function mICEDivertissement(): Urn
    {
        return new Urn(
            'urn:typ:21', 'MDV', 21, false, 'Type', 'MICE - Divertissement', '1'
        );
    }

    public static function mICEAnimation(): Urn
    {
        return new Urn(
            'urn:typ:22', 'MAM', 22, false, 'Type', 'MICE - Animation', '1'
        );
    }

    public static function contactPublic(): Urn
    {
        return new Urn(
            'urn:typ:23', 'CTP', 23, false, 'Type', 'Contact public', '1'
        );
    }

    public static function salle(): Urn
    {
        return new Urn(
            'urn:typ:24', 'SAL', 24, false, 'Type', 'Salle', ''
        );
    }

    public static function autreHebergement(): Urn
    {
        return new Urn(
            'urn:typ:25', 'ATH', 25, false, 'Type', 'Autre hébergement', '1'
        );
    }

    public static function airePourMotorHomes(): Urn
    {
        return new Urn(
            'urn:typ:26', 'AMH', 26, false, 'Type', 'Aire pour motor-homes', '1'
        );
    }

    public static function structureAssociative(): Urn
    {
        return new Urn(
            'urn:typ:27', 'ASC', 27, false, 'Type', 'Structure associative', '1'
        );
    }

    public static function endroitDeCamp(): Urn
    {
        return new Urn(
            'urn:typ:28', 'EDC', 28, false, 'Type', 'Endroit de camp', '1'
        );
    }

    public static function pointNoeud(): Urn
    {
        return new Urn(
            'urn:typ:29', 'PND', 29, false, 'Type', 'Point nœud', '1'
        );
    }

    public static function troncon(): Urn
    {
        return new Urn(
            'urn:typ:30', 'TRN', 30, false, 'Type', 'Tronçon', '1'
        );
    }

    public static function produitTouristique(): Urn
    {
        return new Urn(
            'urn:typ:31', 'PTR', 31, false, 'Type', 'Produit touristique', '1'
        );
    }

    public static function aireDeBivouac(): Urn
    {
        return new Urn(
            'urn:typ:32', 'ABV', 32, false, 'Type', 'Aire de bivouac', '1'
        );
    }

    public static function zoneDeFermeture(): Urn
    {
        return new Urn(
            'urn:typ:33', 'ZFM', 33, false, 'Type', 'Zone de fermeture', '1'
        );
    }

    public static function groupeDEvenements(): Urn
    {
        return new Urn(
            'urn:typ:256', 'EGP', 256, false, 'Type', 'Groupe d’événements', '1'
        );
    }

    public static function animationPedagogique(): Urn
    {
        return new Urn(
            'urn:typ:257', 'APD', 257, false, 'Type', 'Animation pédagogique', '1'
        );
    }

    public static function producteur(): Urn
    {
        return new Urn(
            'urn:typ:258', 'PRD', 258, false, 'Type', 'Producteur', '1'
        );
    }

    public static function artisan(): Urn
    {
        return new Urn(
            'urn:typ:259', 'ATS', 259, false, 'Type', 'Artisan', '1'
        );
    }

    public static function boutiqueDeTerroir(): Urn
    {
        return new Urn(
            'urn:typ:260', 'BTQ', 260, false, 'Type', 'Boutique de terroir', '1'
        );
    }

    public static function restauration(): Urn
    {
        return new Urn(
            'urn:typ:261', 'RST', 261, false, 'Type', 'Restauration', '1'
        );
    }

    public static function recette(): Urn
    {
        return new Urn(
            'urn:typ:262', 'RCT', 262, false, 'Type', 'Recette', '1'
        );
    }

    public static function structureEvenementielle(): Urn
    {
        return new Urn(
            'urn:typ:263', 'SMN', 263, false, 'Type', 'Structure événementielle', '1'
        );
    }

    public static function produitDeTerroir(): Urn
    {
        return new Urn(
            'urn:typ:267', 'PDT', 267, false, 'Type', 'Produit de terroir', '1'
        );
    }

    public static function media(): Urn
    {
        return new Urn(
            'urn:typ:268', 'ANX', 268, false, 'Type', 'Média', ''
        );
    }

    public static function pointDInteret(): Urn
    {
        return new Urn(
            'urn:typ:269', 'POI', 269, false, 'Type', 'Point d’intérêt', '1'
        );
    }

    public static function hebergements(): Urn
    {
        return new Urn(
            'urn:typ:270', 'GHB', 270, false, 'Type', 'Hébergements', '1'
        );
    }

    public static function contactPrive(): Urn
    {
        return new Urn(
            'urn:typ:513', 'CTD', 513, false, 'Type', 'Contact privé', '1'
        );
    }

    public static function suiviDeContact(): Urn
    {
        return new Urn(
            'urn:typ:514', 'SCT', 514, false, 'Type', 'Suivi de contact', ''
        );
    }

    public static function requete(): Urn
    {
        return new Urn(
            'urn:typ:768', 'QRY', 768, false, 'Type', 'Requête', ''
        );
    }

    public static function prototype(): Urn
    {
        return new Urn(
            'urn:typ:769', 'PRO', 769, false, 'Type', 'Prototype', ''
        );
    }

    public static function modele(): Urn
    {
        return new Urn(
            'urn:typ:770', 'TPL', 770, false, 'Type', 'Modèle', ''
        );
    }

    public static function getAllCode(): array
    {
        return [
            'HTL',
            'GIT',
            'CHB',
            'MBL',
            'CMP',
            'BDG',
            'VLG',
            'ITB',
            'EVT',
            'IFP',
            'ALD',
            'GTR',
            'ART',
            'OGT',
            'FTI',
            'FTG',
            'AGV',
            'MIF',
            'MOG',
            'MPR',
            'MDV',
            'MAM',
            'CTP',
            'SAL',
            'ATH',
            'AMH',
            'ASC',
            'EDC',
            'PND',
            'TRN',
            'PTR',
            'ABV',
            'ZFM',
            'EGP',
            'APD',
            'PRD',
            'ATS',
            'BTQ',
            'RST',
            'RCT',
            'SMN',
            'PDT',
            'ANX',
            'POI',
            'GHB',
            'CTD',
            'SCT',
            'QRY',
            'PRO',
            'TPL',
            'HBG'//gloriette inconnu
        ];
    }
}