<?php

namespace AcMarche\Pivot\Entities;

class Geocode
{
    public ?string $x = null;

    public ?string $y = null;

    public ?string $trace = null;

    public function latitude(): ?string
    {
        return $this->y;
    }

    public function longitude(): ?string
    {
        return $this->x;
    }
}
