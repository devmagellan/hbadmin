<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block1TargetPage;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Block1BannerRemoveImageController
{
    /**
     * @var UploadCareService
     */
    private $uploadCareService;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * Block1BannerRemoveImageController constructor.
     *
     * @param UploadCareService      $uploadCareService
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(UploadCareService $uploadCareService, EntityManagerInterface $entityManager, CourseAccessService $courseAccessService)
    {
        $this->uploadCareService = $uploadCareService;
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
        $this->courseAccessService->resolveDeleteAccess($funnel);

        $blockImage = $funnel->getImageBanner();

        if ($blockImage) {
            $this->uploadCareService->removeFile($blockImage->getFileUuid());

            $this->entityManager->remove($blockImage);
            $funnel->setImageBanner(null);
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();
        }

        return new JsonResponse(['status' => 'success']);
    }
}