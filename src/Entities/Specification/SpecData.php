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
    const KEY_CAT = 'urnCat';
    const KEY_SUB_CAT = 'urnSubCat';
    const KEY_TYPE = 'type';

    public string $urn;
    public ?string $urnCat = null;
    public ?string $urnSubCat = null;
    public int $order = 0;
    public ?string $type= null;
    public ?string $value = null;
    /**
     * Type == Object ex: date event: "urn": "urn:obj:date"
     * @var SpecData[]|array
     */
    public array $spec = [];
}