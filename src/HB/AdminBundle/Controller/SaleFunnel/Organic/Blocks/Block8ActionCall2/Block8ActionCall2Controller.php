<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block8ActionCall2;


use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Form\SaleFunnel\Organic\Block8ActionCall2Type;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Block8ActionCall2Controller
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
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * Block8ActionCall2Controller constructor.
     *
     * @param \Twig_Environment    $twig
     * @param FormHandler          $formHandler
     * @param FormFactoryInterface $formFactory
     * @param CourseAccessService  $courseAccessService
     */
    public function __construct(\Twig_Environment $twig, FormHandler $formHandler, FormFactoryInterface $formFactory, CourseAccessService $courseAccessService)
    {
        $this->twig = $twig;
        $this->formHandler = $formHandler;
        $this->formFactory = $formFactory;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param SalesFunnelOrganic $funnel
     * @param Request            $request
     *
     * @return Response
     */
    public function handleAction(SalesFunnelOrganic $funnel, Request $request)
    {
        $this->courseAccessService->resolveCreateAccess($funnel);

        $form = $this->formFactory->create(Block8ActionCall2Type::class, $funnel);
        $saved = false;

        if ($this->formHandler->handle($funnel, $request, $form)) {
            $saved = true;
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Organic/blocks/block8.html.twig', [
            'saved' => $saved,
            'funnel' => $funnel,
            'form' => $form->createView()
        ]);

        return new Response($content);
    }
}
