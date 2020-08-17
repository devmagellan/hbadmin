<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Component\Mailer;

use HB\AdminBundle\Component\Mailer\Model\Receiver;
use HB\AdminBundle\Component\Mailer\Model\Sender;

class Mailer implements MailerInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var \Swift_Mailer
     */
    private $swiftMailer;

    /**
     * Mailer constructor.
     *
     * @param \Twig_Environment $twig
     * @param \Swift_Mailer     $swiftMailer
     */
    public function __construct(\Twig_Environment $twig, \Swift_Mailer $swiftMailer)
    {
        $this->twig = $twig;
        $this->swiftMailer = $swiftMailer;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Sender $sender, Receiver $receiver, string $template, string $subject, array $context = []): void
    {
        $template = $this->twig->render($template, $context);

        $message = (new \Swift_Message($subject))
            ->setFrom([
                $sender->getEmail()->getValue() => $sender->getName()
            ])
            ->setTo($receiver->getEmail()->getValue())
            ->setBody($template, 'text/html');

        $this->swiftMailer->send($message);
    }

}