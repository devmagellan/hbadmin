<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block11PaymentTexts;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\Organic\PaymentText;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Block11PaymentTextRemoveController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * Block11PaymentTextRemoveController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param PaymentText        $paymentText
     * @param SalesFunnelOrganic $funnel
     *
     * @return JsonResponse
     */
    public function handleAction(PaymentText $paymentText, SalesFunnelOrganic $funnel)
    {
        $this->courseAccessService->resolveDeleteAccess($funnel);

        $funnel->removeBlock11PaymentText($paymentText);
        $this->entityManager->persist($funnel);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}