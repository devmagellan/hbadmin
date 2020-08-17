<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar;


use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\Response;

class EditController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * EditController constructor.
     *
     * @param \Twig_Environment   $twig
     * @param CourseAccessService $courseAccessService
     */
    public function __construct(\Twig_Environment $twig, CourseAccessService $courseAccessService)
    {
        $this->twig = $twig;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param SalesFunnelWebinar $funnel
     *
     * @return Response
     */
    public function handleAction(SalesFunnelWebinar $funnel)
    {
        $this->courseAccessService->resolveUpdateAccess($funnel);

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Webinar/edit.html.twig', [
            'funnel' => $funnel,
        ]);

        return new Response($content);
    }
}