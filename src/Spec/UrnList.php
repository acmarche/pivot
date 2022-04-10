<?php

namespace AcMarche\Pivot\Spec;

/**
 * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/urn;fmt=json
 */
enum UrnList: string
{
    case DESCRIPTION = "urn:fld:descmarket";
    case DESCRIPTION_NL = "nl:urn:fld:descmarket";
    case DESCRIPTION_SHORT = "descmarket";
    case TARIF = "urn:fld:tarifcplt";
    case NOMO = "urn:fld:nomofr";
    case MEDIA = "urn:lnk:media:autre";
    case DATE_DEB_VALID = 'urn:fld:datedebvalid';
    case DATE_DEB = 'urn:fld:date:datedeb';
    case DATE_FIN_VALID = "urn:fld:datefinvalid";
    case HOMEPAGE = "urn:fld:homepage";
    case ACTIVE = "urn:fld:typeevt:activrecur";
    case DATE_OBJECT = "urn:obj:date";
    case URL = "urn:fld:url";
    case CATEGORIE = "urn:cat:classlab";
}
