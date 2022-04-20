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
    public function findByUrn(UrnList $keywordSearch, bool $like = false): array
    {
        $specs = [];

        foreach ($this->specs as $spec) {
            if ($like) {
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


    /**
     * @param string $keywordSearch
     * @param bool $value
     *
     * @return SpecData[]
     */
    public function findByUrnCat(UrnList $keywordSearch): array
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