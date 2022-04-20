<?php

namespace AcMarche\Pivot\Entities\Specification;

use AcMarche\Pivot\Entities\Category;

trait SpectFieldsTrait
{
    public ?string $homepage;
    public array $emails = [];
    public array $tels = [];
    /**
     * @var SpecInfo[] $specsDetailed
     */
    public array $specsDetailed;
    /**
     * @var Category[] $categories
     */
    public array $categories = [];
    public array $images = [];
    /**
     * @var SpecData[] $descriptions
     */
    public array $descriptions = [];
    /**
     * @var SpecData[] $tarifs
     */
    public array $tarifs = [];
    /**
     * @var SpecData[] $communications
     */
    public array $communications = [];
    /**
     * @var SpecData[] $webs
     */
    public array $webs = [];
}
