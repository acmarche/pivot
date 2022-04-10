<?php

namespace AcMarche\Pivot\Entities\Pivot\Specification;

use AcMarche\Pivot\Entities\Pivot\SpecData;
use AcMarche\Pivot\Entities\Pivot\SpecInfo;

trait SpectTrait
{
    public ?string $homepage;
    public array $emails = [];
    public array $tels = [];
    /**
     * @var SpecInfo[]
     */
    public array $specsDetailed;
    public array $categories = [];
    public array $images = [];
    /**
     * @var SpecData[]
     */
    public array $descriptions = [];
    /**
     * @var SpecData[]
     */
    public array $tarifs = [];
}
