<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block12PhotoSignature;


use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\Response;

class Block12PhotoSignatureController
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
     * Block12PhotoSignatureController constructor.
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
     * @param SalesFunnelOrganic $funnel
     *
     * @return Response
     */
    public function handleAction(SalesFunnelOrganic $funnel)
    {
        $this->courseAccessService->resolveViewAccess($funnel);

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Organic/blocks/block12.html.twig', [
            'funnel' => $funnel,
        ]);

        return new Response($content);
    }
}