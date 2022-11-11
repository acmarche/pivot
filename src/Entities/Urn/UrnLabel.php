<?php

namespace AcMarche\Pivot\Entities\Urn;

use AcMarche\Pivot\Entities\LabelTrait;

/**
 * Ne contient que l'urn et les labels
 */
class UrnLabel
{
    use LabelTrait;

    public ?string $urn = null;

}
