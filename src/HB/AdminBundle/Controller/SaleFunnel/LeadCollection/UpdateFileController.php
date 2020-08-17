<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\LeadCollection;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLeadCollection;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdateFileController
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
     * UpdateFileController constructor.
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
     * @param SalesFunnelLeadCollection $funnel
     * @param Request                   $request
     *
     * @return JsonResponse
     */
    public function handleAction(SalesFunnelLeadCollection $funnel, Request $request)
    {
        $this->access->resolveUpdateAccess($funnel);

        $fileUuid = $request->get('uuid');
        $fileCdn = $request->get('cdn');
        $fileName = $request->get('file_name');

        if ($fileUuid && $fileCdn && $fileName) {
            if ($funnel->getFile()) {
                $this->uploadCareService->removeFile($funnel->getFile()->getFileUuid());

                $this->entityManager->remove($funnel->getFile());
            }
            $file = new UploadCareFile($fileUuid, $fileCdn, $fileName);
            $this->entityManager->persist($file);
            $funnel->setFile($file);
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();
            $this->access->registerFileAddEvent($file, [
                'description' => 'Сбор лидов, файл',
                'course'      => $funnel->getCourse()->getId(),
            ]);

            return new JsonResponse(['status' => 'success']);
        }

        return new JsonResponse(['status' => 'error']);


    }
}