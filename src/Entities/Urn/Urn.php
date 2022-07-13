<?php

namespace AcMarche\Pivot\Entities\Urn;

//https://organismes.tourismewallonie.be/doc-pivot-gest/liste-des-types-durn/

class Urn
{
    public int $id;
    public string $urn;
    public string $code;
    public int $typeId;
    public bool $deprecated = false;
    public string $type;
    public string $label;
    public bool $root = false;

    public function __construct(
        string $urn,
        string $code,
        int $order,
        bool $deprecated,
        string $type,
        string $libele,
        bool $root
    ) {
        $this->urn = $urn;
        $this->code = $code;
        $this->typeId = $order;
        $this->id = $order;
        $this->deprecated = $deprecated;
        $this->type = $type;
        $this->label = $libele;
        $this->root = $root;
    }
}