<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\LeadCollection;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLeadCollection;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveFileController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UploadCareService
     */
    private $uploadCareService;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * RemoveFileController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UploadCareService      $uploadCareService
     * @param RouterInterface        $router
     * @param CourseAccessService    $access
     */
    public function __construct(EntityManagerInterface $entityManager, UploadCareService $uploadCareService, RouterInterface $router, CourseAccessService $access)
    {
        $this->entityManager = $entityManager;
        $this->uploadCareService = $uploadCareService;
        $this->router = $router;
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

        if ($funnel->getFile()) {
            $this->uploadCareService->removeFile($funnel->getFile()->getFileUuid());

            $this->entityManager->remove($funnel->getFile());
        }

        $funnel->setFile(null);

        $this->entityManager->persist($funnel);

        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.sale_funnel.lead_collection.edit', ['id' => $funnel->getCourse()->getId()]));
    }
}