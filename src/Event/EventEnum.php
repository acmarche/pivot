<?php

namespace AcMarche\Pivot\Event;

enum EventEnum: string
{
    case LOCAL = 'urn:val:visibilite:loc';
    case CONVENTION = 'urn:val:visibilite:conv';
    case PROVINCIAL = 'urn:val:visibilite:prov';
    case REGIONAL = 'urn:val:visibilite:reg';
    case INTERNATIONAL = 'urn:val:visibilite:int';
}
