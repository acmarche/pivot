<?php

namespace AcMarche\Pivot\Spec;

use AcMarche\Pivot\Entities\Specification\SpecData;

trait SpecSearchTrait
{
    /**
     * @param SpecData[] $specs
     */
    public array $specs = [];

    /**
     * @return SpecData[]
     */
    public function findByUrn(string $keywordSearch, string $property = "urn", bool $contains = false): array
    {
        $specs = [];
        foreach ($this->specs as $spec) {
            if (!isset($spec->$property)) {
                continue;
            }
            if ($contains) {
                if (\str_contains($spec->$property, $keywordSearch)) {
                    $specs[] = $spec;
                }
                continue;
            }
            if ($spec->$property === $keywordSearch) {
                $specs[] = $spec;
            }
        }

        return $specs;
    }

    public function findByUrnReturnValue(string $urnName): ?string
    {
        $specs = $this->findByUrn($urnName);
        if (count($specs) > 0) {
            return $specs[0]->value;
        }

        return null;
    }

}