<?php

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework) {
    $framework->secret("%env(APP_SECRET)%");
    $framework->mailer([
        'dsn' => '%env(MAILER_DSN)%',
    ]);
    $framework->errorController('App\Controller\ErrorController::show');
};