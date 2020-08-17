<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Api\Hook\Teachable;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\Teachable\TeachableTransactionRefunded;
use HB\AdminBundle\Entity\Teachable\Webhook\TransactionRefunded;
use HB\AdminBundle\Service\TeachableCourseMapper;
use Symfony\Component\HttpFoundation\Request;

class TransactionRefundedController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TeachableCourseMapper
     */
    private $teachableCourseMapper;

    /**
     * TransactionCreatedController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TeachableCourseMapper  $teachableCourseMapper
     */
    public function __construct(EntityManagerInterface $entityManager, TeachableCourseMapper $teachableCourseMapper)
    {
        $this->entityManager = $entityManager;
        $this->teachableCourseMapper = $teachableCourseMapper;
    }

    /**
     * @param Request $request
     */
    public function handleAction(Request $request)
    {
        $content = $request->getContent();
        $referer = $request->headers->get('referer');

        $webhook = new TransactionRefunded($content, $referer);
        $this->entityManager->persist($webhook);
        $this->entityManager->flush();

        try {
            $transaction = $this->createRefundedTransaction($webhook);

            $this->teachableCourseMapper->refundTransactions($transaction);
        } catch (\InvalidArgumentException $exception) {
            //do nothing
        }


        return Json::ok();
    }

    /**
     * @param TransactionRefunded $transactionRefunded
     *
     * @return TeachableTransactionRefunded
     *
     * @throws \Exception
     */
    private function createRefundedTransaction(TransactionRefunded $transactionRefunded): TeachableTransactionRefunded
    {
        $transaction = TeachableTransactionRefunded::fromWebhook($transactionRefunded->getBodyData());

        if (is_null($this->entityManager->getRepository(TeachableTransactionRefunded::class)->findOneBy(['transactionId' => $transaction->getTransactionId()]))) {
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
        } else {
            throw new \InvalidArgumentException('Refunded Transaction already created');
        }

        return $transaction;
    }
}
