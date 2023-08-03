<?php

namespace AcMarche\Pivot\Utils;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class Mailer
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    public function sendError(string $subject, string $message): void
    {
        if (($message = $this->createMessage($subject, $message)) instanceof \Symfony\Component\Mime\Email) {
            try {
                $this->mailer->send($message);
            } catch (TransportExceptionInterface) {
                //dump($this->mailer,$e->getMessage());
            }
        }
    }

    private function createMessage(string $subject, string $message): ?Email
    {
        $to = $_ENV['WEBMASTER_EMAIL'] ?? null;
        if ($to) {
            return (new Email())
                ->from($to)
                ->to($to)
                ->subject($subject)
                ->text($message);
        }

        return null;
    }
}
