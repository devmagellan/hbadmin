<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block11PaymentTexts;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\Organic\PaymentText;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Form\SaleFunnel\Organic\Block11PaymentTextType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Block11PaymentTextController
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
     * Block11PaymentTextController constructor.
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

        $paymentText = new PaymentText();
        $form = $this->formFactory->create(Block11PaymentTextType::class, $paymentText);

        $added = false;

        if ($this->formHandler->handle($paymentText, $request, $form)) {
            $funnel->addBlock11PaymentText($paymentText);
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();

            $added = true;
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Organic/blocks/block11.html.twig', [
            'form'   => $form->createView(),
            'funnel' => $funnel,
            'added'  => $added,
        ]);

        return new Response($content);
    }
}