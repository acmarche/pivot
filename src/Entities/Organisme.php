<?php

namespace AcMarche\Pivot\Entities;

class Organisme
{
    public int $idMdt;
    public string $label;

    public function setIdMdt($idMdt): void
    {
        $this->idMdt = $idMdt;
    }
}
