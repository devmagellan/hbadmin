<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use HB\AdminBundle\Validator\SaleFunnelOneTimeOfferValidator;
use Symfony\Component\HttpFoundation\Response;

class FunnelErrorsController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * FunnelErrorsController constructor.
     *
     * @param \Twig_Environment $twig
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param SalesFunnelOneTimeOffer $funnel
     *
     * @return Response
     */
    public function handleAction(SalesFunnelOneTimeOffer $funnel)
    {
        $errors = SaleFunnelOneTimeOfferValidator::validate($funnel);

        return new Response($this->twig->render('@HBAdmin/SaleFunnel/OneTimeOffer/errors.html.twig', [
            'errors' => $errors
        ]));
    }

}