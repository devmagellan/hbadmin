<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block3Knowledge;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\Organic\KnowledgeSkills;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Form\SaleFunnel\Organic\Block3KnowledgeAddType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Block3Knowledge
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
     * Block3Knowledge constructor.
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

        $skill = new KnowledgeSkills();
        $form = $this->formFactory->create(Block3KnowledgeAddType::class, $skill);

        $added = false;

        if ($this->formHandler->handle($skill, $request, $form)) {
            $funnel->addBlock3KnowledgeSkill($skill);
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();

            $skill = new KnowledgeSkills();
            $form = $this->formFactory->create(Block3KnowledgeAddType::class, $skill);

            $added = true;
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Organic/blocks/block3.html.twig', [
            'form'   => $form->createView(),
            'funnel' => $funnel,
            'added'  => $added,
        ]);

        return new Response($content);
    }
}