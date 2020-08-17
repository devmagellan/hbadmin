<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar;


use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\Response;

class PreviewController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var CourseAccessService
     */
    private $accessService;

    /**
     * PreviewController constructor.
     *
     * @param \Twig_Environment   $twig
     * @param CourseAccessService $accessService
     */
    public function __construct (\Twig_Environment $twig, CourseAccessService $accessService)
    {
        $this->twig = $twig;
        $this->accessService = $accessService;
    }

    /**
     * @param SalesFunnelWebinar $funnel
     *
     * @return Response
     */
    public function handleAction (SalesFunnelWebinar $funnel)
    {
        $this->accessService->resolveViewAccess($funnel);
        $timeLast = $funnel->getTime()->diff(new \DateTime());

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Webinar/preview.html.twig', [
            'funnel'   => $funnel,
            'timeLast' => $timeLast,
        ]);

        return new Response($content);
    }
}