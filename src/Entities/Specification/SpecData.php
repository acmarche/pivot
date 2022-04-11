<?php

namespace AcMarche\Pivot\Entities\Specification;

/**
 * Noeud définissant une spécificité de la base de données PIVOT. Cette spec peut être :
 * • un type d’offres
 * • une catégorie ou sous-catégorie de champ
 * • un champ
 * • une valeur de champ
 * • un type de relation
 * • un index symbolique
 * • un objet
 * • un type de champ
 */
class SpecData
{
    public string $urn;
    public string $urnCat;
    public string $urnSubCat;
    public int $order;
    public string $type;
    public ?string $value = null;
    /**
     * Type == Object
     * @var SpecData[]|array
     */
    public array $spec;
}