<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\LeadCollection;


use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLeadCollection;
use Symfony\Component\HttpFoundation\Response;

class PreviewController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * PreviewController constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct (\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param SalesFunnelLeadCollection $funnel
     *
     * @return Response
     */
    public function handleAction(SalesFunnelLeadCollection $funnel)
    {
        $content = $this->twig->render('@HBAdmin/SaleFunnel/LeadCollection/preview.html.twig', [
            'funnel' => $funnel
        ]);

        return new Response($content);
    }
}