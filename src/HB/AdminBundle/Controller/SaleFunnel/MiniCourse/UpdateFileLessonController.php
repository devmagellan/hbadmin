<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\MiniCourse;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\MiniCourse\MiniLesson;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdateFileLessonController
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
     * UpdateFileLessonController constructor.
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
     * @param MiniLesson $lesson
     * @param Request    $request
     *
     * @return JsonResponse
     */
    public function handleAction(MiniLesson $lesson, Request $request)
    {
        $this->access->resolveUpdateAccess($lesson->getFunnelMiniCourse());

        $fileUuid = $request->get('uuid');
        $fileCdn = $request->get('cdn');
        $fileName = $request->get('file_name');

        if ($fileUuid && $fileCdn && $fileName) {
            if ($lesson->getLessonFile()) {
                $this->uploadCareService->removeFile($lesson->getLessonFile()->getFileUuid());

                $this->entityManager->remove($lesson->getLessonFile());
            }
            $file = new UploadCareFile($fileUuid, $fileCdn, $fileName);

            $this->entityManager->persist($file);

            $lesson->setLessonFile($file);

            $this->entityManager->persist($lesson);

            $this->entityManager->flush();
            $this->access->registerFileAddEvent($file, [
                'description' => 'Образовательная воронка, файл урока',
                'course'      => $lesson->getFunnelMiniCourse()->getCourse()->getId(),
            ]);

            return new JsonResponse(['status' => 'success']);
        }

        return new JsonResponse(['status' => 'error']);
    }
}