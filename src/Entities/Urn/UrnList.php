<?php

namespace AcMarche\Pivot\Urn;

class UrnList
{
    public static function hotel(): Urn
    {
        return new Urn(1, 'HTL', 'urn:typ:1', 'Hôtel');
    }

    public static function evenement(): Urn
    {
        return new Urn(9, 'EVT', 'urn:typ:9', 'Événement');
    }
}