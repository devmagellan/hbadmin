<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Finance\Report\Admin;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\CustomerPaymentReport;
use HB\AdminBundle\Form\Finance\ReportType;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\Intercom\IntercomEventRecorder;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
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
class ReportListController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var IntercomEventRecorder
     */
    private $eventRecorder;

    /**
     * ReportListController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface   $formFactory
     * @param FlashBagInterface      $flashBag
     * @param RouterInterface        $router
     * @param FormHandler            $formHandler
     * @param IntercomEventRecorder  $eventRecorder
     */
    public function __construct(\Twig_Environment $twig, EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, FlashBagInterface $flashBag, RouterInterface $router, FormHandler $formHandler, IntercomEventRecorder $eventRecorder)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->formHandler = $formHandler;
        $this->eventRecorder = $eventRecorder;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Request $request)
    {
        $this->eventRecorder->registerEvent(IntercomEvents::ACCESS_REPORTS);

        $report = new CustomerPaymentReport();
        $form = $this->formFactory->create(ReportType::class, $report, [
            'method' => Request::METHOD_POST,
            'action' => $this->router->generate('hb.finance.admin.reports'),
        ]);

        if ($this->formHandler->handle($report, $request, $form)) {
            $this->flashBag->add('success', 'Отчет добавлен. Загрузите файл отчета');

            return new RedirectResponse($this->router->generate('hb.finance.admin.reports'));
        }

        $reports = $this->entityManager->getRepository(CustomerPaymentReport::class)->findBy([], ['reportDate' => 'DESC']);

        $content = $this->twig->render('@HBAdmin/Finance/Payment/Admin/report_list.html.twig', [
            'reports' => $reports,
            'form'    => $form->createView(),
        ]);

        return new Response($content);
    }
}