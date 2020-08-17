<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer;


use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
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
     * @param SalesFunnelOneTimeOffer $funnel
     */
    public function handleAction(SalesFunnelOneTimeOffer $funnel)
    {
        $content = $this->twig->render('@HBAdmin/SaleFunnel/OneTimeOffer/preview.html.twig', [
            'funnel' => $funnel
        ]);

        return new Response($content);
    }
}