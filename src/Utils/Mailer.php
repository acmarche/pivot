<?php

namespace AcMarche\Pivot\Utils;

class Mailer
{
    public static function sendError(string $subject, string $message): void
    {
        Env::loadEnv();
        $to = $_ENV['WEBMASTER_EMAIL'];
        if (function_exists('wp_mail')) {
            wp_mail($to, $subject, $message);//Todo replace sf mailer
        }
    }
}
