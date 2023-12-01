<?php

namespace AcMarche\Pivot\Entity;

use AcMarche\Pivot\Repository\TypeFicheRepository;
use Doctrine\ORM\Mapping as ORM;
use Stringable;

/**
 * https://organismes.tourismewallonie.be/doc-pivot-gest/documentation-technique/types-de-fiche-pivot/
 */
#[ORM\Entity(repositoryClass: TypeFicheRepository::class)]
#[ORM\Table(name: 'pivot_type_fiche')]
class TypeFiche implements Stringable
{
    use IdTrait;

    #[ORM\Column(nullable: false)]
    public int $countOffres = 0;

    #[ORM\Column(length: 180, nullable: false)]
    public readonly string $name;
    #[ORM\Column(length: 180, nullable: false)]
    public readonly string $code;

    public function __construct(string $name, string $code)
    {
        $this->name = $name;
        $this->code = $code;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
