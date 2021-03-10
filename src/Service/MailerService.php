<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Entity\Contact;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmailAfterContactBeeznessly(Contact $contact): void
    {
        $email = (new TemplatedEmail())
            ->from('beeznessly@gmail.com')
            //TODO: Change ->to('beeznessly')
            ->to('beeznessly@gmail.com')
            ->subject('Nouveau message de Beeznessly')
            ->html(
                '<p>' . $contact->getFirstname() . ' ' .
                $contact->getLastname() . '</p> vous a envoyé un message:</p>' .
                '<p>Pour lui répondre ' .
                '<p>Email : ' . $contact->getEmail() . ' ' . '<p>Téléphone : ' . $contact->getPhoneNumber() .
                '<p>Sujet : ' . $contact->getSubject() . '</p>' .
                '<p>Nom de l\'entreprise : ' . $contact->getCompanyName() . '</p>' .
                '<p>Message ' . $contact->getMessage() . '</p>'
            );

        $this->mailer->send($email);
    }

    public function sendEmailAfterContactExpert(Contact $contact): void
    {
        $email = (new TemplatedEmail())
            ->from('beeznessly@gmail.com')
            ->to($contact->getUser()->getEmail())
            ->subject('Nouveau message sur la plateforme Beeznessly')
            ->html(
                '<p>' . $contact->getFirstname() . ' ' . $contact->getLastname() . '</h4> vous a envoyé un message:</p>' .
                '<p>' . $contact->getEmail() . '</h4> pour lui répondre</p>' .
                '<p>Sujet: ' . $contact->getSubject() . '</p>' .
                '<p>' . $contact->getMessage() . '</p>'
            );

        $this->mailer->send($email);
    }
}
