<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Component\Mailer;

use HB\AdminBundle\Component\Mailer\Model\Receiver;
use HB\AdminBundle\Component\Mailer\Model\Sender;

interface MailerInterface
{
    /**
     * @param Sender   $sender
     * @param Receiver $receiver
     * @param string   $template
     * @param string   $subject
     * @param array    $context
     */
    public function send(Sender $sender, Receiver $receiver, string $template, string $subject, array $context = []): void;
}