<?php


namespace AcMarche\Pivot\Entities;


class Localite
{
    public string $id;

    public string $l_nom;

    public string $postal;

    public string $com_id;

    public string $c_nom;

    public string $reg_id;

    public string $x;

    public string $y;

    public function localite(): string
    {
        return $this->l_nom;
    }

    public function latitude(): string
    {
        return $this->y;
    }

    public function longitude(): string
    {
        return $this->x;
    }
}
