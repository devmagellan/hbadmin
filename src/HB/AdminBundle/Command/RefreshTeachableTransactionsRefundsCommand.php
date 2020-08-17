<?php

namespace HB\AdminBundle\Command;

use HB\AdminBundle\Entity\Teachable\TeachableTransactionRefunded;
use HB\AdminBundle\Entity\Teachable\Webhook\TransactionRefunded;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class RefreshTeachableTransactionsRefundsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("teachable:transactions_refund:refresh");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $refundedTransactionsWebhookRepository = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository(TransactionRefunded::class);
        $refundedTransactionsRepository = $this->getContainer()->get('doctrine.orm.entity_manager')->getRepository(TeachableTransactionRefunded::class);

        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $ids = [];

        $refundedTransactions = $refundedTransactionsWebhookRepository->findAll();

        foreach ($refundedTransactions as $refundedTransaction) {
            $body = $refundedTransaction->getBodyData();
            $hookId = $body['hook_event_id'];

            if (!in_array($hookId, $ids)) {

                /** @var TeachableTransactionRefunded $existed */
                $existed = $refundedTransactionsRepository->findOneBy(['hookEventId' => $hookId]);

                if ($existed) {
                    $refunded = TeachableTransactionRefunded::fromWebhook($body, $existed);
                } else {
                    $refunded = TeachableTransactionRefunded::fromWebhook($body);
                }


                $ids[] = $refunded->getHookEventId();

                $em->persist($refunded);
            }
        }

        $em->flush();

        $this->getContainer()->get('teachable.course_mapper')->refundTransactions();
    }
}