<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonSection;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\CoursePriceBlock;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UpdatePriceBlockController
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
     * UpdatePriceBlockController constructor.
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
     * @param LessonSection    $section
     * @param CoursePriceBlock $block
     *
     * @return Response
     */
    public function handleAction(LessonSection $section, CoursePriceBlock $block): Response
    {
        $this->courseAccessService->resolveUpdateAccess($section->getCourse());

        if ($section->hasPriceBlock($block->getId())) {
            $section->removePriceBlock($block);
        } else {
            $section->addPriceBlock($block);
        }

        $this->entityManager->persist($section);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}
