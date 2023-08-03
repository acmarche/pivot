<?php

namespace AcMarche\Pivot\Entities\Urn;

//https://organismes.tourismewallonie.be/doc-pivot-gest/liste-des-types-durn/

class Urn
{
    public int $id;

    public function __construct(
        public string $urn,
        public string $code,
        public int $typeId,
        public bool $deprecated,
        public string $type,
        public string $label,
        public bool $root
    ) {
        $this->id = $typeId;
    }
}