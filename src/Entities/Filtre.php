<?php

namespace AcMarche\Pivot\Entities;

use AcMarche\Pivot\Repository\FiltreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiltreRepository::class)]
class Filtre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;
    #[ORM\Column(type: 'string', length: 100)]
    public string $nom;
    public int $parent;

    public function __construct(int $id, string $nom, int $parent)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->parent = $parent;
    }
}