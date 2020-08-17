<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonSection;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdatePriorityLessonsController
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
     * UpdatePriorityLessonsController constructor.
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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function handleAction(Request $request): JsonResponse
    {
        $ids = json_decode($request->get('ids'), true);

        foreach ($ids as $id => $priority) {
            /** @var Lesson $lesson */
            $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);
            $this->courseAccessService->resolveUpdateAccess($lesson->getSection()->getCourse());

            $this->entityManager->createQueryBuilder()
                ->update(Lesson::class, 'l')
                ->set('l.priority', (int) $priority)
                ->where('l.id = :id')
                ->setParameter('id', (int) $id)
                ->getQuery()->getResult();
        }

        return new JsonResponse(['status' => 'success']);
    }
}