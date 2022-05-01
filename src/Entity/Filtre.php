<?php

namespace AcMarche\Pivot\Entity;

use AcMarche\Pivot\Repository\FiltreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FiltreRepository::class)]
#[ORM\Table(name: 'pivot_filtre')]
class Filtre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;
    #[ORM\Column(type: 'string', length: 180)]
    public string $nom;
    #[ORM\Column(type: 'integer')]
    public int $reference;
    #[ORM\Column(type: 'integer')]
    public int $parent;

    public array $children = [];

    public function __construct(int $reference, string $nom, int $parent)
    {
        $this->reference = $reference;
        $this->nom = $nom;
        $this->parent = $parent;
    }
}