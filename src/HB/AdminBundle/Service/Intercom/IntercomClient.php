<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Service\Intercom;

class IntercomClient
{
    /**
     * @var \Intercom\IntercomClient
     */
    private $client;

    /**
     * IntercomClient constructor.
     *
     * @param string                      $token
     */
    public function __construct(string $token)
    {
        $this->init($token);
    }

    /**
     * @return \Intercom\IntercomClient
     */
    public function getClient()
    {
        return $this->client;
    }


    /**
     * @param string $token
     */
    private function init(string $token)
    {
        $this->client = new \Intercom\IntercomClient($token);
    }

}