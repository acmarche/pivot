<?php

declare(strict_types=1);

namespace AcMarche\PivotAi\Entity\Traits;

use AcMarche\PivotAi\Entity\Pivot\Specification;

trait RestaurantTrait
{
    /** @var array<string, Specification> */
    public array $culinarySpecialties = [];

    public ?int $restaurantMichelinStars = null;
    public ?string $restaurantMichelinTitle = null;
    public bool $restaurantIsBibGourmand = false;
    public ?int $restaurantGaultMillauToques = null;
    public ?string $restaurantGaultMillauTitle = null;
}
