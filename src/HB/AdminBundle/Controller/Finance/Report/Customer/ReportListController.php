<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance\Report\Customer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPaymentReport;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ReportListController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Customer
     */
    private $currentUser;

    private $eventRecorder;

    /**
     * PaymentReportsListController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface  $tokenStorage
     * @param IntercomEventRecorder  $eventRecorder
     */
    public function __construct(\Twig_Environment $twig, EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage, IntercomEventRecorder $eventRecorder)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->eventRecorder = $eventRecorder;
    }

    /**
     * @return Response
     */
    public function handleAction()
    {
        $content = $this->twig->render('@HBAdmin/Finance/Payment/Customer/report_list.html.twig', [
            'reports' => $this->getReportForCustomer($this->currentUser),
        ]);

        $this->eventRecorder->registerEvent(IntercomEvents::ACCESS_REPORTS);

        return new Response($content);
    }

    /**
     * @param Customer $customer
     *
     * @return mixed
     */
    private function getReportForCustomer(Customer $customer)
    {
        if ($customer->isProducer()) {
            $payments = $this->entityManager->createQueryBuilder()
                ->select('report')
                ->from(CustomerPaymentReport::class, 'report')
                ->leftJoin('report.customer', 'customer')
                ->leftJoin('customer.owner', 'owner')
                ->where('customer = :customer')
                ->orWhere('owner = :customer')
                ->setParameter('customer', $customer)
                ->orderBy('report.reportDate', 'DESC')
                ->addOrderBy('customer.id', 'DESC')
                ->getQuery()
                ->getResult();
        } else if ($customer->isAuthor()) {
            $payments = $this->entityManager->createQueryBuilder()
                ->select('report')
                ->from(CustomerPaymentReport::class, 'report')
                ->leftJoin('report.customer', 'customer')
                ->leftJoin('customer.owner', 'owner')
                ->where('customer = :customer')
                ->setParameter('customer', $customer)
                ->orderBy('report.reportDate', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            throw new \InvalidArgumentException('Customer id: %s, with role %s cannot view payments reports', $customer->getId(), $customer->getRole());
        }

        return $payments;
    }

}