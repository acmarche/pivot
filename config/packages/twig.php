<?php

use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig) {
    $twig
        ->path('%kernel.project_dir%/src/AcMarche/Pivot/templates', 'AcMarchePivot')
        ->path('%kernel.project_dir%/wp-content/themes/visittail/templates', 'VisitTail');
};
