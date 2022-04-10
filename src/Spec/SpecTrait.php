<?php

namespace AcMarche\Pivot\Spec;

use AcMarche\Pivot\Entities\Pivot\SpecData;

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
            if ($spec->urn === $key) {
                if ($value) {
                    return $spec->value;
                }

                return $spec;
            }
        }

        return null;
    }

    /**
     * @param string $key
     * @return SpecData[]
     */
    public function getByUrns(UrnList $key, bool $like = false): array
    {
        $specs = [];
        foreach ($this->specs as $spec) {
            if ($like) {
                if (str_contains($key, $spec->urn)) {
                    $specs[] = $spec;
                }
                continue;
            }
            if ($spec->urn === $key) {
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
            if (isset($spec->urnCat) && $spec->urnCat === $key) {
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