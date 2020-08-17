<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonSection;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;

class MoveLessonToSectionController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * MoveLessonToSectionController constructor.
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
     * @param LessonSection $section
     * @param Lesson        $lesson
     *
     * @return JsonResponse
     */
    public function handleAction(LessonSection $section, Lesson $lesson): JsonResponse
    {
        $this->courseAccessService->resolveUpdateAccess($section->getCourse());
        $lesson->setSection($section);
        $this->entityManager->persist($lesson);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }

}