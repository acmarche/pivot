<?php

namespace AcMarche\Pivot\Utils;

use Exception;
use Symfony\Component\Dotenv\Dotenv;

class Env
{
    public static function loadEnv(): void
    {
        $dotenv = new Dotenv();
        try {
            $dotenv->load(getcwd().'/.env');
        } catch (Exception $exception) {
            //echo "error load env: " . $exception->getMessage();
        }
    }
}