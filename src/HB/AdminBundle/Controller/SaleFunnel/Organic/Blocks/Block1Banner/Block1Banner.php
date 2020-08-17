<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Organic\Blocks\Block1Banner;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Form\SaleFunnel\Organic\Block1BannerType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Block1Banner
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
    private $courseAccess;

    /**
     * Block1Banner constructor.
     *
     * @param \Twig_Environment      $twig
     * @param FormHandler            $formHandler
     * @param FormFactoryInterface   $formFactory
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccess
     */
    public function __construct(\Twig_Environment $twig, FormHandler $formHandler, FormFactoryInterface $formFactory, EntityManagerInterface $entityManager, CourseAccessService $courseAccess)
    {
        $this->twig = $twig;
        $this->formHandler = $formHandler;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->courseAccess = $courseAccess;
    }

    /**
     * @param SalesFunnelOrganic $funnel
     * @param Request            $request
     *
     * @return Response
     */
    public function handleAction(SalesFunnelOrganic $funnel, Request $request)
    {
        $this->courseAccess->resolveCreateAccess($funnel);

        $saved = false;

        $form = $this->formFactory->create(Block1BannerType::class, $funnel);

        if ($this->formHandler->handle($funnel, $request, $form)) {
            $saved = true;
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Organic/blocks/block1.html.twig',[
            'form' => $form->createView(),
            'funnel' => $funnel,
            'saved' => $saved
        ]);

        return new Response($content);
    }
}
