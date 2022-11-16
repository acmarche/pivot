<?php

namespace AcMarche\Pivot\Spec;

/**
 * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/urn;fmt=json
 * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/typeofr/261/urn:fld:specculi;fmt=json
 */
enum UrnList: string
{
    case EQUIPEMENTS = "urn:cat:eqpsrv";
    case DESCRIPTION = "urn:fld:descmarket";
    case DESCRIPTION_SHORT = "descmarket";
    case TARIF = "urn:fld:tarifcplt";
    case NOMO = "urn:fld:nomofr";
    case DATE_DEB_VALID = 'urn:fld:datedebvalid';
    case DATE_DEB = 'urn:fld:date:datedeb';
    case DATE_FIN_VALID = "urn:fld:datefinvalid";
    case HOMEPAGE = "urn:fld:homepage";
    case ACTIVE = "urn:fld:typeevt:activrecur";
    case DATE_OBJECT = "urn:obj:date";
    case URL = "urn:fld:url";
    case POIS = "urn:lnk:offre:poi";
    case CATEGORIE_EVENT = "urn:fld:catevt";
    case CATEGORIE_PDT = "urn:fld:catpdt";//produit du terroir
    case CATEGORIE_PRD = "urn:fld:catprd";//producteur
    case CATEGORIE_ATS = "urn:fld:catats";//artisan
    case CATEGORIE_DECOUVERTE = "urn:fld:catdec";
    case CATEGORIE_PATRIMOINE_BATI = "urn:fld:catdec:patrbati";
    case CATEGORIE = "urn:fld:cat";
    case WEB = "urn:fld:urlweb";
    case FACEBOOK = "urn:fld:urlfacebook";
    case TRIPADVISOR = "urn:fld:tripadvisor";
    case HADES_ID = "urn:fld:idhades";
    case ADRESSE_RUE = "urn:fld:adr";
    case VOIR_AUSSI = "urn:lnk:offre:voiraussi";
    case MEDIAS_PARTIAL = "urn:lnk:media";
    case MEDIAS_AUTRE = "urn:lnk:media:autre";
    case MEDIA_DEFAULT = "urn:lnk:media:defaut";
    case CONTACT_DIRECTION = "urn:lnk:contact:direction";
    case OFFRE_ENFANT = "urn:lnk:offre:enfant";
    case PATRIMOINE_NATUREL = "urn:fld:catdec:patrnat";
    case CLASSIFICATION_LABEL = "urn:cat:classlab";
    case CLASSIFICATION = "urn:cat:classlab:classif";
    case HERGEMENT = "urn:fam:1";
    case CULINAIRE = "urn:fld:specculi";
}
