<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block6Author;


use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Form\SaleFunnel\Organic\Block6AuthorType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Block6AuthorController
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
     * Block6AuthorController constructor.
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

        $form = $this->formFactory->create(Block6AuthorType::class, $funnel);
        $saved = false;

        if ($this->formHandler->handle($funnel, $request, $form)) {
            $saved = true;
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Organic/blocks/block6.html.twig',[
            'saved' => $saved,
            'form' => $form->createView(),
            'funnel' => $funnel
        ]);

        return new Response($content);
    }
}