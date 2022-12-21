<?php

namespace AcMarche\Pivot\Entity;

use AcMarche\Pivot\Repository\ElevationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ElevationRepository::class)]
#[ORM\UniqueConstraint(columns: ['latitude', 'longitude'])]
#[ORM\Table(name: 'pivot_elevation')]
class Elevation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;
    #[ORM\Column(type: 'string', length: 180)]
    public string $latitude;
    #[ORM\Column(type: 'string', length: 180)]
    public string $longitude;
    #[ORM\Column(type: 'string', length: 180)]
    public string $elevation;

    public function __construct(
        string $latitude,
        string $longitude,
        string $elevation,
    ) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->elevation = $elevation;
    }

    public function __toString(): string
    {
        return $this->latitude.','.$this->longitude;
    }
}