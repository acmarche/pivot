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

    public function getOffre(): ?Offer
    {
        if ($this->count > 0) {
            return $this->offre[0];
        }

        return null;
    }
}