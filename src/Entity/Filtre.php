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
    #[ORM\ManyToOne(targetEntity: Filtre::class)]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    public ?Filtre $parent = null;
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

    public function __construct(int $reference, string $nom, ?Filtre $parent)
    {
        $this->reference = $reference;
        $this->nom = $nom;
        $this->parent = $parent;
    }

    public function __toString(): string
    {
        return $this->nom;
    }
}