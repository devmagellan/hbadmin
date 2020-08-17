<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer\Blocks;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use HB\AdminBundle\Form\SaleFunnel\OneTimeOffer\DescriptionType;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class Block3DescriptionController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * Block3DescriptionController constructor.
     *
     * @param \Twig_Environment    $twig
     * @param FormHandler          $formHandler
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface      $router
     */
    public function __construct(\Twig_Environment $twig, FormHandler $formHandler, FormFactoryInterface $formFactory, RouterInterface $router)
    {
        $this->twig = $twig;
        $this->formHandler = $formHandler;
        $this->formFactory = $formFactory;
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
        $form = $this->formFactory->create(DescriptionType::class, $funnel, [
            'action' => $this->router->generate('hb.sale_funnel.one_time_offer.blocks.block3_description', ['id' => $funnel->getId()]),
        ]);

        $saved = false;

        if ($this->formHandler->handle($funnel, $request, $form)) {
            $saved = true;
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/OneTimeOffer/blocks/block3description.html.twig', [
            'funnel' => $funnel,
            'form'   => $form->createView(),
            'saved'  => $saved,
        ]);

        return new Response($content);
    }
}