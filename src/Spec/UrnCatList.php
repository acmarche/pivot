<?php

namespace AcMarche\Pivot\Spec;

/**
 * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/urn;fmt=json
 */
enum UrnCatList: string
{
    case COMMUNICATION = "urn:cat:moycom";
    case DESCRIPTION = "urn:cat:descmarket";
    case EQUIPEMENTS = "urn:cat:eqpsrv";
    case ACCUEIL = "urn:cat:accueil";
    case CLASS_LAB = "urn:cat:classlab";
}
