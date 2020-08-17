<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\LayerCake;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLayerCake;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\Response;

class LessonViewController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * LessonViewController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $access
     */
    public function __construct(\Twig_Environment $twig, EntityManagerInterface $entityManager, CourseAccessService $access)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->access = $access;
    }


    /**
     * @param LessonSection        $section
     * @param SalesFunnelLayerCake $funnel
     *
     * @return Response
     */
    public function handleAction(LessonSection $section, SalesFunnelLayerCake $funnel)
    {
        $this->access->resolveViewAccess($funnel);

        $lessons = $this->entityManager->getRepository(Lesson::class)->findBy(['section' => $section], ['priority' => 'ASC']);

        $content = $this->twig->render('@HBAdmin/SaleFunnel/LayerCake/lesson_view.html.twig', [
            'funnel'  => $funnel,
            'lessons' => $lessons,
        ]);

        return new Response($content);

    }
}