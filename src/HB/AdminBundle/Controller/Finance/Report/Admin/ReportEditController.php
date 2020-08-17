<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance\Report\Admin;


use HB\AdminBundle\Entity\CustomerPaymentReport;
use HB\AdminBundle\Form\Finance\ReportType;
use HB\AdminBundle\Service\FormHandler;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class ReportEditController
{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * ReportEditController constructor.
     *
     * @param \Twig_Environment    $twig
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface      $router
     * @param FormHandler          $formHandler
     * @param FlashBagInterface    $flashBag
     */
    public function __construct(\Twig_Environment $twig, FormFactoryInterface $formFactory, RouterInterface $router, FormHandler $formHandler, FlashBagInterface $flashBag)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->formHandler = $formHandler;
        $this->flashBag = $flashBag;
    }

    /**
     * @param CustomerPaymentReport $report
     * @param Request               $request
     *
     * @return Response
     */
    public function handleAction(CustomerPaymentReport $report, Request $request)
    {
        $form = $this->formFactory->create(ReportType::class, $report, [
            'action' => $this->router->generate('hb.finance.admin.report.edit', ['id' => $report->getId()]),
            'method' => Request::METHOD_POST,
        ]);

        if ($this->formHandler->handle($report, $request, $form)) {
            $this->flashBag->add('success', 'Отчет сохранен');

            return new RedirectResponse($this->router->generate('hb.finance.admin.reports'));
        }

        $content = $this->twig->render('@HBAdmin/Finance/Payment/Admin/report_edit.html.twig', [
            'form' => $form->createView(),
        ]);

        return new Response($content);
    }
}