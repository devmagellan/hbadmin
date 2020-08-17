<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service;


use Curl\Curl;
use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;

class SmsSender
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SmsSender constructor.
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
    public function sendSignUpSms(Customer $customer)
    {
        $code = rand(100000, 999999);
        $customer->setConfirmSmsCode((string) $code);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        $smsText = sprintf('Heartbeat education. Код подтверджения: %s', $code);
        $smsData = [
            'sms_phones' => $customer->getPhone(),
            'type'       => 'groupSms',
            'key'        => 'salepartnersleadadssms',
            'sms_text'   => $smsText,
        ];

        $smsCurl = new Curl();
        $smsCurl->setBasicAuthentication('bQiQt6pw5Cs', 'RFGuJHEf');
        $smsCurl->post('https://smsadmin.baxtep.com/webhook', $smsData);
        $smsCurl->close();
    }
}