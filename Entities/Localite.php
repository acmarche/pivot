<?php


namespace AcMarche\Pivot\Entities;


class Localite
{
    public ?string $id = null;

    public ?string $l_nom = null;

    public ?string $postal = null;

    public ?string $com_id = null;

    public ?string $c_nom = null;

    public ?string $reg_id = null;

    public ?string $x = null;

    public ?string $y = null;

    public function localite(): ?string
    {
        return $this->l_nom;
    }

    public function latitude(): ?string
    {
        return $this->y;
    }

    public function longitude(): ?string
    {
        return $this->x;
    }
}
