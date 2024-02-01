<?php

namespace AcMarche\Pivot\Event;

enum EventEnum: string
{
    case LOCAL = 'loc';
    case CONVENTION = 'conv';
    case PROVINCIAL = 'prov';
    case REGIONAL = 'reg';
    case INTERNATIONAL = 'int';
}
