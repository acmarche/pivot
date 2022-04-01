<?php

namespace AcMarche\Pivot\Entities\Pivot;

class Organisme
{
    public int $idMdt;
    public string $label;

    public function setIdMdt($idMdt): void
    {
        $this->idMdt = $idMdt;
    }

}