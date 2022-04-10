<?php

namespace AcMarche\Pivot\Entities\Pivot;

use DateTimeInterface;

class Hotel extends Offer
{
    public ?string $homepage= null;
    public DateTimeInterface $dateBegin;
    public DateTimeInterface $dateEnd;
    public bool $active;
    public string $email;
    public string $tel;
    public string|SpecData|null $description = null;
    public string|SpecData|null $tarif = null;
    public string $image;
    public array $emails = [];
    public array $tels = [];
    /**
     * @var SpecInfo[]
     */
    public array $specsDetailed;
    public array $categories = [];
    public array $images= [];
}
