<?php

namespace AcMarche\Pivot\Entity;

use AcMarche\Pivot\Entities\Label;
use AcMarche\Pivot\Entities\LabelTrait;
use AcMarche\Pivot\Entities\Urn\UrnDefinition;
use AcMarche\Pivot\Repository\UrnDefinitionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrnDefinitionRepository::class)]
#[ORM\Table(name: 'pivot_urn_definition')]
class UrnDefinitionEntity
{
    use LabelTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;
    #[ORM\Column(type: 'string', length: 180, nullable: false, unique: true)]
    public ?string $urn = null;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $code = null;
    #[ORM\Column(type: 'boolean', length: 180, nullable: true)]
    public bool $deprecated = false;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $type = null;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $dateModification = null;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $dateCreation = null;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $xpathPivotWebWs = null;
    #[ORM\Column(type: 'boolean', length: 180, nullable: true)]
    public ?bool $dynamic = null;
    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $visibilite = null;
    #[ORM\Column(type: 'integer', length: 180, nullable: true)]
    public ?int $userGlobalId = null;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $userGlobalName = null;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $userLocalLogin = null;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $userLocalName = null;
    #[ORM\Column(type: 'string', length: 180, nullable: true)]
    public ?string $userLocalSurname = null;
    #[ORM\Column(type: 'boolean', length: 180, nullable: true)]
    public bool|null $collapsed = null;

    /**
     * @var Label[] $label
     */
    #[ORM\Column(type: 'json', length: 180, nullable: true)]
    public array $label = [];
    /**
     * @var array|Label
     */
    #[ORM\Column(type: 'json', length: 180)]
    public array $abstract = [];

    public static function fromUrnDefinition(UrnDefinition $urnDefinition): self
    {
        $urnEntity = new self();
        $urnEntity->urn = $urnDefinition->urn;
        $urnEntity->code = $urnDefinition->code;
        $urnEntity->deprecated = $urnDefinition->deprecated;
        $urnEntity->type = $urnDefinition->type;
        $urnEntity->dateModification = $urnDefinition->dateModification;
        $urnEntity->dateCreation = $urnDefinition->dateCreation;
        $urnEntity->xpathPivotWebWs = $urnDefinition->xpathPivotWebWs;
        $urnEntity->dynamic = $urnDefinition->dynamic;
        $urnEntity->visibilite = $urnDefinition->visibilite;
        $urnEntity->userGlobalId = $urnDefinition->userGlobalId;
        $urnEntity->userGlobalName = $urnDefinition->userGlobalName;
        $urnEntity->userLocalLogin = $urnDefinition->userLocalLogin;
        $urnEntity->userLocalName = $urnDefinition->userLocalName;
        $urnEntity->userLocalSurname = $urnDefinition->userLocalSurname;
        $urnEntity->collapsed = $urnDefinition->collapsed;
        $urnEntity->label = $urnDefinition->label;
        $urnEntity->abstract = $urnDefinition->abstract;

        return $urnEntity;
    }
}