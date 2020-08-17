<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block1TargetPage;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarThesis;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Block1RemoveThesisController
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
     * Block1RemoveThesisController constructor.
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
     * @param SalesFunnelWebinar $funnel
     * @param WebinarThesis      $thesis
     *
     * @return JsonResponse
     */
    public function handleAction(SalesFunnelWebinar $funnel, WebinarThesis $thesis)
    {
        $this->courseAccessService->resolveDeleteAccess($funnel);

        $funnel->removeThesis($thesis);
        $this->entityManager->remove($thesis);
        $this->entityManager->persist($funnel);

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}