<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonElement;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\LessonElement;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdatePriorityLessonElementsController
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
     * UpdatePriorityLessonElementsController constructor.
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
            /** @var LessonElement $lessonElement */
            $lessonElement = $this->entityManager->getRepository(LessonElement::class)->find($id);

            if ($lessonElement) {
                $this->courseAccessService->resolveUpdateAccess($lessonElement->getLesson()->getSection()->getCourse());
            }

            $this->entityManager->createQueryBuilder()
                ->update(LessonElement::class, 'ls')
                ->set('ls.priority', (int) $priority)
                ->where('ls.id = :id')
                ->setParameter('id', (int) $id)
                ->getQuery()->getResult();
        }

        return new JsonResponse(['status' => 'success']);
    }
}