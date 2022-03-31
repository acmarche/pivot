<?php

namespace AcMarche\Pivot\Entities\Pivot\Response;

use AcMarche\Pivot\Entities\Pivot\Offer;

class ResultOfferDetail
{
    public int $count = 0;
    /**
     * @var Offer[]
     */
    public array $offre;
}