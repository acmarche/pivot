<?php

namespace AcMarche\Pivot\Utils;

class Mailer
{
    public static function sendError(string $subject, string $message): void
    {
        $to = $_ENV['WEBMASTER_EMAIL'] ?? null;
        if ($to) {
            if (function_exists('wp_mail')) {
                wp_mail($to, $subject, $message);//Todo replace sf mailer
            }
        }
    }
}
