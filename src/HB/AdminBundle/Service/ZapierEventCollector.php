<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;

class ZapierEventCollector
{
    private const PROFILE_COMPLETED_URL = 'https://hooks.zapier.com/hooks/catch/2331074/jxv830/';
    private const PASSWORD_CONFIRMED_URL = '';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ZapierEventCollector constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Customer $customer
     */
    public function profileCompleted(Customer $customer)
    {
        if (!$customer->isZapierProfileCompletedEvent()) {
            $data = [
                'email' => $customer->getEmail(),
                'name'  => $customer->getFirstName().' '.$customer->getSurname(),
            ];

            $result = $this->send(self::PROFILE_COMPLETED_URL, $data);

            if ($result) {
                $customer->setZapierProfileCompletedEvent(true);
                $this->entityManager->persist($customer);
                $this->entityManager->flush();
            }
        }
    }

    /**
     * @param Customer $customer
     * @param string   $password
     */
    public function passwordConfirmed(Customer $customer, string $password)
    {
        $data = [
            'email'    => $customer->getEmail(),
            'password' => $password,
        ];

        $this->send(self::PASSWORD_CONFIRMED_URL, $data);
    }

    /**
     * Send data to url
     *
     * @param string $url
     * @param array  $data
     *
     * @return bool
     */
    private function send(string $url, array $data): bool
    {
        $postData = http_build_query($data);
        $options = [
            'http' =>
                [
                    'method'  => 'POST',
                    'header'  => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => $postData,
                ],
        ];

        $context = stream_context_create($options);

        $result = file_get_contents($url, false, $context);

        $decodedResult = json_decode($result, true);

        if (array_key_exists('status', $decodedResult) && $decodedResult['status'] === 'success') {
            return true;
        }

        return false;
    }

}