<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonSection;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UpdatePriorityController
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
     * UpdatePriorityController constructor.
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
            $lessonSection = $this->entityManager->getRepository(LessonSection::class)->find($id);
            $this->courseAccessService->resolveUpdateAccess($lessonSection->getCourse());

            $this->entityManager->createQueryBuilder()
                ->update(LessonSection::class, 'ls')
                ->set('ls.priority', (int) $priority)
                ->where('ls.id = :id')
                ->setParameter('id', (int) $id)
                ->getQuery()->getResult();
        }

        return new JsonResponse(['status' => 'success']);
    }
}