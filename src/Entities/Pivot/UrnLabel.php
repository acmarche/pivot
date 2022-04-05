<?php

namespace AcMarche\Pivot\Entities\Pivot;

/**
 * Ne contient que l'urn et les labels
 */
class UrnLabel
{
    use LabelTrait;

    public string $urn;

}
