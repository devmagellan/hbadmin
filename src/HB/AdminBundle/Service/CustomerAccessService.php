<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerRole;
use HB\AdminBundle\Exception\CustomerInteractAccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CustomerAccessService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * CustomerAccessService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface  $tokenStorage
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->currentUser = $tokenStorage->getToken()->getUser();
    }

    /**
     * Resolve roles available for add by current customer
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function resolveCustomerRolesAvailableForCurrentUserQuery()
    {
        if ($this->currentUser->isProducer()) {

            $availableRolesForAction = [
                '\''.CustomerRole::ROLE_AUTHOR.'\'',
                '\''.CustomerRole::ROLE_MANAGER.'\'',
            ];

        } else if ($this->currentUser->isAuthor()) {

            $availableRolesForAction = ['\''.CustomerRole::ROLE_MANAGER.'\''];

        } else if ($this->currentUser->isManager()) {

            throw new AccessDeniedException('Менеджер не может управлять пользователями');

        } else if ($this->currentUser->isAdmin()) {

            $availableRolesForAction = [
                '\''.CustomerRole::ROLE_ADMIN.'\'',
                '\''.CustomerRole::ROLE_PRODUCER.'\'',
                '\''.CustomerRole::ROLE_AUTHOR.'\'',
            ];
        } else {
            throw new \InvalidArgumentException('Unknown role for action with customer');
        }

        $queryDql = 'role.name IN ('.implode(',', $availableRolesForAction).')';

        return $this->entityManager->createQueryBuilder()
            ->select('role')
            ->from(CustomerRole::class, 'role')
            ->where($queryDql);
    }

    /**
     * Get customer list depends of current customer role
     *
     * @return \Doctrine\ORM\Query
     */
    public function getCustomersListQuery()
    {
        if ($this->currentUser->isAdmin()) {
            $customers = $this->entityManager->createQueryBuilder()
                ->select('customer')
                ->from(Customer::class, 'customer')
                ->orderBy('customer.dateCreatedAt', 'DESC')
                ->getQuery();
        } else if ($this->currentUser->isProducer()) {
            $customers = $this->entityManager->createQueryBuilder()
                ->select('customer')
                ->from(Customer::class, 'customer')
                ->where('customer.producer = :producer_id')
                ->orWhere('customer.id = :current_user_id')
                ->orderBy('customer.dateCreatedAt', 'DESC')
                ->setParameters([
                    'producer_id'     => $this->currentUser->getId(),
                    'current_user_id' => $this->currentUser->getId(),
                ])
                ->getQuery();
        } else if ($this->currentUser->isAuthor()) {
            $customers = $this->entityManager->createQueryBuilder()
                ->select('customer')
                ->from(Customer::class, 'customer')
                ->where('customer.owner = :owner_id')
                ->orWhere('customer.id = :current_user_id')
                ->orderBy('customer.dateCreatedAt', 'DESC')
                ->setParameters([
                    'owner_id'        => $this->currentUser->getId(),
                    'current_user_id' => $this->currentUser->getId(),
                ])
                ->getQuery();
        } else if ($this->currentUser->isManager()) {
            $customers = $this->entityManager->createQueryBuilder()
                ->select('customer')
                ->from(Customer::class, 'customer')
                ->where('customer.id = :current_user_id')
                ->orderBy('customer.dateCreatedAt', 'DESC')
                ->setParameters([
                    'current_user_id' => $this->currentUser->getId(),
                ])
                ->getQuery();
        } else {
            throw new AccessDeniedException(sprintf('User (id: %s) cannot get access for users list', $this->currentUser->getId()));
        }


        return $customers;
    }


    /**
     * @return array
     */
    public function getAvailableAuthorsForCourseCreate()
    {
        /** @var Customer $currentUser */
        $currentUser = $this->currentUser;

        $customers = [];

        if ($currentUser->isProducer()) {
            $customers = $this->entityManager->createQueryBuilder()
                ->select('customer')
                ->from(Customer::class, 'customer')
                ->where('customer.producer = :producer')
                ->orWhere('customer.id = :customer_id')
                ->setParameters([
                    'producer'    => $currentUser,
                    'customer_id' => $currentUser->getId(),
                ])
                ->getQuery()->getResult();
        } else if ($currentUser->isAuthor()) {
            $customers = $this->entityManager->createQueryBuilder()
                ->select('customer')
                ->from(Customer::class, 'customer')
                ->where('customer.id = :customer_id')
                ->setParameters(['customer_id' => $currentUser->getId()])
                ->getQuery()->getResult();
        } else if ($currentUser->isAdmin()) {
            $customers = $this->entityManager->createQueryBuilder()
                ->select('customer')
                ->from(Customer::class, 'customer')
                ->leftJoin('customer.role', 'role')
                ->where('role.name != :role_manager')
                ->setParameters(['role_manager' => CustomerRole::ROLE_MANAGER])
                ->getQuery()->getResult();
        } else if ($currentUser->isManager()) {
            $customers = $this->entityManager->createQueryBuilder()
                ->select('customer')
                ->from(Customer::class, 'customer')
                ->where('customer.id = :customer_id')
                ->setParameters(['customer_id' => $currentUser->getOwner()->getId()])
                ->getQuery()->getResult();
        }

        return $customers;
    }

    /**
     * @param Customer $customer
     *
     * @return bool
     * @throws CustomerInteractAccessDeniedException
     */
    public function resolveCustomerInteractAccess(Customer $customer)
    {
        $exceptionMessage = sprintf('Доступ запрещен к взаимодействию с пользователем id: %s, текущий пользователь: %s', $customer->getId(), $this->currentUser->getId());

        if ($this->currentUser->isAuthor()) {
            if (($customer->getOwner() && $customer->getOwner()->getId() === $this->currentUser->getId())
                || $customer->getId() === $this->currentUser->getId()) {
                return true;
            }
        } else if ($this->currentUser->isAdmin()) {
            return true;
        } else if ($this->currentUser->isProducer()) {
            if ($customer->getProducer() && $customer->getProducer()->getId() === $this->currentUser->getId()) {
                return true;
            }
        }

        throw new CustomerInteractAccessDeniedException($exceptionMessage);
    }

    /**
     * Get query authors created by producer
     *
     * @param Customer $customer
     *
     * @return mixed
     */
    public function getProducerAuthorsQuery(Customer $customer)
    {
        return $this->entityManager->createQueryBuilder()
            ->select('customer')
            ->from(Customer::class, 'customer')
            ->leftJoin('customer.role', 'role')
            ->where('customer.owner = :customer')
            ->andWhere('role.name = :role ')
            ->setParameters([
                'customer' => $customer->getId(),
                'role'     => CustomerRole::ROLE_AUTHOR,
            ]);
    }

    /**
     * @return array| int[]
     */
    public function getAvailableCustomerIds()
    {
        $query = $this->getCustomersListQuery();

        $ids = [];

        /** @var Customer $customer */
        foreach ($query->getResult() as $customer) {
            $ids[] = $customer->getId();
        }

        return $ids;
    }

}