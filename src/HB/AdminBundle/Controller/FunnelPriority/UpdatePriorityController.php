<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\FunnelPriority;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\FunnelPriority;
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
     * @param Course  $course
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function handleAction(Course $course, Request $request): JsonResponse
    {
        $this->courseAccessService->resolveUpdateAccess($course);

        $ids = json_decode($request->get('ids'), true);

        foreach ($ids as $id => $priority) {
            $this->entityManager->createQueryBuilder()
                ->update(FunnelPriority::class, 'fp')
                ->set('fp.priority', (int) $priority)
                ->where('fp.id = :id')
                ->andWhere('fp.course = :course')
                ->setParameters([
                    'id' => (int) $id,
                    'course' => $course
                ])
                ->getQuery()->getResult();
        }

        return new JsonResponse(['status' => 'success']);
    }
}