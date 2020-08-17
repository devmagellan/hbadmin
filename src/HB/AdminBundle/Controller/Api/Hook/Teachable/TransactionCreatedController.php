<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Api\Hook\Teachable;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use HB\AdminBundle\Entity\Teachable\Webhook\TransactionCreated;
use HB\AdminBundle\Service\TeachableCourseMapper;
use HB\AdminBundle\Service\TeachableWebhooksMapper;
use Symfony\Component\HttpFoundation\Request;

class TransactionCreatedController
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
     * @var TeachableWebhooksMapper
     */
    private $teachableWebooksMapper;

    /**
     * TransactionCreatedController constructor.
     *
     * @param EntityManagerInterface  $entityManager
     * @param TeachableCourseMapper   $teachableCourseMapper
     * @param TeachableWebhooksMapper $teachableWebooksMapper
     */
    public function __construct(EntityManagerInterface $entityManager, TeachableCourseMapper $teachableCourseMapper, TeachableWebhooksMapper $teachableWebooksMapper)
    {
        $this->entityManager = $entityManager;
        $this->teachableCourseMapper = $teachableCourseMapper;
        $this->teachableWebooksMapper = $teachableWebooksMapper;
    }

    /**
     * @param Request $request
     */
    public function handleAction(Request $request)
    {
        $content = $request->getContent();
        $referer = $request->headers->get('referer');

        $webhook = new TransactionCreated($content, $referer);
        $this->entityManager->persist($webhook);
        $this->entityManager->flush();


        try {
            $transaction = $this->createTransaction($webhook);

            $this->teachableCourseMapper->mapTransactionOnCourse($transaction);
            $student = $this->teachableWebooksMapper->updateOrCreateStudent($transaction);
            $this->teachableCourseMapper->mapStudentOnCourse($student);
        } catch (\InvalidArgumentException $exception) {
          //do nothing
        }

        return Json::ok();
    }

    /**
     * @param TransactionCreated $transactionCreated
     *
     * @return TeachableTransaction
     *
     * @throws \Exception
     */
    private function createTransaction(TransactionCreated $transactionCreated): TeachableTransaction
    {
        $transaction = TeachableTransaction::fromWebhook($transactionCreated->getBodyData());
        $transaction->updateIncome();

        if (is_null($this->entityManager->getRepository(TeachableTransaction::class)->findOneBy(['transactionId' => $transaction->getTransactionId()]))) {
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
        } else {
            throw new \InvalidArgumentException('Transaction already created');
        }

        return $transaction;
    }
}
