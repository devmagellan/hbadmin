<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonSection;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\Response;

class LessonListController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * LessonListController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param LessonSection $section
     *
     * @return Response
     */
    public function handleAction(LessonSection $section): Response
    {
        $this->courseAccessService->resolveViewAccess($section->getCourse());

        $lessons = $this->entityManager->getRepository(Lesson::class)->findBy(['section' => $section], ['priority' => 'ASC']);

        $content = $this->twig->render('@HBAdmin/LessonSection/lesson_list.html.twig', [
            'lessons' => $lessons,
            'section' => $section
        ]);

        return new Response($content);
    }
}
