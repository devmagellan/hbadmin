<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdateOtoFileController
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
    private $access;

    /**
     * UpdateOtoFileController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UploadCareService      $uploadCareService
     * @param CourseAccessService    $access
     */
    public function __construct(EntityManagerInterface $entityManager, UploadCareService $uploadCareService, CourseAccessService $access)
    {
        $this->entityManager = $entityManager;
        $this->uploadCareService = $uploadCareService;
        $this->access = $access;
    }


    /**
     * @param SalesFunnelOneTimeOffer $funnel
     * @param Request                 $request
     *
     * @return JsonResponse
     */
    public function handleAction(SalesFunnelOneTimeOffer $funnel, Request $request)
    {
        $this->access->resolveUpdateAccess($funnel);

        $fileUuid = $request->get('uuid');
        $fileCdn = $request->get('cdn');
        $fileName = $request->get('file_name');

        $type = $request->get('type');

        if ($fileUuid && $fileCdn && $fileName && $type) {
            $file = new UploadCareFile($fileUuid, $fileCdn, $fileName);
            $previousFile = null;


            if (SalesFunnelOneTimeOffer::OTO_FILE_TYPE_OFFER === $type) {
                $previousFile = $funnel->getOfferFile();
                $funnel->setOfferFile($file);
            } else if (SalesFunnelOneTimeOffer::OTO_FILE_TYPE_IMAGE === $type) {
                $previousFile = $funnel->getImage();
                $funnel->setImage($file);
            } else if (SalesFunnelOneTimeOffer::OTO_FILE_TYPE_VIDEO === $type) {
                $previousFile = $funnel->getVideo();
                $funnel->setVideo($file);
            }

            if ($previousFile) {
                $this->uploadCareService->removeFile($previousFile->getFileUuid());
            }

            $this->entityManager->persist($file);
            $this->entityManager->persist($funnel);

            $this->entityManager->flush();
            $this->access->registerFileAddEvent($file, [
                'description' => "Единоразовое предложение {$funnel->getId()}, {$type}",
                'course'      => $funnel->getCourse()->getId(),
            ]);

            return Json::ok();
        }

        return Json::error();
    }
}