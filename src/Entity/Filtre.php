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
    #[ORM\Column(type: 'integer', unique: false, nullable: false)]
    public int $reference;
    #[ORM\Column(type: 'integer')]
    public int $parent;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $name_fr;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $name_nl;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $name_en;

    /**
     * @var Filtre[] $children
     */
    public array $children = [];

    public function __construct(int $reference, string $nom, int $parent)
    {
        $this->reference = $reference;
        $this->nom = $nom;
        $this->parent = $parent;
    }
}