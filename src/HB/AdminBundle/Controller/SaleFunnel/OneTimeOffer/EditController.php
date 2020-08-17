<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
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
    private $accessService;

    /**
     * EditController constructor.
     *
     * @param \Twig_Environment   $twig
     * @param CourseAccessService $accessService
     */
    public function __construct(\Twig_Environment $twig, CourseAccessService $accessService)
    {
        $this->twig = $twig;
        $this->accessService = $accessService;
    }

    /**
     * @param SalesFunnelOneTimeOffer $funnel
     *
     * @return Response
     */
    public function handleAction(SalesFunnelOneTimeOffer $funnel)
    {
        $this->accessService->resolveViewAccess($funnel);
        $this->accessService->registerEvent(IntercomEvents::ACCESS_FUNNEL_OTO);

        $content = $this->twig->render('@HBAdmin/SaleFunnel/OneTimeOffer/edit.html.twig', [
            'funnel' => $funnel,
        ]);

        return new Response($content);
    }
}