<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer\Blocks;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use HB\AdminBundle\Form\SaleFunnel\OneTimeOffer\LinkType;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class Block4OfferLinkController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * Block4OfferLinkController constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param \Twig_Environment    $twig
     * @param FormHandler          $formHandler
     * @param RouterInterface      $router
     */
    public function __construct(FormFactoryInterface $formFactory, \Twig_Environment $twig, FormHandler $formHandler, RouterInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->formHandler = $formHandler;
        $this->router = $router;
    }


    /**
     * @param SalesFunnelOneTimeOffer $funnel
     * @param Request                 $request
     *
     * @return Response
     */
    public function handleAction(SalesFunnelOneTimeOffer $funnel, Request $request)
    {
        $form = $this->formFactory->create(LinkType::class, $funnel, [
            'action' => $this->router->generate('hb.sale_funnel.one_time_offer.blocks.block4_offer_link', ['id' => $funnel->getId()]),
        ]);

        $saved = false;

        if ($this->formHandler->handle($funnel, $request, $form)) {
            $saved = true;
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/OneTimeOffer/blocks/block4offer_link.html.twig', [
            'form'  => $form->createView(),
            'saved' => $saved,
            'funnel' => $funnel
        ]);

        return new Response($content);
    }
}