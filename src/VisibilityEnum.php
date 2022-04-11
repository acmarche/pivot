<?php

namespace AcMarche\Pivot;

enum VisibilityEnum: int
{
    case ACTIVE_ARCHIVE = 5; //= archivée
    case ACTIVE_NOT_PUBLIABLE = 10; //= non publiable
    case ACTIVE_EDITON = 20; //en édition
    case ACTIVE_PUBLIABLE = 30; // = publiable

 //   case VISIBILITY_LOCAL = 10; // local (territoire d’une maison de tourisme)
    case VISIBILITY_BUREAU = 15; //  convention bureau (territoire d’un convention bureau) – réservé au MICE
 //   case VISIBILITY_PROVINCIAL = 20; //  provincial
 //   case VISIBILITY_REGIONAL = 30; //  régional
    case VISIBILITY_INTERNATIONAL = 40; //  international
}