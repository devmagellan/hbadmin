<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block4ActionCall1;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Block4ActionCall1RemoveImage
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
     * Block4ActionCall1RemoveImage constructor.
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
     * @param SalesFunnelOrganic $funnel
     *
     * @return JsonResponse
     */
    public function handleAction(SalesFunnelOrganic $funnel)
    {
        $this->courseAccessService->resolveDeleteAccess($funnel);

        $blockImage = $funnel->getBlock4Photo();

        if ($blockImage) {
            $this->uploadCareService->removeFile($blockImage->getFileUuid());

            $this->entityManager->remove($blockImage);
            $funnel->setBlock4Photo(null);
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();
        }

        return new JsonResponse(['status' => 'success']);
    }
}