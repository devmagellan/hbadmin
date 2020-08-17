<?php

declare(strict_types = 1);


namespace HB\AdminBundle\AMQP;


class AMQPManager
{
    private const READ_TIMEOUT = 3600;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var \AMQPConnection
     */
    private $connection;

    /**
     * AMQPManager constructor.
     *
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $pass
     */
    public function __construct(string $host, string $port, string $user, string $pass)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->connection = new \AMQPConnection([
            'host'         => $host,
            'port'         => $port,
            'login'        => $user,
            'password'     => $pass,
            'read_timeout' => self::READ_TIMEOUT,
        ]);

        $this->connection->connect();
    }

    /**
     * @return \AMQPConnection
     */
    public function getConnection(): \AMQPConnection
    {
        $connection = null;

        if ($this->connection && $this->connection->isConnected()) {
            $connection = $this->connection;
        } else {
            $this->connection = new \AMQPConnection([
                'host'         => $this->host,
                'port'         => $this->port,
                'login'        => $this->user,
                'password'     => $this->pass,
                'read_timeout' => self::READ_TIMEOUT,
            ]);

            $this->connection->connect();

            $connection = $this->connection;
        }

        return $connection;
    }
}