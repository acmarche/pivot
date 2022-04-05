<?php

namespace AcMarche\Pivot\Entities\Pivot;

use DateTimeInterface;

class Event extends Offer
{
    public ?string $homepage;
    public DateTimeInterface $dateBegin;
    public DateTimeInterface $dateEnd;
    public bool $active;
    public string $email;
    public string $tel;
    public string|Spec|null $description;
    public string|Spec|null $tarif;
    public string $image;
    public array $emails = [];
    public array $tels = [];
    /**
     * @var Urn[] $urns
     */
    public array $urns = [];
}
