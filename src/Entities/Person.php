<?php

namespace AcMarche\Pivot\Entities;

class Person
{
    public string $nom;
    public string $prenom;
    /**
     * @var Adresse2[] $adresse
     */
    public array $adresse;
}