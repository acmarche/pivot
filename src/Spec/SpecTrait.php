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
     * @param string $key
     * @param bool $value
     * @return SpecData|string|null
     */
    public function getByUrn(UrnList $key, bool $value = false): SpecData|string|null
    {
        foreach ($this->specs as $spec) {
            if ($spec->urn === $key->value) {
                if ($value) {
                    return $spec->value;
                }

                return $spec;
            }
        }

        return null;
    }

    /**
     * @return SpecData[]
     */
    public function getByUrns(UrnList $key, bool $like = false): array
    {
        $specs = [];

        foreach ($this->specs as $spec) {
            if ($like) {
                if (str_contains($key->value, $spec->urn)) {
                    $specs[] = $spec;
                }
                continue;
            }
            if ($spec->urn === $key->value) {
                $specs[] = $spec;
            }
        }

        return $specs;
    }


    /**
     * @param string $key
     * @param bool $value
     * @return SpecData[]
     */
    public function getByUrnCat(UrnList $key, bool $value = false): array
    {
        $data = [];
        foreach ($this->specs as $spec) {
            if (isset($spec->urnCat) && $spec->urnCat === $key->value) {
                $data[] = $spec;
            }
        }

        return $data;
    }

    /**
     * @param SpecTypeConst $type
     * @return SpecData[]
     */
    public function getByType(SpecTypeConst $type): array
    {
        $values = [];
        foreach ($this->specs as $spec) {
            if ($spec->type === $type) {
                $values[] = $spec;
            }
        }

        return $values;
    }

}