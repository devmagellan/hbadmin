<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Lesson;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonElement;
use HB\AdminBundle\Entity\Types\LessonElementType;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;

class CheckFileCountController
{
    private const MAX_FILES = 20;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * CheckFileCountController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param Lesson $lesson
     *
     * @return JsonResponse
     */
    public function handleAction(Lesson $lesson): JsonResponse
    {
        $this->courseAccessService->resolveViewAccess($lesson->getSection()->getCourse());

        $files = $this->entityManager->getRepository(LessonElement::class)->findBy([
            'type'   => LessonElementType::FILE,
            'lesson' => $lesson,
        ]);

        if (self::MAX_FILES <= \count($files)) {
            return new JsonResponse(['status' => 'error', 'errorMessage' => 'Максимум допустимо '.self::MAX_FILES.' файла']);
        }

        return new JsonResponse(['status' => 'success']);
    }
}