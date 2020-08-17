<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar;


use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetWebinarPriceController
{
    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * GetWebinarPriceController constructor.
     *
     * @param CourseAccessService $courseAccessService
     */
    public function __construct(CourseAccessService $courseAccessService)
    {
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param SalesFunnelWebinar $funnel
     *
     * @return JsonResponse
     */
    public function handleAction(SalesFunnelWebinar $funnel): JsonResponse
    {
        $this->courseAccessService->resolveViewAccess($funnel);

        return Json::ok((string) $funnel->getPrice());
    }
}