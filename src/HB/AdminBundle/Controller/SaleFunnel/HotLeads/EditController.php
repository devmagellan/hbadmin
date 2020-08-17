<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\HotLeads;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\HotLeads\SuccessHistory;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelHotLeads;
use HB\AdminBundle\Form\SaleFunnel\HotLeads\HotLeadsType;
use HB\AdminBundle\Form\SaleFunnel\HotLeads\SuccessHistoryType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class EditController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * EditController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param FormFactoryInterface   $formFactory
     * @param FormHandler            $formHandler
     * @param \Twig_Environment      $twig
     * @param FlashBagInterface      $flashBag
     * @param CourseAccessService    $access
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, FormFactoryInterface $formFactory, FormHandler $formHandler, \Twig_Environment $twig, FlashBagInterface $flashBag, CourseAccessService $access)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->access = $access;
    }


    /**
     * @param SalesFunnelHotLeads $funnel
     * @param Request             $request
     *
     * @return Response
     */
    public function handleAction(SalesFunnelHotLeads $funnel, Request $request)
    {
        $this->access->resolveUpdateAccess($funnel);
        $this->access->registerEvent(IntercomEvents::ACCESS_FUNNEL_HOT_LEADS);

        $formFunnel = $this->formFactory->create(HotLeadsType::class, $funnel, [
            'method' => 'POST',
            'action' => $this->router->generate('hb.sale_funnel.hot_leads.edit', ['id' => $funnel->getId()]),
        ]);

        $successHistory = new SuccessHistory($funnel);
        $formHistory = $this->formFactory->create(SuccessHistoryType::class, $successHistory, [
            'method' => 'POST',
            'action' => $this->router->generate('hb.sale_funnel.hot_leads.edit', ['id' => $funnel->getId()]),
        ]);

        if ($this->formHandler->handle($funnel, $request, $formFunnel) ||
            $this->formHandler->handle($successHistory, $request, $formHistory)) {
            $this->flashBag->add('success', 'Сохранено');
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/HotLeads/edit.html.twig', [
            'formFunnel'  => $formFunnel->createView(),
            'formHistory' => $formHistory->createView(),
            'funnel'      => $funnel,
        ]);

        return new Response($content);
    }
}