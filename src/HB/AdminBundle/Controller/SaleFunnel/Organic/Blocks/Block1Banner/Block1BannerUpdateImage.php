<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block1Banner;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Block1BannerUpdateImage
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
    private $courseAccess;

    /**
     * Block1BannerUpdateImage constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UploadCareService      $uploadCareService
     * @param CourseAccessService    $courseAccess
     */
    public function __construct(EntityManagerInterface $entityManager, UploadCareService $uploadCareService, CourseAccessService $courseAccess)
    {
        $this->entityManager = $entityManager;
        $this->uploadCareService = $uploadCareService;
        $this->courseAccess = $courseAccess;
    }


    /**
     * @param SalesFunnelOrganic $funnel
     * @param Request            $request
     *
     * @return JsonResponse
     */
    public function handleAction(SalesFunnelOrganic $funnel, Request $request)
    {
        $this->courseAccess->resolveUpdateAccess($funnel);

        $fileUuid = $request->get('uuid');
        $fileCdn = $request->get('cdn');
        $fileName = $request->get('file_name');

        if ($fileUuid && $fileCdn && $fileName) {
            if ($funnel->getBlock1ImageBanner()) {
                $this->uploadCareService->removeFile($funnel->getBlock1ImageBanner()->getFileUuid());
                $this->entityManager->remove($funnel->getBlock1ImageBanner());
            }
            $file = new UploadCareFile($fileUuid, $fileCdn, $fileName);

            $this->entityManager->persist($file);

            $funnel->setBlock1ImageBanner($file);

            $this->entityManager->persist($funnel);

            $this->entityManager->flush();
            $this->courseAccess->registerImageAddEvent($file, [
                'description' => 'Органическая воронка, блок 1, баннер',
                'course'      => $funnel->getCourse()->getId(),
            ]);

            return new JsonResponse(['status' => 'success']);
        }

        return new JsonResponse(['status' => 'error']);

    }
}