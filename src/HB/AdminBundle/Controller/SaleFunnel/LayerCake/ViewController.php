<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\LayerCake;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLayerCake;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\Response;

class ViewController
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
     * ViewController constructor.
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
     * @param SalesFunnelLayerCake $funnel
     *
     * @return Response
     */
    public function handleAction(SalesFunnelLayerCake $funnel)
    {
        $this->access->resolveViewAccess($funnel);

        $sections = $this->entityManager->getRepository(LessonSection::class)->findBy(['course' => $funnel->getCourse()], ['priority' => 'ASC']);

        $content = $this->twig->render('@HBAdmin/SaleFunnel/LayerCake/view.html.twig', [
            'sections' => $sections,
            'funnel' => $funnel
        ]);

        return new Response($content);

    }
}