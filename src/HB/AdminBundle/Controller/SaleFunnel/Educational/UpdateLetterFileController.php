<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Educational;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\SaleFunnel\Educational\Letter;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelEducational;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdateLetterFileController
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
     * @var IntercomEventRecorder
     */
    private $eventRecorder;

    /**
     * UpdateLetterFileController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param UploadCareService      $uploadCareService
     * @param IntercomEventRecorder  $eventRecorder
     */
    public function __construct(EntityManagerInterface $entityManager, UploadCareService $uploadCareService, IntercomEventRecorder $eventRecorder)
    {
        $this->entityManager = $entityManager;
        $this->uploadCareService = $uploadCareService;
        $this->eventRecorder = $eventRecorder;
    }

    /**
     * @param Letter  $letter
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function handleAction(Letter $letter, Request $request)
    {
        $fileUuid = $request->get('uuid');
        $fileCdn = $request->get('cdn');
        $fileName = $request->get('file_name');
        $type = $request->get('type');

        if ($fileUuid && $fileCdn && $fileName && $type) {

            $file = new UploadCareFile($fileUuid, $fileCdn, $fileName);
            $previousFile = null;

            if (SalesFunnelEducational::ARTICLES_FILE === $type) {

                $previousFile = $letter->getArticleFile();
                $letter->setArticleFile($file);

            } else if (SalesFunnelEducational::LETTERS_FILE === $type) {

                $previousFile = $letter->getArticleFile();
                $letter->setLessonFile($file);
            }

            if ($previousFile) {
                $this->uploadCareService->removeFile($previousFile->getFileUuid());
            }

            $this->entityManager->persist($file);
            $this->entityManager->persist($letter);

            $this->entityManager->flush();
            $this->eventRecorder->registerFileAddEvent($file, [
                'description' => 'Обновление письма',
                'funnel'      => 'Образовательная',
            ]);

            return Json::ok();
        }

        return Json::error();
    }
}