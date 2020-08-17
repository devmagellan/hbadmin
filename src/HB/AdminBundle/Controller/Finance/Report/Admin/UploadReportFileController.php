<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance\Report\Admin;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\CustomerPaymentReport;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use HB\AdminBundle\Service\UploadCareService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class UploadReportFileController
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
     * @var IntercomEventRecorder
     */
    private $eventRecorder;

    /**
     * UploadReportFileController constructor.
     *
     * @param UploadCareService      $uploadCareService
     * @param EntityManagerInterface $entityManager
     * @param IntercomEventRecorder  $eventRecorder
     */
    public function __construct(UploadCareService $uploadCareService, EntityManagerInterface $entityManager, IntercomEventRecorder $eventRecorder)
    {
        $this->uploadCareService = $uploadCareService;
        $this->entityManager = $entityManager;
        $this->eventRecorder = $eventRecorder;
    }

    /**
     * @param CustomerPaymentReport $report
     * @param Request               $request
     *
     * @return JsonResponse
     */
    public function handleAction(CustomerPaymentReport $report, Request $request)
    {
        $fileUuid = $request->get('uuid');
        $fileCdn = $request->get('cdn');
        $fileName = $request->get('file_name');

        if ($fileUuid && $fileCdn && $fileName) {
            if ($report->getFile()) {
                $this->uploadCareService->removeFile($report->getFile()->getFileUuid());

                $this->entityManager->remove($report->getFile());
            }
            $file = new UploadCareFile($fileUuid, $fileCdn, $fileName);

            $this->entityManager->persist($file);

            $report->setFile($file);

            $this->entityManager->persist($report);

            $this->entityManager->flush();
            $this->eventRecorder->registerFileAddEvent($file, [
                'description' => 'Файл фин. отчета',
            ]);

            return Json::ok();
        }

        return Json::error();
    }
}