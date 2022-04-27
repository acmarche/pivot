<?php

namespace AcMarche\Pivot\Spec;

/**
 * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/urn;fmt=json
 */
enum UrnCatList: string
{
    case CATEGORIE = "urn:cat:classlab";
    case COMMUNICATION = "urn:cat:moycom";
    case DESCRIPTION = "urn:cat:descmarket";
}
