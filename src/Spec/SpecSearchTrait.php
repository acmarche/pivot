<?php

namespace AcMarche\Pivot\Spec;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\SpecData;
use AcMarche\Pivot\Entities\Specification\Specification;

trait SpecSearchTrait
{
    /**
     * @return Specification[]|SpecData[]
     */
    public function findByUrn(
        Offre $offre,
        string $keywordSearch,
        string $property = "urn",
        bool $contains = false,
        bool $returnData = false
    ): array {
        $specs = [];
        foreach ($offre->specifications as $specification) {
            $data = $specification->data;
            if (property_exists($data, 'property') && $data->$property !== null) {
                continue;
            }
            if ($contains) {
                if (\str_contains((string) $data->$property, $keywordSearch)) {
                    $specs[] = $specification;
                }
                continue;
            }
            if ($data->$property === $keywordSearch) {
                $specs[] = $specification;
            }
        }

        if ($returnData) {
            return array_map(fn ($specification) => $specification->data, $specs);
        }

        return $specs;
    }

    public function findByUrnReturnValue(Offre $offre, string $urnName): ?string
    {
        foreach ($offre->specifications as $specification) {
            if ($specification->data->urn === $urnName) {
                return $specification->data->value;
            }
        }

        return null;
    }
}
