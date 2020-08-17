<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\FunnelPriority;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\FunnelPriority;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\Response;

class IndexController
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
     * IndexController constructor.
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
     * @param Course $course
     *
     * @return Response
     */
    public function handleAction(Course $course)
    {
        $this->courseAccessService->resolveViewAccess($course);

        $coursePriorities = $this->entityManager->getRepository(FunnelPriority::class)->findBy(['course' => $course], ['priority' => 'ASC']);

        $content = $this->twig->render('@HBAdmin/Course/funnel_priorities.html.twig', [
            'course'     => $course,
            'priorities' => $coursePriorities,
        ]);

        return new Response($content);
    }
}