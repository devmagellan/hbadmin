<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Api\Hook\Teachable;


use HB\AdminBundle\AMQP\AMQPManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TeachableWebhookListenController
{
    /**
     * @var AMQPManager
     */
    private $amqpManager;

    /**
     * @var string
     */
    private $amqpQueue;

    /**
     * @var string
     */
    private $amqpExchange;

    /**
     * TeachableWebhookListenController constructor.
     *
     * @param AMQPManager $amqpManager
     * @param string      $amqpQueue
     * @param string      $amqpExchange
     */
    public function __construct(AMQPManager $amqpManager, string $amqpQueue, string $amqpExchange)
    {
        $this->amqpManager = $amqpManager;
        $this->amqpQueue = $amqpQueue;
        $this->amqpExchange = $amqpExchange;
    }

    /**
     * @param Request $request
     *
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public function handleAction(Request $request)
    {
        $channel = new \AMQPChannel($this->amqpManager->getConnection());
        $queue = new \AMQPQueue($channel);
        $queue->setName($this->amqpQueue);


        $exchange = new \AMQPExchange($channel);
        $exchange->setName($this->amqpExchange);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $exchange->setFlags(AMQP_DURABLE);

        $exchange->declareExchange();
        $exchange->bind($this->amqpExchange, $this->amqpQueue);

        /**
         * delivery_mode = 2 - persist messages on storage
         * AMQP_REQUEUE - resend message to queue
         */
        $exchange->publish($request->getContent(), $queue->getName(), AMQP_REQUEUE, [
            'delivery_mode' => 2,
        ]);

        return new JsonResponse(['status' => 'ok']);
    }
}