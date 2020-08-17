<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block10AdditionalBlock;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\AdditionalBlock;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Form\SaleFunnel\AdditionalBlockType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Block10AdditionalBlocksController
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * Block10AdditionalBlocksController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param FormHandler            $formHandler
     * @param FormFactoryInterface   $formFactory
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(\Twig_Environment $twig, FormHandler $formHandler, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, CourseAccessService $courseAccessService)
    {
        $this->twig = $twig;
        $this->formHandler = $formHandler;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
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

        $added = false;
        $additionalBlock = new AdditionalBlock();
        $form = $this->formFactory->create(AdditionalBlockType::class, $additionalBlock);

        if ($this->formHandler->handle($additionalBlock, $request, $form)) {

            $funnel->addBlock10AdditionalBlock($additionalBlock);
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();
            $added = true;

            $additionalBlock = new AdditionalBlock();
            $form = $this->formFactory->create(AdditionalBlockType::class, $additionalBlock);
        }

        $content = $this->twig->render("@HBAdmin/SaleFunnel/Organic/blocks/block10.html.twig", [
            'form' => $form->createView(),
            'added' => $added,
            'funnel' => $funnel
        ]);

        return new Response($content);
    }
}