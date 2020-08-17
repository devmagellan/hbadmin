<?php

namespace HB\AdminBundle\Command\Consumers;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TeachableWebhookConsumerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("consumers:webhook:teachable");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $channel = $this->getContainer()->get('amqp.webhook_channel');
        $channel->consume();
    }
}