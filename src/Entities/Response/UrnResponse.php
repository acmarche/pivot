<?php

namespace AcMarche\Pivot\Entities\Response;

use AcMarche\Pivot\Entities\Urn\UrnDefinition;

/**
 * https://pivotweb.tourismewallonie.be/PivotWeb-3.1/thesaurus/urn;fmt=json
 */
class UrnResponse
{
    /**
     * @var UrnDefinition[] $spec
     */
    public array $spec;
}
