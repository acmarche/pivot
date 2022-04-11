<?php

namespace AcMarche\Pivot\Entities\Specification;

trait SpectFieldsTrait
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
