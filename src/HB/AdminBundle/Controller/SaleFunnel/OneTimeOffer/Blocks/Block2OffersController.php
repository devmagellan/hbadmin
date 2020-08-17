<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer\Blocks;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\OneTimeOffer;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer;
use HB\AdminBundle\Form\SaleFunnel\OneTimeOffer\OneTimeOfferSingleType;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class Block2OffersController
{
    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * Block2OffersController constructor.
     *
     * @param FormHandler            $formHandler
     * @param FormFactoryInterface   $formFactory
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     * @param RouterInterface        $router
     */
    public function __construct(FormHandler $formHandler, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, \Twig_Environment $twig, RouterInterface $router)
    {
        $this->formHandler = $formHandler;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
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
        $offer = new OneTimeOffer($funnel);

        $form = $this->formFactory->create(OneTimeOfferSingleType::class, $offer, [
            'action' => $this->router->generate('hb.sale_funnel.one_time_offer.blocks.block2_offers', ['id' => $funnel->getId()]),
        ]);

        $added = false;

        if ($this->formHandler->handle($offer, $request, $form)) {
            $funnel->addOffer($offer);
            $added = true;
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/OneTimeOffer/blocks/block2offers.html.twig', [
            'funnel' => $funnel,
            'added'  => $added,
            'form'   => $form->createView(),
        ]);

        return new Response($content);
    }
}