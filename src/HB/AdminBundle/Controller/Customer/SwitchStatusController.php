<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Service\CustomerAccessService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SwitchStatusController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var CustomerAccessService
     */
    private $customerAccessService;

    /**
     * SwitchStatusController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param TokenStorageInterface  $tokenStorage
     * @param FlashBagInterface      $flashBag
     * @param CustomerAccessService     $customerAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, TokenStorageInterface $tokenStorage, FlashBagInterface $flashBag, CustomerAccessService $customerAccessService)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->flashBag = $flashBag;
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

        if ($customer->getId() !== $this->currentUser->getId()) {
            if ($customer->isEnabled()) {
                $customer->disable();
            } else {
                $customer->enable();
            }
        } else {
            $this->flashBag->add('error', 'Вы не можете заблокировать свой аккаунт');
        }


        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return new RedirectResponse($referer);
    }
}