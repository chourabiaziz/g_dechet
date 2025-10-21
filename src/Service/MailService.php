<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendMail(string $to, string $subject, string $content): void
    {
        $email = (new Email())
            ->from(new Address('aziz@longevityplus.store', 'Longevity Plus'))
            ->to($to)
            ->subject($subject)
            ->html($content);
            

            try {
                $this->mailer->send($email);
            } catch (\Exception $e) {
                // Log l’erreur ou affiche un message
            }
                }
}
