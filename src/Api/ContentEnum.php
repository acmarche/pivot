<?php

namespace AcMarche\Pivot\Api;

/**
 * • 0 = génère une offre ne contenant que le codeCgt et les dates de création et de dernière
 * modification. Ce mode est utile pour consulter la date de dernière modification d’une offre
 * • 1 = génère un « résumé » d’offre, ne contenant que le codeCgt, le nom, l’adresse et la
 * géolocalisation, ainsi que le classement, le label Qualité Wallonie, et le média par défaut
 * associé à l’offre. Ce mode est utile pour produire des listes de résultats de recherche
 * • 2 = (valeur par défaut) produit le contenu complet de l’offre demandée. Les offres liées à
 * l’offre ne sont représentées que par leur codeCgt.
 * • 3 = produit le contenu de l’offre demandée, ainsi que celui de toutes les offres liées à
 * l’offre. Dans ce cas, le détail des offres filles en relation est aussi généré.
 * • 4 = génère le contenu OpenData d’offre, ne contenant que le codeCgt, le nom, l’adresse et
 * la géolocalisation, les moyens de communication ainsi que le classement, le label Qualité
 * Wallonie, le descriptif commercial, la période et horaire d’ouverture, les équipements et
 * services et le média par défaut associé à l’offre.
 */
enum ContentEnum: int
{
    case LVL0 = 0;
    case LVL1 = 1;
    case LVL2 = 2;
    case LVL3 = 3;
    case LVL4 = 4;

    public const LVL_DEFAULT = self::LVL2;
}
