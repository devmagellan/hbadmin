<?php

namespace HB\AdminBundle\Command;

use Doctrine\ORM\EntityManager;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class RemoveTeachableDuplicateTransactionCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName("teachable:transactions:remove_duplicates");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');

        $transactions = $em->getRepository(TeachableTransaction::class)->findBy([], ['id' => 'ASC']);

        /** @var TeachableTransaction $transaction */
        foreach ($transactions as $transaction) {
            $this->findTransactionNewer($transaction, $em);
        }
    }

    private function findTransactionNewer(TeachableTransaction $transaction, EntityManager $entityManager)
    {
        if ($transaction->getId()) {
            $duplicates = $entityManager->createQueryBuilder()
                ->select('t')
                ->from(TeachableTransaction::class, 't')
                ->where('t.id > :id')
                ->andWhere('t.transactionId = :transaction_id')
                ->setParameters([
                    'id'             => $transaction->getId(),
                    'transaction_id' => $transaction->getTransactionId(),
                ])
                ->getQuery()->getResult();

            foreach ($duplicates as $duplicate) {
                $entityManager->remove($duplicate);
                $entityManager->flush();
            }
        }

    }

}