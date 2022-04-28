<?php

namespace AcMarche\Pivot\Api;

/**
 *
 * • XML (par défaut) : contenu xml respectant le schéma pivot-offer.xsd
 * • JSON : contenu json respectant le schéma pivot-offer.xsd
 * • HTML (expérimental) : page HTML de présentation de l’offre
 * • KML : contenu xml respectant le schéma www.opengis.net/kml/2.2 de Google Earth
 * • ATOM : contenu xml conçu pour la syndication de contenu périodique (disponible
 * actuellement que pour les offres de type événements)
 */
enum FormatEnum: string
{
    case JSON_HEADER = 'application/json';
    case XML_HEADER = 'application/xml';
    case KML_HEADER = 'application/vnd.google-earth.kml+xml';
    case ATOM_HEADER = 'application/atom+xml';
    case HTML_HEADER = 'text/html';

    case JSON_FTM = 'json';
    case XML_FMT = 'xml';
    case KML_FMT = 'kml';
    case ATOM_FMT = 'atom';
    case ATOM_HTML = 'html';

    case FORMAT_GZIP = 'Accept-Encoding: gzip';
}