<?php

namespace AcMarche\Pivot\Entity;

use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Entities\LabelTrait;
use AcMarche\Pivot\Repository\FiltreRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

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
    #[ORM\Column(type: 'string', nullable:true, length: 50)]
    public ?string $code = null;
    #[ORM\Column(type: 'boolean')]
    public bool $root = false;
    #[ORM\Column(type: 'string', length: 250, nullable: true)]
    public ?string $urn;
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
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $name_de;

    /**
     * @var Filtre[] $children
     */
    public array $children = [];

    public function __construct(
        string $nom,
        int $reference,
        ?string $urn,
        ?Filtre $parent,
        ?string $name_nl = null,
        ?string $name_en = null,
        ?string $name_de = null
    ) {
        $this->reference = $reference;
        $this->nom = $nom;
        $this->urn = $urn;
        $this->parent = $parent;
        $this->name_nl = $name_nl;
        $this->name_en = $name_en;
        $this->name_de = $name_de;
    }

    public function __toString(): string
    {
        return $this->nom;
    }

    public function labelByLanguage(string $language = Label::FR): string
    {
        $property = 'name_'.$language;
        if (isset($this->$property) && $this->$property != null) {
            return $this->$property;
        } else {
            return $this->nom;
        }
    }
}