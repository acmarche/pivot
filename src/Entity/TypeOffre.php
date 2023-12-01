<?php

namespace AcMarche\Pivot\Entity;

use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

#[ORM\Entity(repositoryClass: TypeOffreRepository::class)]
#[ORM\Table(name: 'pivot_type_offre')]
class TypeOffre implements Stringable
{
    use IdTrait;

    #[ORM\Column(length: 180)]
    public string $name;
    #[ORM\Column(nullable: true)]
    public int $typeId;
    #[ORM\Column(nullable: false)]
    public int $display_order;
    #[ORM\Column(unique: false, nullable: true)]
    public ?string $code;
    #[ORM\Column(length: 250, nullable: false)]
    public string $urn;
    #[ORM\Column(length: 50, nullable: false)]
    public string $type;
    #[ORM\ManyToOne(targetEntity: TypeOffre::class)]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    public ?TypeOffre $parent;
    #[ORM\Column(length: 180, nullable: true)]
    public ?string $name_nl = null;
    #[ORM\Column(length: 180, nullable: true)]
    public ?string $name_en = null;
    #[ORM\Column(length: 180, nullable: true)]
    public ?string $name_de = null;

    #[ORM\Column(nullable: false)]
    public int $countOffres = 0;
    #[ORM\Column(length: 180, nullable: true)]
    public ?string $name_fr = null;

    /**
     * @var TypeOffre[] $children
     */
    public array $children = [];
    public bool $withChildren = false;
    public ?string $url = null;

    public function __construct(
        string $name,
        int $typeId,
        int $display_order,
        ?string $code,
        string $urn,
        string $type,
        ?TypeOffre $parent,
        ?string $name_nl = null,
        ?string $name_en = null,
        ?string $name_de = null
    ) {
        $this->name = $name;
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
        return $this->name;
    }

    public function labelByLanguage(string $language = Label::FR): string
    {
        $property = 'name_'.$language;
        if (property_exists($this, 'property') && $this->$property !== null && $this->$property != null) {
            return $this->$property;
        } else {
            return $this->name;
        }
    }
}
