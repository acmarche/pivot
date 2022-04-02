<?php

namespace AcMarche\Pivot\Spec;

use AcMarche\Pivot\Entities\Pivot\Spec;

trait SpecTrait
{
    /**
     * @param Spec[] $specs
     */
    public array $specs = [];

    /**
     * @param string $key
     * @param bool $value
     * @return Spec|string|null
     */
    public function getByUrn(string $key, bool $value = false): Spec|string|null
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
     * @param string $type
     * @return array|Spec[]
     */
    public function getByType(string $type): array
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