<?php

namespace AcMarche\Pivot\Entities\Pivot;

use AcMarche\Pivot\Event\DateBeginEnd;
use DateTimeInterface;

class Event extends Offer
{
    public ?string $homepage;
    public DateTimeInterface $dateBegin;
    public DateTimeInterface $dateEnd;
    public bool $active;
    public string $email;
    public string $tel;
    public string|SpecData|null $description;
    public string|SpecData|null $tarif;
    public string $image;
    public array $emails = [];
    public array $tels = [];
    /**
     * @var SpecInfo[]
     */
    public array $specsDetailed;
    public array $categories = [];
    public array $images = [];
    /**
     * @var DateBeginEnd[]
     */
    public array $dates = [];
}
