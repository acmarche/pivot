<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Traits;

trait AccommodationTrait
{
    public ?int $classificationValue = null;
    public ?string $classificationTitle = null;
    public bool $isSuperior = false;
    public ?int $michelinStars = null;
    public ?string $michelinTitle = null;
    public bool $isBibGourmand = false;
    public ?int $gaultMillauToques = null;
    public ?string $gaultMillauTitle = null;
}
