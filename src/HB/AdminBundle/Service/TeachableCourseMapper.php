<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Teachable\TeachableCourseStudent;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use HB\AdminBundle\Entity\Teachable\TeachableTransactionRefunded;
use HB\AdminBundle\Entity\Teachable\Webhook\TransactionCreated;

class TeachableCourseMapper
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TeachableWebhooksMapper
     */
    private $webhooksMapper;

    /**
     * TeachableCourseMapper constructor.
     *
     * @param EntityManagerInterface  $entityManager
     * @param TeachableWebhooksMapper $webhooksMapper
     */
    public function __construct(EntityManagerInterface $entityManager, TeachableWebhooksMapper $webhooksMapper)
    {
        $this->entityManager = $entityManager;
        $this->webhooksMapper = $webhooksMapper;
    }


    /**
     * @param Course|null $course
     */
    public function updateTeachableRelations(Course $course = null)
    {
        if ($course) {
            $this->update($course);
        } else {
            $courses = $this->getCoursesForUpdate();

            foreach ($courses as $course) {
                $this->update($course);
            }
        }
    }

    /**
     * @param TeachableTransaction $teachableTransaction
     */
    public function mapTransactionOnCourse(TeachableTransaction $teachableTransaction)
    {
        $course = $this->entityManager->getRepository(Course::class)->findOneBy(['teachableId' => $teachableTransaction->getCourseId()]);

        if ($course) {
            $teachableTransaction->setInternalCourse($course);
            $this->entityManager->persist($teachableTransaction);
            $this->entityManager->flush();
        }
    }

    /**
     * @param TeachableCourseStudent $student
     */
    public function mapStudentOnCourse(TeachableCourseStudent $student)
    {
        $transactions = $this->entityManager->getRepository(TeachableTransaction::class)->findBy([
            'course_id'    => $student->getCourseId(),
            'studentEmail' => $student->getStudentEmail(),
        ]);

        /** @var TeachableTransaction $transaction */
        foreach ($transactions as $transaction) {
            $transaction->setTeachableStudent($student);
            $this->entityManager->persist($transaction);
        }
        $this->entityManager->flush();
    }

    public function refreshTransactionsFromWebhook()
    {
        $webhooks = $this->entityManager->getRepository(TransactionCreated::class)->findAll();
        $transactionRepository = $this->entityManager->getRepository(TeachableTransaction::class);

        /** @var TransactionCreated $webhook */
        foreach ($webhooks as $webhook) {
            $body = $webhook->getBodyData();
            $transactionId = $preparedProperties['transactionId'] = $body['object']['id'];

            /** @var TeachableTransaction $existTransaction */
            $existTransaction = $transactionRepository->findOneBy(['transactionId' => $transactionId]);

            if ($existTransaction) {
                $transaction = TeachableTransaction::fromWebhook($body, $existTransaction, $existTransaction->isRefunded());
            } else {
                $transaction = TeachableTransaction::fromWebhook($body);
            }

            $this->mapTransactionOnCourse($transaction);

            $student = $this->webhooksMapper->updateOrCreateStudent($transaction);
            $this->mapStudentOnCourse($student);

            $this->entityManager->persist($transaction);
        }
        $this->entityManager->flush();

        $this->refundTransactions();

        $this->entityManager->flush();
    }

    /**
     * Apply refund to TeachableTransactions
     *
     * @param TeachableTransactionRefunded|null $transactionRefunded
     */
    public function refundTransactions(TeachableTransactionRefunded $transactionRefunded = null)
    {
        if ($transactionRefunded) {
            $refunded = [$transactionRefunded];
        } else {
            $refunded = $this->entityManager->getRepository(TeachableTransactionRefunded::class)->findAll();
        }

        $transactionRepository = $this->entityManager->getRepository(TeachableTransaction::class);

        /** @var TeachableTransactionRefunded $transaction */
        foreach ($refunded as $transaction) {
            $refundedTransaction = $transactionRepository->findOneBy(['transactionId' => $transaction->getTransactionId()]);

            if ($refundedTransaction) {
                $refundedTransaction->refund($transaction);
                $this->entityManager->persist($refundedTransaction);
                $this->entityManager->flush($refundedTransaction);
            }
        }
    }

    /**
     * @param Course $course
     */
    private function update(Course $course)
    {
        $this->entityManager->createQueryBuilder()
            ->update(TeachableTransaction::class, 't')
            ->set('t.internalCourse', 'NULL')
            ->where('t.internalCourse = :course_id')
            ->setParameter('course_id', $course->getId())->getQuery()->getResult();

        if ($course->getTeachableId()) {
            $this->entityManager->createQueryBuilder()
                ->update(TeachableTransaction::class, 't')
                ->set('t.internalCourse', $course->getId())
                ->where('t.course_id = :teachable_course_id')
                ->setParameter('teachable_course_id', $course->getTeachableId())->getQuery()->getResult();
        }
    }

    /**
     * @return Course[]
     */
    private function getCoursesForUpdate()
    {
        return $this->entityManager->createQueryBuilder()
            ->select('c')
            ->from(Course::class, 'c')
            ->where('c.teachableId IS NOT NULL')
            ->getQuery()->getResult();
    }

}