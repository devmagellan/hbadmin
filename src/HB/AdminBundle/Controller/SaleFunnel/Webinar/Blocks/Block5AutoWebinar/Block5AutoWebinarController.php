<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block5AutoWebinar;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Block5AutoWebinarController
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
     * Block5AutoWebinarController constructor.
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
     *
     * @return JsonResponse
     */
    public function handleAction(SalesFunnelWebinar $funnel)
    {
        $this->courseAccessService->resolveUpdateAccess($funnel);

        if ($funnel->isAutoWebinar()) {
            $funnel->setAutoWebinar(false);
        } else {
            $funnel->setAutoWebinar(true);
        }

        $this->entityManager->persist($funnel);
        $this->entityManager->flush();

        return Json::ok();
    }
}