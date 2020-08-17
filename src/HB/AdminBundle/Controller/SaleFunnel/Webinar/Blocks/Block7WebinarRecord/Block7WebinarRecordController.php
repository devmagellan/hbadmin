<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block7WebinarRecord;


use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Form\SaleFunnel\Webinar\Block7WebinarPriceType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Block7WebinarRecordController
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
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * Block7WebinarRecordController constructor.
     *
     * @param FormHandler          $formHandler
     * @param FormFactoryInterface $formFactory
     * @param \Twig_Environment    $twig
     * @param CourseAccessService  $courseAccessService
     */
    public function __construct(FormHandler $formHandler, FormFactoryInterface $formFactory, \Twig_Environment $twig, CourseAccessService $courseAccessService)
    {
        $this->formHandler = $formHandler;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param SalesFunnelWebinar $funnel
     * @param Request            $request
     *
     * @return Response
     */
    public function handleAction(SalesFunnelWebinar $funnel, Request $request)
    {
        $this->courseAccessService->resolveCreateAccess($funnel);

        $saved = false;

        $formPrice = $this->formFactory->create(Block7WebinarPriceType::class, $funnel);

        if ($this->formHandler->handle($funnel, $request, $formPrice)) {
            $saved = true;
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Webinar/blocks/block7webinar_record.html.twig', [
            'formPrice' => $formPrice->createView(),
            'funnel' => $funnel,
            'saved' => $saved
        ]);

        return new Response($content);
    }
}