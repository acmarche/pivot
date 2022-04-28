<?php

namespace AcMarche\Pivot\Spec;

/**
 * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/urn;fmt=json
 */
enum UrnList: string
{
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
    case CATEGORIE_EVENT = "urn:fld:catevt";
    case WEB = "urn:fld:urlweb";
    case HADES_ID = "urn:fld:idhades";
    case ADRESSE_RUE = "urn:fld:adr";
    case VOIR_AUSSI = "urn:lnk:offre:voiraussi";
    case MEDIAS_PARTIAL = "urn:lnk:media";
    case MEDIAS_AUTRE = "urn:lnk:media:autre";
    case CONTACT_DIRECTION = "urn:lnk:contact:direction";
    case OFFRE_ENFANT = "urn:lnk:offre:enfant";
}
