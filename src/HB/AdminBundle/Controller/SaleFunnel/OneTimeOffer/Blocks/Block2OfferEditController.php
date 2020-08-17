<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Controller\SaleFunnel\OneTimeOffer\Blocks;

use HB\AdminBundle\Entity\SaleFunnel\OneTimeOffer;
use HB\AdminBundle\Form\SaleFunnel\OneTimeOffer\OneTimeOfferSingleType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class Block2OfferEditController
{
    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var CourseAccessService
     */
    private $accessService;

    /**
     * Block2OfferEditController constructor.
     *
     * @param FormHandler          $formHandler
     * @param RouterInterface      $router
     * @param \Twig_Environment    $twig
     * @param FormFactoryInterface $formFactory
     * @param CourseAccessService  $accessService
     */
    public function __construct(FormHandler $formHandler, RouterInterface $router, \Twig_Environment $twig, FormFactoryInterface $formFactory, CourseAccessService $accessService)
    {
        $this->formHandler = $formHandler;
        $this->router = $router;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->accessService = $accessService;
    }

    /**
     * @param OneTimeOffer $offer
     * @param Request      $request
     *
     * @return Response
     */
    public function handleAction(OneTimeOffer $offer, Request $request)
    {
        $this->accessService->resolveUpdateAccess($offer->getFunnel());

        $form = $this->formFactory->create(OneTimeOfferSingleType::class, $offer, [
            'action' => $this->router->generate('hb.sale_funnel.one_time_offer.blocks.block2_offers.edit_offer', ['id' => $offer->getId()]),
        ]);

        if ($this->formHandler->handle($offer, $request, $form)) {
            return new RedirectResponse($this->router->generate('hb.sale_funnel.one_time_offer.edit', [
                'id'  => $offer->getFunnel()->getId(),
                'tab' => 'offers',
            ]));
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/OneTimeOffer/blocks/block2edit_offer.html.twig', [
            'offer' => $offer,
            'form' => $form->createView()
        ]);

        return new Response($content);
    }
}