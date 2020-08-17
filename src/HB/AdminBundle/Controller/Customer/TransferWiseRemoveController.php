<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Customer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\CustomerTransferWiseAccount;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class TransferWiseRemoveController
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
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * TransferWiseRemoveController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param FlashBagInterface      $flashBag
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, FlashBagInterface $flashBag)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->flashBag = $flashBag;
    }

    /**
     * @param CustomerTransferWiseAccount $account
     * @param Request                     $request
     *
     * @return RedirectResponse
     */
    public function handleAction(CustomerTransferWiseAccount $account, Request $request)
    {
        $redirect = $request->headers->get('referer', $this->router->generate('hb.customer.list'));

        $customer = $account->getCustomer();
        $account->getCustomer()->setTransferWiseAccount(null);
        $this->entityManager->remove($account);
        $this->entityManager->persist($customer);
        $this->entityManager->flush();

        return new RedirectResponse($redirect);
    }
}