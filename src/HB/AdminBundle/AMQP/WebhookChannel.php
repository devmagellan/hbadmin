<?php

declare(strict_types = 1);


namespace HB\AdminBundle\AMQP;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use HB\AdminBundle\Entity\Teachable\TeachableTransactionRefunded;
use HB\AdminBundle\Entity\Teachable\Webhook\CommentCreated;
use HB\AdminBundle\Entity\Teachable\Webhook\EnrollmentCreated;
use HB\AdminBundle\Entity\Teachable\Webhook\LectureProgressCreated;
use HB\AdminBundle\Entity\Teachable\Webhook\ResponseCreated;
use HB\AdminBundle\Entity\Teachable\Webhook\TransactionCreated;
use HB\AdminBundle\Entity\Teachable\Webhook\TransactionRefunded;
use HB\AdminBundle\Exception\ConsumerErrorException;
use HB\AdminBundle\Helper\EntityManagerConnectionHelper;
use HB\AdminBundle\Service\TeachableCourseMapper;
use HB\AdminBundle\Service\TeachableWebhooksMapper;

class WebhookChannel
{
    private const REFERER = 'amqp';

    private const RESPONSE_CREATED = 'Response.created';
    private const TRANSACTION_REFUNDED = 'Transaction.refunded';
    private const COMMENT_CREATED = 'Comment.created';
    private const ENROLLMENT_COMPLETED = 'Enrollment.created';
    private const LECTURE_PROGRESS_CREATED = 'LectureProgress.created';
    private const TRANSACTION_CREATED = 'Transaction.created';

    /**
     * @var AMQPManager
     */
    private $amqpManager;

    /**
     * @var string
     */
    private $queueName;

    /**
     * @var string
     */
    private $exchange;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TeachableWebhooksMapper
     */
    private $teachableWebhooksMapper;

    /**
     * @var TeachableCourseMapper
     */
    private $teachableCourseMapper;

    /**
     * WebhookChannel constructor.
     *
     * @param AMQPManager             $amqpManager
     * @param string                  $queueName
     * @param string                  $exchange
     * @param EntityManagerInterface  $entityManager
     * @param TeachableWebhooksMapper $teachableWebhooksMapper
     * @param TeachableCourseMapper   $teachableCourseMapper
     */
    public function __construct(AMQPManager $amqpManager, string $queueName, string $exchange, EntityManagerInterface $entityManager, TeachableWebhooksMapper $teachableWebhooksMapper, TeachableCourseMapper $teachableCourseMapper)
    {
        $this->amqpManager = $amqpManager;
        $this->queueName = $queueName;
        $this->exchange = $exchange;
        $this->entityManager = $entityManager;
        $this->teachableWebhooksMapper = $teachableWebhooksMapper;
        $this->teachableCourseMapper = $teachableCourseMapper;
    }

    /**
     * Consume Teachable webhook channel
     *
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPEnvelopeException
     * @throws \AMQPQueueException
     */
    public function consume()
    {
        $channel = new \AMQPChannel($this->amqpManager->getConnection());

        $queue = new \AMQPQueue($channel);
        $queue->setName($this->queueName);

        $queue->bind($this->exchange, $this->queueName);
        $queue->setFlags(AMQP_DURABLE);

        $queue->declareQueue();
        $queue->consume(
            $this->messageProcessor()
        );
    }

    /**
     * Process amqp queue messages
     *
     * @return callable
     */
    private function messageProcessor(): callable
    {
        return function (\AMQPEnvelope $envelope, \AMQPQueue $queue) {

            EntityManagerConnectionHelper::lazyConnect($this->entityManager);
            $deliveryTag = $envelope->getDeliveryTag();

            $json = $envelope->getBody();
            $data = json_decode($json, true);

            if (is_array($data)) {
                if (isset($data['type'])) {
                    $type = $data['type'];

                    try {
                        switch ($type) {
                            case self::COMMENT_CREATED:
                                $this->processCommentCreated($json);
                                break;
                            case self::ENROLLMENT_COMPLETED:
                                $this->processEnrollmentCompleted($json);
                                break;
                            case self::LECTURE_PROGRESS_CREATED:
                                $this->processLectureProgressCreated($json);
                                break;
                            case self::RESPONSE_CREATED:
                                $this->processResponseCreated($json);
                                break;
                            case self::TRANSACTION_REFUNDED:
                                $this->processTransactionRefunded($json);
                                break;
                            case self::TRANSACTION_CREATED:
                                $this->processTransactionCreated($json);
                                break;
                            default:
                                // do nothing
                        }
                    } catch (\PDOException $exception) {
                        if (strpos('MySQL server has gone away', $exception->getMessage()) >= 0) {
                            $queue->nack($deliveryTag, AMQP_REQUEUE);
                            throw new ConsumerErrorException($exception->getMessage());
                        } else {
                            throw $exception;
                        }
                    } catch (\ErrorException $exception) {
                        if (strpos('Error while sending QUERY packet', $exception->getMessage()) >= 0) {
                            $queue->nack($deliveryTag, AMQP_REQUEUE);
                            throw new ConsumerErrorException($exception->getMessage());
                        } else {
                            throw $exception;
                        }
                    }

                }
            }

            $queue->ack($deliveryTag, AMQP_NOPARAM);
//          $queue->nack($deliveryTag, AMQP_REQUEUE);

            /** No interested webhook */
            return true;
        };
    }

    /**
     * @param string $json
     */
    private function processResponseCreated(string $json)
    {
        $webhook = new ResponseCreated($json, self::REFERER);

        $this->entityManager->persist($webhook);
        $this->entityManager->flush();

        $this->teachableWebhooksMapper->updateResponseCreated($webhook);
    }

    private function processTransactionRefunded(string $json)
    {
        $webhook = new TransactionRefunded($json, self::REFERER);
        $this->entityManager->persist($webhook);
        $this->entityManager->flush();

        try {
            $transaction = $this->createRefundedTransaction($webhook);

            $this->teachableCourseMapper->refundTransactions($transaction);
        } catch (\InvalidArgumentException $exception) {
            //do nothing
        }
    }

    private function processCommentCreated(string $json)
    {
        $webhook = new CommentCreated($json, self::REFERER);
        $this->entityManager->persist($webhook);
        $this->entityManager->flush();

        $this->teachableWebhooksMapper->updateCommentCreated($webhook);
    }

    private function processEnrollmentCompleted(string $json)
    {
        $webhook = new EnrollmentCreated($json, self::REFERER);
        $this->entityManager->persist($webhook);
        $this->entityManager->flush();

        $this->teachableWebhooksMapper->updateEnrollment($webhook);
    }

    private function processLectureProgressCreated(string $json)
    {
        $webhook = new LectureProgressCreated($json, self::REFERER);
        $this->entityManager->persist($webhook);
        $this->entityManager->flush();

        $this->teachableWebhooksMapper->updateLectureProgress($webhook);
    }

    private function processTransactionCreated(string $json)
    {
        $webhook = new TransactionCreated($json, self::REFERER);
        $this->entityManager->persist($webhook);
        $this->entityManager->flush();

        try {
            $transaction = $this->createTransaction($webhook);

            $this->teachableCourseMapper->mapTransactionOnCourse($transaction);
            $student = $this->teachableWebhooksMapper->updateOrCreateStudent($transaction);
            $this->teachableCourseMapper->mapStudentOnCourse($student);
        } catch (\InvalidArgumentException $exception) {
            //do nothing
        }
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