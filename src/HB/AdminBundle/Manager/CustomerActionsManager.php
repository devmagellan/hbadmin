<?php

namespace HB\AdminBundle\Manager;

use Doctrine\ORM\EntityManager;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerAction;

class CustomerActionsManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * CustomerActionsManager constructor.
     *
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    /**
     * @param $title
     * @param Customer $customer
     * @param $type
     * @param string $status
     */
    public function publish($title, Customer $customer, $type, $status = CustomerAction::STATUS_NEW_MODERATE): void
    {
        $action = new CustomerAction();

        $action->setTitle($title);
        $action->setStatus($status);
        $action->setCustomer($customer);
        $action->setType($type);

        $this->entityManager->persist($action);
        $this->entityManager->flush();
    }

    /**
     * @param CustomerAction $action
     */
    public function actionIsViewed(CustomerAction $action): void
    {
        $action->setIsViewed(true);

        $this->entityManager->persist($action);
        $this->entityManager->flush();
    }
}