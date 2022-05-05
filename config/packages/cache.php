<?php
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework) {
    $framework->cache()
        ->app('cache.adapter.filesystem')
        ->system('cache.adapter.system')
    ;
};