<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\CustomerAction;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\CustomerAction;
use Symfony\Component\HttpFoundation\JsonResponse;

class ViewedController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ViewedController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param CustomerAction $action
     *
     * @return JsonResponse
     */
    public function handleAction(CustomerAction $action): JsonResponse
    {
        $action->setIsViewed(true);

        try {
            $this->entityManager->persist($action);
            $this->entityManager->flush();
        } catch (\Exception $ex) {
            return new JsonResponse(['status' => 'error']);
        }

        return new JsonResponse(['status' => 'done']);
    }
}