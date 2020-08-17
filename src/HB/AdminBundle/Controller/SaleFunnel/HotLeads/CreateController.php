<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\HotLeads;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelHotLeads;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class CreateController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * CreateController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param CourseAccessService    $access
     * @param \Twig_Environment    $twig
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, CourseAccessService $access, \Twig_Environment $twig)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->access = $access;
        $this->twig = $twig;
    }


    /**
     * @param Course  $course
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Course $course, Request $request)
    {
        $funnel = new SalesFunnelHotLeads($course);

        if ($request->isMethod('POST')) {
            $this->access->resolveCreateAccess($funnel);
            $course->setSalesFunnelHotLeads($funnel);

            $this->entityManager->persist($funnel);
            $this->entityManager->persist($course);
            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('hb.sale_funnel.hot_leads.edit', ['id' => $funnel->getId()]));
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/HotLeads/popup.html.twig', [
            'course' => $course
        ]);

        return new Response($content);
    }
}