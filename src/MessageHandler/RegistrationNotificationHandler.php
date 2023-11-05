<?php
// src/MessageHandler/RegistrationNotificationHandler.php

namespace App\MessageHandler;

use App\Message\RegistrationNotification;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class RegistrationNotificationHandler implements \Symfony\Component\Messenger\Handler\MessageHandlerInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(RegistrationNotification $message)
    {
        $user = $message->getUser();

        $email = (new Email())
            ->from('mydriveipssi@gmail.com') // Adresse d'expéditeur
            ->to($user->getEmail()) // Adresse de destination
            ->subject('Inscription réussie')
            ->text('Bienvenue sur notre site!'); // Corps du message

        $this->mailer->send($email);
    }
}
