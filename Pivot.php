<?php

namespace AcMarche\Pivot;
/**
 * http://w3.ftlb.be/wiki/index.php/Liste_des_cat%C3%A9gories
 * https://visitmarche.be/fr/index-des-offres/
 */
class Pivot
{
    /**
     * 0 = (valeur par défaut) génère des offres ne contenant que le codeCgt et les dates de
     * création et de dernière modification.
     * • 1 = génère des « résumés » d’offres, ne contenant que le codeCgt, le nom, l’adresse et la
     * géolocalisation, ainsi que le classement, le label Qualité Wallonie et média par défaut
     * associé à l’offre.
     * • 2 = produit des offres au contenu complet. Les offres filles des relations ne sont
     * représentées que par leur codeCgt.
     * • 3 = produit les offres au contenu complet, avec également un contenu complet pour les
     * offres liées.
     */
    public const QUERY_DETAIL_LVL_DEFAULT = 0;
    public const QUERY_DETAIL_LVL_RESUME = 1;
    public const QUERY_DETAIL_LVL_COMPLET = 2;
    public const QUERY_DETAIL_LVL_LIES = 3;

    /**
     * Le paramètre matriciel content permet de modifier la richesse du contenu produit.
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
    public const OFFER_DETAIL_LVL_CGT = 0;
    public const OFFER_DETAIL_LVL_RESUME = 1;
    public const OFFER_DETAIL_LVL_DEFAULT = 2;
    public const OFFER_DETAIL_LVL_LIES = 3;
    public const OFFER_DETAIL_LVL_OPEND_DATA = 4;

    public const FORMAT_JSON = 'application/json';
    public const FORMAT_XML = 'application/xml';
    public const FORMAT_KML = 'application/vnd.google-earth.kml+xml';
    public const FORMAT_ATOM = 'application/atom+xml';

    public const ACTIVE_ARCHIVE = 5; //= archivée
    public const ACTIVE_NOT_PUBLIABLE = 10; //= non publiable
    public const ACTIVE_EDITON = 20; //en édition
    public const ACTIVE_PUBLIABLE = 30; // = publiable

    public const VISIBILITY_LOCAL = 10; // local (territoire d’une maison de tourisme)
    public const VISIBILITY_BUREAU = 15; //  convention bureau (territoire d’un convention bureau) – réservé au MICE
    public const VISIBILITY_PROVINCIAL = 20; //  provincial
    public const VISIBILITY_REGIONAL = 30; //  régional
    public const VISIBILITY_INTERNATIONAL = 40; //  international
}
