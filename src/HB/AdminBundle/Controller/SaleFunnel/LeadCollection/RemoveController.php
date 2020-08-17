<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\LeadCollection;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLeadCollection;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveController
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
     * @var UploadCareService
     */
    private $uploadCareService;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * RemoveController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param UploadCareService      $uploadCareService
     * @param CourseAccessService    $access
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, UploadCareService $uploadCareService, CourseAccessService $access)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->uploadCareService = $uploadCareService;
        $this->access = $access;
    }


    /**
     * @param SalesFunnelLeadCollection $funnel
     *
     * @return RedirectResponse
     */
    public function handleAction(SalesFunnelLeadCollection $funnel)
    {
        $this->access->resolveDeleteAccess($funnel);

        $courseId = $funnel->getCourse()->getId();

        if ($funnel->getImage()) {
            $this->uploadCareService->removeFile($funnel->getImage()->getFileUuid());
        }

        if ($funnel->getFile()) {
            $this->uploadCareService->removeFile($funnel->getFile()->getFileUuid());
        }

        $this->entityManager->remove($funnel);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.sale_funnel.lead_collection.edit', ['id' => $courseId]));
    }
}
