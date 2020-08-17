<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block9Companies;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\Organic\CompanyLogo;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;

class Block9CompaniesRemoveImageController
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
     * Block9CompaniesRemoveImageController constructor.
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
     * @param CompanyLogo        $logo
     *
     * @return JsonResponse
     */
    public function handleAction(SalesFunnelOrganic $funnel, CompanyLogo $logo)
    {
        $this->courseAccessService->resolveDeleteAccess($funnel);

        $blockImage = $logo->getLogo();

        if ($blockImage) {
            $this->uploadCareService->removeFile($blockImage->getFileUuid());
            $logo->setLogo(null);
            $this->entityManager->remove($blockImage);
        }
        $funnel->removeBlock9CompaniesLogo($logo);

        $this->entityManager->persist($funnel);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}