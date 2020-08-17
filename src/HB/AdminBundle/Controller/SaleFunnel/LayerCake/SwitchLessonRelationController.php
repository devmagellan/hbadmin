<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\LayerCake;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLayerCake;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;

class SwitchLessonRelationController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * SwitchLessonRelationController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $access
     */
    public function __construct(EntityManagerInterface $entityManager, CourseAccessService $access)
    {
        $this->entityManager = $entityManager;
        $this->access = $access;
    }


    /**
     * @param Lesson               $lesson
     * @param SalesFunnelLayerCake $funnel
     *
     * @return JsonResponse
     */
    public function handleAction(Lesson $lesson, SalesFunnelLayerCake $funnel)
    {
        $this->access->resolveUpdateAccess($funnel);

        if ($funnel->hasLesson($lesson)) {
            $funnel->removeLesson($lesson);
        } else {
            $funnel->addLesson($lesson);
        }

        $this->entityManager->persist($funnel);
        $this->entityManager->flush();

        $isOtherLessonExistInSection = $this->isOtherLessonExistInSection($lesson, $funnel);

        if ($isOtherLessonExistInSection) {
            if (!$funnel->hasSection($lesson->getSection())) {
                $funnel->addSection($lesson->getSection());
            }
            $action = 'add';
        } else {
            if (!$isOtherLessonExistInSection && $funnel->hasSection($lesson->getSection())) {
                $funnel->removeSection($lesson->getSection());
            }
            $action = 'remove';
        }

        $this->entityManager->persist($funnel);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success', 'action' => $action]);
    }

    /**
     * @param Lesson               $lesson
     * @param SalesFunnelLayerCake $funnel
     *
     * @return bool
     */
    private function isOtherLessonExistInSection(Lesson $lesson, SalesFunnelLayerCake $funnel)
    {
        return (bool) $this->entityManager->createQueryBuilder()
            ->select('COUNT(lesson)')
            ->from(SalesFunnelLayerCake::class, 'funnel')
            ->leftJoin('funnel.lessons', 'lesson')
            ->leftJoin('lesson.section', 'section')
            ->where('section.id = :section_id')
            ->andWhere('funnel.id = :funnel_id')
            ->setParameters([
                'section_id' => $lesson->getSection()->getId(),
                'funnel_id' => $funnel->getId()
            ])->getQuery()->getSingleScalarResult();
    }
}