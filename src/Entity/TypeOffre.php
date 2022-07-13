<?php

namespace AcMarche\Pivot\Entity;

use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeOffreRepository::class)]
#[ORM\Table(name: 'pivot_type_offre')]
class TypeOffre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;
    #[ORM\Column(type: 'string', length: 180)]
    public string $nom;
    #[ORM\Column(type: 'integer', nullable: false)]
    public int $display_order = 0;
    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    public string $type;
    #[ORM\Column(type: 'string', length: 250, nullable: false)]
    public string $urn;
    #[ORM\Column(type: 'string', unique: false, nullable: true)]
    public ?string $code = null;
    #[ORM\Column(type: 'string', nullable: false)]
    public int|string $typeId;
    #[ORM\ManyToOne(targetEntity: TypeOffre::class)]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    public ?TypeOffre $parent = null;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $name_fr;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $name_nl;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $name_en;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $name_de;

    /**
     * @var TypeOffre[] $children
     */
    public array $children = [];

    public function __construct(
        string $nom,
        int|string $typeId,
        int $display_order,
        ?string $code,
        ?string $urn,
        ?string $type,
        ?TypeOffre $parent,
        ?string $name_nl = null,
        ?string $name_en = null,
        ?string $name_de = null
    ) {
        $this->nom = $nom;
        $this->typeId = $typeId;
        $this->display_order = $display_order;
        $this->code = $code;
        $this->urn = $urn;
        $this->type = $type;
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

    public function getIdentifiant(): string|int
    {
        if (!$this->parent) {
            return $this->code;
        } else {
            return $this->urn;
        }
    }
}