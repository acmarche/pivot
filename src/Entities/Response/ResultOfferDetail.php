<?php

namespace AcMarche\Pivot\Entities\Response;

use AcMarche\Pivot\Entities\Offre\Offre;

class ResultOfferDetail
{
    /**
     * La donnée au format original
     * @var string $data
     */
    public string $data;

    public int $count = 0;
    /**
     * @var Offre[]
     */
    public array $offre;

    public function getOffre(): ?Offre
    {
        if ($this->count > 0) {
            return $this->offre[0];
        }

        return null;
    }
}