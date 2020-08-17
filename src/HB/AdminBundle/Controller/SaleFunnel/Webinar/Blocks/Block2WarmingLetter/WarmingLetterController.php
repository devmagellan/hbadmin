<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block2WarmingLetter;


use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Form\SaleFunnel\Webinar\Block2HomeworkType;
use HB\AdminBundle\Form\SaleFunnel\Webinar\Block2WorkbookType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WarmingLetterController
{
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
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * WarmingLetterController constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param FormHandler          $formHandler
     * @param \Twig_Environment    $twig
     * @param CourseAccessService  $courseAccessService
     */
    public function __construct(FormFactoryInterface $formFactory, FormHandler $formHandler, \Twig_Environment $twig, CourseAccessService $courseAccessService)
    {
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
        $this->twig = $twig;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param SalesFunnelWebinar $funnel
     * @param Request            $request
     */
    public function handleAction(SalesFunnelWebinar $funnel, Request $request)
    {
        $this->courseAccessService->resolveCreateAccess($funnel);

        $formHomework = $this->formFactory->create(Block2HomeworkType::class, $funnel);
        $formWorkbook = $this->formFactory->create(Block2WorkbookType::class, $funnel);
        $saved = false;

        if ($this->formHandler->handle($funnel, $request, $formHomework) || $this->formHandler->handle($formWorkbook, $request, $formWorkbook)) {
            $saved = true;
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Webinar/blocks/block2warming_letter.html.twig', [
            'formHomework' => $formHomework->createView(),
            'formWorkbook' => $formWorkbook->createView(),
            'funnel' => $funnel,
            'saved' => $saved
        ]);

        return new Response($content);
    }

}