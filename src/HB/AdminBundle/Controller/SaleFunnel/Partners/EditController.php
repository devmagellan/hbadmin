<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Partners;


use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelPartner;
use HB\AdminBundle\Form\SaleFunnel\Partner\PartnerType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class EditController
{
    /**
     * @var CourseAccessService
     */
    private $accessService;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * EditController constructor.
     *
     * @param CourseAccessService  $accessService
     * @param RouterInterface      $router
     * @param FormHandler          $formHandler
     * @param FormFactoryInterface $formFactory
     * @param FlashBagInterface    $flashBag
     * @param \Twig_Environment    $twig
     */
    public function __construct(CourseAccessService $accessService, RouterInterface $router, FormHandler $formHandler, FormFactoryInterface $formFactory, FlashBagInterface $flashBag, \Twig_Environment $twig)
    {
        $this->accessService = $accessService;
        $this->router = $router;
        $this->formHandler = $formHandler;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
    }

    /**
     * @param SalesFunnelPartner $funnel
     * @param Request            $request
     *
     * @return RedirectResponse|Response
     */
    public function handleAction(SalesFunnelPartner $funnel, Request $request)
    {
        $this->accessService->resolveUpdateAccess($funnel);

        $form = $this->formFactory->create(PartnerType::class, $funnel);

        if ($this->formHandler->handle($funnel, $request, $form)) {
            $this->flashBag->add('success', 'Изменения сохранены');

            return new RedirectResponse($this->router->generate('hb.sale_funnel.partner.list', ['id' => $funnel->getCourse()->getId()]));
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Partner/edit.html.twig', [
            'form'   => $form->createView(),
            'funnel' => $funnel,
            'course' => $funnel->getCourse(),
        ]);

        return new Response($content);
    }
}