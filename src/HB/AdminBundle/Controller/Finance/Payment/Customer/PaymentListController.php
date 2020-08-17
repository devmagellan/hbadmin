<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance\Payment\Customer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPayment;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PaymentListController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var Customer
     */
    private $currentUser;

    /**
     * @var IntercomEventRecorder
     */
    private $eventRecorder;

    /**
     * PaymentListController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     * @param TokenStorageInterface  $tokenStorage
     * @param IntercomEventRecorder  $eventRecorder
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, TokenStorageInterface $tokenStorage, IntercomEventRecorder $eventRecorder)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->eventRecorder = $eventRecorder;
    }

    /**
     * @return Response
     */
    public function handleAction()
    {
        $this->eventRecorder->registerEvent(IntercomEvents::ACCESS_REPORTS);

        $content = $this->twig->render('@HBAdmin/Finance/Payment/Customer/payment_list.html.twig', [
            'payments' => $this->getPaymentForCustomer($this->currentUser),
        ]);

        return new Response($content);
    }

    /**
     * @param Customer $customer
     *
     * @return mixed
     */
    private function getPaymentForCustomer(Customer $customer)
    {
        if ($customer->isProducer()) {
            $payments = $this->entityManager->createQueryBuilder()
                ->select('payment')
                ->from(CustomerPayment::class, 'payment')
                ->leftJoin('payment.customer', 'customer')
                ->leftJoin('customer.owner', 'owner')
                ->where('customer = :customer')
                ->orWhere('owner = :customer')
                ->setParameter('customer', $customer)
                ->orderBy('customer.id', 'DESC')
                ->addOrderBy('payment.paymentDate', 'DESC')
                ->getQuery()
                ->getResult();
        } else if ($customer->isAuthor()) {
            $payments = $this->entityManager->createQueryBuilder()
                ->select('payment')
                ->from(CustomerPayment::class, 'payment')
                ->leftJoin('payment.customer', 'customer')
                ->leftJoin('customer.owner', 'owner')
                ->where('customer = :customer')
                ->setParameter('customer', $customer)
                ->orderBy('payment.paymentDate', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            throw new \InvalidArgumentException('Customer id: %s, with role %s cannot view payments', $customer->getId(), $customer->getRole());
        }

        return $payments;
    }


}