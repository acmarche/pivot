<?php

namespace AcMarche\Pivot\Spec;

/**
 * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/urn;fmt=json
 */
enum UrnCatList: string
{
    case COMMUNICATION = "urn:cat:moycom";
    case DESCRIPTION = "urn:cat:descmarket";
    case DESCRIPTION_30 = "urn:cat:descmarket30";
    case DESCRIPTION_MARKETING = "urn:obj:descmarkettgt";
    case DESCRIPTION_CIRCUIT = "urn:fld:desccirc";
    case EQUIPEMENTS = "urn:cat:eqpsrv";
    case ACCUEIL = "urn:cat:accueil";
    case CLASS_LAB = "urn:cat:classlab";
    case SIGNAL = "urn:fld:signal";
}
