<?php

namespace AcMarche\Pivot\Entities;

/**
 * Ne contient que l'urn et les labels
 */
class UrnLabel
{
    use LabelTrait;

    public ?string $urn = null;

}
