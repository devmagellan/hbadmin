<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\DTO\CustomerStatsDTO;
use HB\AdminBundle\Entity\CustomerPayment;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;
use HB\AdminBundle\Service\DqlFilters;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class ConsolidateReportController
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
     * ConsolidateReportController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Request $request)
    {
        $transactions = $this->getTransactions($request);
        $payments = $this->getPayments($request);

        $customerStats = $this->buildArray($transactions, $payments);

        $content = $this->twig->render('@HBAdmin/Finance/consolidate_report.html.twig', [
            'customerStats' => $customerStats,
        ]);

        return new Response($content);
    }

    /**
     * @param Request $request
     *
     * @return array | TeachableTransaction[]
     */
    private function getTransactions(Request $request)
    {
        // todo: create filters  (date 100%! for decrease load)
        $filters = $request->get('f', []);
        $filters['status'] = TeachableTransaction::TRANSACTION_STATUS_PAID;

        $filters = new DqlFilters($filters);
        $filters->equal('t.status', 'status');

        $transactionsDql = "
            SELECT t FROM HBAdminBundle:Teachable\TeachableTransaction t
            INNER JOIN t.internalCourse course
            INNER JOIN course.owner owner ".$filters->getDql()."
        ";

        $query = $this->entityManager->createQuery($transactionsDql)->setParameters($filters->getParameters());

        return $query->getResult();
    }


    /**
     * @param Request $request
     *
     * @return array | TeachableTransaction[]
     */
    private function getPayments(Request $request)
    {
        // todo: create filters (date 100%! for decrease load)
        $filters = $request->get('f', []);

        $filters = new DqlFilters($filters);

        $transactionsDql = "
            SELECT p FROM HBAdminBundle:CustomerPayment p
            INNER JOIN p.customer customer ".$filters->getDql()."
        ";

        $query = $this->entityManager->createQuery($transactionsDql)->setParameters($filters->getParameters());

        return $query->getResult();
    }

    /**
     * @param array| TeachableTransaction[] $transactions
     * @param array| CustomerPayment[]      $payments
     *
     * @return CustomerStatsDTO[]
     */
    private function buildArray(array $transactions, array $payments): array
    {
        $customersStats = [];

        foreach ($transactions as $transaction) {
            if ($transaction->getInternalCourse()->getOwner()->getProducer()) {
                $customerId = $transaction->getInternalCourse()->getOwner()->getProducer()->getId();
                $customer = $transaction->getInternalCourse()->getOwner()->getProducer();
            } else {
                $customerId = $transaction->getInternalCourse()->getOwner()->getId();
                $customer = $transaction->getInternalCourse()->getOwner();
            }

            if (isset($customersStats[$customerId])) {
                $dto = $customersStats[$customerId];
            } else {
                $customersStats[$customerId] = new CustomerStatsDTO($customer);
                $dto = $customersStats[$customerId];
            }

            /** @var CustomerStatsDTO $dto */
            $dto->increaseIncome($transaction);
        }

        foreach ($payments as $payment) {
            $customerId = $payment->getCustomer()->getId();

            if (isset($customersStats[$customerId])) {
                $dto = $customersStats[$customerId];
            } else {
                $customersStats[$customerId] = new CustomerStatsDTO($payment->getCustomer());
                $dto = $customersStats[$customerId];
            }

            /** @var CustomerStatsDTO $dto */
            $dto->increasePaid($payment);
        }

        return $customersStats;
    }
}