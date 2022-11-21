<?php

namespace AcMarche\Pivot\Entities\Specification;

use AcMarche\Pivot\Entities\Tag;

trait SpectFieldsTrait
{
    public ?string $homepage = null;
    public array $emails = [];
    public array $tels = [];
    /**
     * @var Tag[] $tags
     */
    public array $tags = [];
    /**
     * @var string[] $images
     */
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
     * @var SpecData[] $equipements
     */
    public array $equipements = [];
    /**
     * @var SpecData[] $accueils
     */
    public array $accueils = [];
    /**
     * @var SpecData[] $webs
     */
    public array $webs = [];

    /**
     * @param string $language
     * @param string|null $skip
     * @return SpecData[]
     */
    public function descriptionsByLanguage(string $language = 'fr', ?string $skip = "descmarket30"): array
    {
        $descriptions = [];
        $startString = "$language:urn";
        if ($language === 'fr') {
            $startString = 'urn:';
        }
        foreach ($this->descriptions as $description) {
            if (str_starts_with($description->urn, $startString)) {
                if ($skip !== null) {
                    if (!str_contains($description->urn, $skip)) {
                        $descriptions[] = $description;
                    }
                } else {
                    $descriptions[] = $description;
                }
            }
        }

        return $descriptions;
    }

    public function descriptionByLanguage(string $language = 'fr', ?string $skip = "descmarket30"): ?string
    {
        $descriptions = $this->descriptionsByLanguage($language, $skip);
        if (count($descriptions) > 0) {
            return $descriptions[0]->value;
        }

        return null;
    }
}
