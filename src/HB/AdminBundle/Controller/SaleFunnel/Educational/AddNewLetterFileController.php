<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Educational;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Component\Json;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AddNewLetterFileController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var IntercomEventRecorder
     */
    private $eventRecorder;

    /**
     * AddNewLetterFileController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param IntercomEventRecorder  $eventRecorder
     */
    public function __construct(EntityManagerInterface $entityManager, IntercomEventRecorder $eventRecorder)
    {
        $this->entityManager = $entityManager;
        $this->eventRecorder = $eventRecorder;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function handleAction(Request $request)
    {
        $fileUuid = $request->get('uuid');
        $fileCdn = $request->get('cdn');
        $fileName = $request->get('file_name');

        if ($fileUuid && $fileCdn && $fileName) {

            $file = new UploadCareFile($fileUuid, $fileCdn, $fileName);

            $this->entityManager->persist($file);

            $this->entityManager->flush();
            $this->eventRecorder->registerFileAddEvent($file, [
                'description' => 'Добавление нового письма',
                'funnel'      => 'Образовательная',
            ]);

            return Json::ok((string) $file->getId());
        }

        return Json::error();
    }
}