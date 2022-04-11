<?php

namespace AcMarche\Pivot\Entities\Response;

use AcMarche\Pivot\Entities\Offre\OffreShort;

class ResponseQuery
{
    public int $count = 0;
    /**
     * @var OffreShort[]
     */
    public array $offre;
}