<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Webinar\Blocks\Block3Bonuses;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;
use HB\AdminBundle\Entity\SaleFunnel\Webinar\WebinarBonus;
use HB\AdminBundle\Form\SaleFunnel\Webinar\Block3BonusType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Block3BonusController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

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
     * Block3BonusController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface   $formFactory
     * @param FormHandler            $formHandler
     * @param \Twig_Environment      $twig
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, FormHandler $formHandler, \Twig_Environment $twig, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
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

        $bonus = new WebinarBonus();
        $form = $this->formFactory->create(Block3BonusType::class, $bonus);
        $added = false;

        if ($this->formHandler->handle($bonus, $request, $form)) {
            $funnel->addBonus($bonus);
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();

            $added = true;
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Webinar/blocks/block3bonuses.html.twig', [
            'added' => $added,
            'form' => $form->createView(),
            'funnel' => $funnel
        ]);

        return new Response($content);
    }
}