<?php

namespace HB\AdminBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class RefreshTeachableResponsesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("teachable:response:refresh");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mapper = $this->getContainer()->get('teachable.webhooks_mapper');

        $mapper->updateResponseCreated();
    }

}