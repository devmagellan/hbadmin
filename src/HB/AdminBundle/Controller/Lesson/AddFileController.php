<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Lesson;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonElement;
use HB\AdminBundle\Entity\Types\LessonElementType;
use HB\AdminBundle\Entity\UploadCareFile;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\UploadCareService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AddFileController
{
    private const MAX_FILES = 20;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var UploadCareService
     */
    private $uploadCareService;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * AddFileController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     * @param UploadCareService      $uploadCareService
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, UploadCareService $uploadCareService, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->uploadCareService = $uploadCareService;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param Lesson  $lesson
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function handleAction(Lesson $lesson, Request $request): JsonResponse
    {
        $this->courseAccessService->resolveCreateAccess($lesson->getSection()->getCourse());

        $fileCdn = $request->get('cdn');
        $fileUuid = $request->get('uuid');
        $fileName = $request->get('file_name');

        $files = $this->entityManager->getRepository(LessonElement::class)->findBy([
            'type'   => LessonElementType::FILE,
            'lesson' => $lesson,
        ]);

        if (self::MAX_FILES <= \count($files)) {
            $this->uploadCareService->removeFile($fileUuid);

            return new JsonResponse(['status' => 'error', 'message' => 'Загружено максимальное количество файлов']);
        } else {
            if ($fileCdn && $fileUuid && $fileName) {
                $file = new UploadCareFile($fileUuid, $fileCdn, $fileName);
                $this->entityManager->persist($file);

                $element = new LessonElement($lesson, new LessonElementType(LessonElementType::FILE));
                $element->setFile($file);

                $this->entityManager->persist($element);
                $this->entityManager->flush();
                $this->courseAccessService->registerFileAddEvent($file, [
                    'description' => 'Файл урока',
                    'course'      => $lesson->getSection()->getCourse()->getId(),
                ]);
            } else {
                return new JsonResponse(['status' => 'error', 'message' => 'Invalid file data']);
            }
        }

        return new JsonResponse(['status' => 'success']);
    }
}