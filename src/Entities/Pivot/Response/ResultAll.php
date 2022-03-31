<?php

namespace AcMarche\Pivot\Entities\Pivot\Response;

use AcMarche\Pivot\Entities\Pivot\OffreShort;

class ResultAll
{
    public int $count = 0;
    /**
     * @var OffreShort[]
     */
    public array $offre;
}