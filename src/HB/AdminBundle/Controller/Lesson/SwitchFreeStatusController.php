<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Lesson;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;

class SwitchFreeStatusController
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
     * SwitchFreeStatusController constructor.
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
        $this->courseAccessService->resolveUpdateAccess($lesson->getSection()->getCourse());

        if ($lesson->getIsFree()) {
            $lesson->setIsFree(false);
        } else {
            $lesson->setIsFree(true);
        }

        $this->entityManager->persist($lesson);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}
