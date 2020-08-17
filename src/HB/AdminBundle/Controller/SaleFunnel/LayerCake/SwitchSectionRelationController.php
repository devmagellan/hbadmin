<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\LayerCake;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLayerCake;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;

class SwitchSectionRelationController
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
     * SwitchSectionRelationController constructor.
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
     * @param LessonSection        $section
     * @param SalesFunnelLayerCake $funnel
     *
     * @return JsonResponse
     */
    public function handleAction(LessonSection $section, SalesFunnelLayerCake $funnel)
    {
        $this->access->resolveUpdateAccess($funnel);

        if ($funnel->hasSection($section)) {

            foreach ($section->getLessons() as $lesson) {
                if ($funnel->hasLesson($lesson)) {
                    $funnel->removeLesson($lesson);
                }

                $funnel->removeSection($section);
            }

            $this->entityManager->persist($funnel);
            $this->entityManager->flush();

            return new JsonResponse(['action' => 'removed']);
        } else {
            $funnel->addSection($section);

            foreach ($section->getLessons() as $lesson) {
                if (!$funnel->hasLesson($lesson)) {
                    $funnel->addLesson($lesson);
                }
            }

            $this->entityManager->persist($funnel);
            $this->entityManager->flush();

            return new JsonResponse(['action' => 'added']);
        }


    }
}