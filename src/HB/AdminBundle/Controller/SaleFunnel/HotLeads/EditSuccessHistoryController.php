<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\HotLeads;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\HotLeads\SuccessHistory;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelHotLeads;
use HB\AdminBundle\Form\SaleFunnel\HotLeads\SuccessHistoryEditType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class EditSuccessHistoryController
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
     * EditSuccessHistoryController constructor.
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
     * @param SalesFunnelHotLeads $history
     * @param Request             $request
     *
     * @return Response
     */
    public function handleAction(SuccessHistory $history, Request $request)
    {
        $this->access->resolveUpdateAccess($history->getFunnelHotLeads());

        $formHistory = $this->formFactory->create(SuccessHistoryEditType::class, $history, [
            'method' => 'POST',
            'action' => $this->router->generate('hb.sale_funnel.hot_leads.edit_success_history', ['id' => $history->getId()]),
        ]);

        if ($this->formHandler->handle($history, $request, $formHistory)) {
            $this->flashBag->add('success', 'Сохранено');
            return new RedirectResponse($this->router->generate('hb.sale_funnel.hot_leads.edit', ['id' => $history->getFunnelHotLeads()->getId()]));
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/HotLeads/editSuccessHistory.html.twig', [
            'form' => $formHistory->createView(),
        ]);

        return new Response($content);
    }
}