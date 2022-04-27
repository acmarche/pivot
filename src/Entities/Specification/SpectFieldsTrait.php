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

    /**
     * @param string $language
     * @return SpecData[]
     */
    public function descriptionsByLanguage(string $language = 'fr'): array
    {
        $descriptions = [];
        foreach ($this->descriptions as $description) {
            if (str_starts_with($language.':urn', $description->urn)) {
                $descriptions[] = $description;
            }
        }

        return $descriptions;
    }
}
