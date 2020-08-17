<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\CustomerAction;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\ActionLog;
use HB\AdminBundle\Entity\Customer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MarkPublishedController
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
     * MarkPublishedController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface               $tokenStorage
     */
    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->currentUser = $tokenStorage->getToken()->getUser();
    }

    /**
     * @param ActionLog $log
     *
     * @return JsonResponse
     */
    public function handleAction(ActionLog $log)
    {
        if ($this->currentUser->isAdmin()) {
            $log->setPublished(true);
            $this->entityManager->persist($log);
            $this->entityManager->flush();
        } else {
            return Json::error('Доступ запрещен');
        }

        return Json::ok();
    }
}