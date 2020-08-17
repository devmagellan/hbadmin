<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Listener;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CustomerActivityListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * CustomerActivityListener constructor.
     *
     * @param TokenStorageInterface  $tokenStorage
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    public function updateCustomerActivityDate()
    {
        if ($this->tokenStorage->getToken()) {
            /** @var Customer $customer */
            $customer = $this->tokenStorage->getToken()->getUser();

            if ($customer instanceof Customer) {
                $customer->setLastActivityAt(new \DateTime('now'));
                $this->entityManager->persist($customer);
                $this->entityManager->flush();
            }
        }
    }


}