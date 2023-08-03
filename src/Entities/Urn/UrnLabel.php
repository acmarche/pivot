<?php

namespace AcMarche\Pivot\Entities\Urn;

use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Entities\LabelTrait;

/**
 * Ne contient que l'urn et les labels
 */
class UrnLabel
{
    use LabelTrait;

    /**
     * Be careful duplicate from LabelTrait voluntary !
     * Bug deserialize
     * @var Label[] $label
     */
    public array $label = [];
    public ?string $urn = null;
}
