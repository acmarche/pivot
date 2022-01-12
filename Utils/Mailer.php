<?php


namespace AcMarche\Pivot\Utils;

class Mailer
{
    public static function sendError(string $subject, string $message): void
    {
        Env::loadEnv();
        $to = $_ENV['WEBMASTER_EMAIL'];
        wp_mail($to, $subject, $message);//Todo replace sf mailer
    }
}
