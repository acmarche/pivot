<?php


namespace AcMarche\Pivot\Entities;


class Geocode
{
    public string $x;

    public string $y;

    public string $trace;

    public function latitude(): string
    {
        return $this->y;
    }

    public function longitude(): string
    {
        return $this->x;
    }
}
