<?php

namespace HB\AdminBundle\Command;

use HB\AdminBundle\Entity\Teachable\Webhook\CommentCreated;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class RefreshTeachableCommentsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("teachable:comments:refresh");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mapper = $this->getContainer()->get('teachable.webhooks_mapper');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $webhooks = $em->getRepository(CommentCreated::class)->findBy([], ['id' => 'ASC']);

        foreach ($webhooks as $webhook) {
            $mapper->updateCommentCreated($webhook);

            $output->writeln('updated '.$webhook->getId());
        }

    }

}