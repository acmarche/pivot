<?php

namespace AcMarche\Pivot\Spec;

use AcMarche\Pivot\Entities\Specification\SpecData;

trait SpecTrait
{
    /**
     * @param SpecData[] $specs
     */
    public array $specs = [];

    /**
     * @return SpecData[]
     */
    public function findByUrn(UrnList $keywordSearch, bool $contains = false): array
    {
        $specs = [];

        foreach ($this->specs as $spec) {
            if ($contains) {
                if (\str_contains($spec->urn, $keywordSearch->value)) {
                    $specs[] = $spec;
                }
                continue;
            }
            if ($spec->urn === $keywordSearch->value) {
                $specs[] = $spec;
            }
        }

        return $specs;
    }

    public function findByUrnReturnValue(UrnList $urnName): ?string
    {
        $specs = $this->findByUrn($urnName);
        if (count($specs) > 0) {
            return $specs[0]->value;
        }

        return null;
    }


    /**
     * @param string $keywordSearch
     * @param bool $value
     *
     * @return SpecData[]
     */
    public function findByUrnCat(UrnCatList $keywordSearch): array
    {
        $data = [];
        foreach ($this->specs as $spec) {
            if (isset($spec->urnCat) && $spec->urnCat === $keywordSearch->value) {
                $data[] = $spec;
            }
        }

        return $data;
    }

    /**
     * @param SpecTypeEnum $keywordSearch
     *
     * @return SpecData[]
     */
    public function findByType(SpecTypeEnum $keywordSearch): array
    {
        $values = [];
        foreach ($this->specs as $spec) {
            if ($spec->type === $keywordSearch->value) {
                $values[] = $spec;
            }
        }

        return $values;
    }

}