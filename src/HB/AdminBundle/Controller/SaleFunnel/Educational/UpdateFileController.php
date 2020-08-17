<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Educational;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelEducational;
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
     * UpldateFileController constructor.
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
     * @param SalesFunnelEducational $funnel
     * @param Request                $request
     *
     * @return JsonResponse
     */
    public function handleAction(SalesFunnelEducational $funnel, Request $request)
    {
        $this->access->resolveUpdateAccess($funnel);

        $fileUuid = $request->get('uuid');
        $fileCdn = $request->get('cdn');
        $fileName = $request->get('file_name');
        $type = $request->get('type');

        if ($fileUuid && $fileCdn && $fileName && $type) {

            $file = new UploadCareFile($fileUuid, $fileCdn, $fileName);
            $previousFile = null;

            if (SalesFunnelEducational::ARTICLES_FILE === $type) {

                $previousFile = $funnel->getArticlesFile();
                $funnel->setArticlesFile($file);

            } else if (SalesFunnelEducational::LETTERS_FILE === $type) {

                $previousFile = $funnel->getArticlesFile();
                $funnel->setLettersFile($file);
            }

            if ($previousFile) {
                $this->uploadCareService->removeFile($previousFile->getFileUuid());
            }

            $this->entityManager->persist($file);
            $this->entityManager->persist($funnel);

            $this->entityManager->flush();
            $this->access->registerFileAddEvent($file, [
                'description' => 'Образовательная воронка, файл',
                'course'      => $funnel->getCourse()->getId(),
            ]);

            return Json::ok();
        }

        return Json::error();
    }
}