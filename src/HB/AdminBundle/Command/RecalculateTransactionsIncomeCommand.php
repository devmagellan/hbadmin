<?php

namespace HB\AdminBundle\Command;

use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class RecalculateTransactionsIncomeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("transactions:recalculate:income");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContainer()->get("doctrine.orm.entity_manager");

        $transactions = $manager->getRepository(TeachableTransaction::class)->findAll();

        /** @var TeachableTransaction $transaction */
        foreach ($transactions as $transaction) {
            $transaction->updateIncome();
            $manager->persist($transaction);
        }

        $manager->flush();
    }

}