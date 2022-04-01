<?php

namespace AcMarche\Pivot\Entities\Pivot;

class OffreItem
{
    public string $codeCgt;
    public string $dateCreation;
    public string $dateModification;
    /**
     * @var IdTypeOffre[] $typeOffre
     */
    public array $typeOffre;
}