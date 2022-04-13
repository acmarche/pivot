<?php

namespace AcMarche\Pivot\Entities\Event;

use AcMarche\Pivot\Entities\Offre\Offre;
use AcMarche\Pivot\Entities\Specification\SpecData;
use DateTimeInterface;

class Event extends Offre
{
    public DateTimeInterface $dateBegin;
    public DateTimeInterface $dateEnd;

    public bool $active;
    public string $email;
    public string $tel;

    public string|SpecData|null $description;
    public string|SpecData|null $tarif;

    public string $image;
    /**
     * @var DateBeginEnd[]
     */
    public array $dates = [];
}