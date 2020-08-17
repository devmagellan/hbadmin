<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance\Payment\Admin;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\CustomerPayment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class PaymentRemoveController
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
     * RemoveController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    /**
     * @param CustomerPayment $payment
     *
     * @return RedirectResponse
     */
    public function handleAction(CustomerPayment $payment)
    {
//        $this->entityManager->remove($payment);
//        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.finance.admin.payments'));
    }
}