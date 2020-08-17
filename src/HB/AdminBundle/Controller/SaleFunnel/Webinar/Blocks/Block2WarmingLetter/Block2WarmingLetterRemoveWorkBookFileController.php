<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block2WarmingLetter;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Block2WarmingLetterRemoveWorkBookFileController
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
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * Block2WarmingLetterRemoveWorkBookFileController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UploadCareService      $uploadCareService
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, UploadCareService $uploadCareService, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->uploadCareService = $uploadCareService;
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

        $file = $funnel->getBlock2WorkBookFile();
        if ($file) {
            $this->uploadCareService->removeFile($file->getFileUuid());

            $this->entityManager->remove($file);
            $funnel->setBlock2WorkBookFile(null);
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();
        }

        return new JsonResponse(['status' => 'success']);
    }
}