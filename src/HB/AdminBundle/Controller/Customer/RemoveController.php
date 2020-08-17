<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Service\CustomerAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RemoveController
{
    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var UploadCareService
     */
    private $uploadCareService;

    /**
     * @var CustomerAccessService
     */
    private $customerAccessService;

    /**
     * RemoveController constructor.
     *
     * @param TokenStorageInterface  $tokenStorage
     * @param EntityManagerInterface $entityManager
     * @param FlashBagInterface      $flashBag
     * @param RouterInterface        $router
     * @param UploadCareService      $uploadCareService
     * @param CustomerAccessService  $customerAccessService
     */
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager, FlashBagInterface $flashBag, RouterInterface $router, UploadCareService $uploadCareService, CustomerAccessService $customerAccessService)
    {
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->uploadCareService = $uploadCareService;
        $this->customerAccessService = $customerAccessService;
    }

    /**
     * @param Customer $customer
     * @param Request  $request
     *
     * @return RedirectResponse
     */
    public function handleAction(Customer $customer, Request $request): RedirectResponse
    {
        $this->customerAccessService->resolveCustomerInteractAccess($customer);
        $referer = $request->headers->get('referer', $this->router->generate('hb.customer.list'));

        if ($this->currentUser->getId() === $customer->getId()) {
            $this->flashBag->add('error', 'Невозможно удалить собственный аккаунт');

            return new RedirectResponse($referer);
        }

        if ($customer->getPhoto()) {
            $this->uploadCareService->removeFile($customer->getPhoto()->getFileUuid());
        }

        $this->entityManager->remove($customer);
        $this->entityManager->flush();

        return new RedirectResponse($referer);
    }
}
