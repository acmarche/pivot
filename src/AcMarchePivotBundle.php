<?php

namespace AcMarche\Pivot;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcMarchePivotBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
