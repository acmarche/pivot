<?php

namespace AcMarche\Pivot\Entity;

use Stringable;
use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Repository\TypeOffreRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeOffreRepository::class)]
#[ORM\Table(name: 'pivot_type_offre')]
class TypeOffre implements Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;
    #[ORM\Column(type: 'integer', nullable: false)]
    public int $countOffres = 0;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $name_fr = null;

    /**
     * @var TypeOffre[] $children
     */
    public array $children = [];
    public bool $withChildren = false;
    public ?string $url = null;

    public function __construct(#[ORM\Column(type: 'string', length: 180)]
        public string $name, #[ORM\Column(type: 'integer', nullable: true)]
        public int $typeId, #[ORM\Column(type: 'integer', nullable: false)]
        public int $display_order, #[ORM\Column(type: 'string', unique: false, nullable: true)]
        public ?string $code, #[ORM\Column(type: 'string', length: 250, nullable: false)]
        public string $urn, #[ORM\Column(type: 'string', length: 50, nullable: false)]
        public string $type, #[ORM\ManyToOne(targetEntity: TypeOffre::class)]
        #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
        public ?TypeOffre $parent, #[ORM\Column(type: 'string', length: 180, nullable: true)]
        public ?string $name_nl = null, #[ORM\Column(type: 'string', length: 180, nullable: true)]
        public ?string $name_en = null, #[ORM\Column(type: 'string', length: 180, nullable: true)]
        public ?string $name_de = null)
    {
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function labelByLanguage(string $language = Label::FR): string
    {
        $property = 'name_' . $language;
        if (property_exists($this, 'property') && $this->$property !== null && $this->$property != null) {
            return $this->$property;
        } else {
            return $this->name;
        }
    }
}
