<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\LayerCake;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLayerCake;
use HB\AdminBundle\Form\SaleFunnel\LayerCake\LayerCakeType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class EditController
{
    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * EditController constructor.
     *
     * @param FormHandler            $formHandler
     * @param RouterInterface        $router
     * @param FormFactoryInterface   $formFactory
     * @param \Twig_Environment      $twig
     * @param FlashBagInterface      $flashBag
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $access
     */
    public function __construct(FormHandler $formHandler, RouterInterface $router, FormFactoryInterface $formFactory, \Twig_Environment $twig, FlashBagInterface $flashBag, EntityManagerInterface $entityManager, CourseAccessService $access)
    {
        $this->formHandler = $formHandler;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->flashBag = $flashBag;
        $this->entityManager = $entityManager;
        $this->access = $access;
    }


    /**
     * @param SalesFunnelLayerCake $layerCake
     * @param Request              $request
     */
    public function handleAction(SalesFunnelLayerCake $layerCake, Request $request)
    {
        $this->access->resolveUpdateAccess($layerCake);

        $form = $this->formFactory->create(LayerCakeType::class, $layerCake, [
            'method' => Request::METHOD_POST,
            'action' => $this->router->generate('hb.sale_funnel.layer_cake.edit', ['id' => $layerCake->getId()]),
        ]);

        if ($this->formHandler->handle($layerCake, $request, $form)) {
            $this->flashBag->add('success', 'Сохранено');
        }

        $sections = $this->entityManager->getRepository(LessonSection::class)->findBy(['course' => $layerCake->getCourse()], ['priority' => 'ASC']);

        $content = $this->twig->render('@HBAdmin/SaleFunnel/LayerCake/edit.html.twig', [
            'form' => $form->createView(),
            'funnel' => $layerCake,
            'sections' => $sections
        ]);

        return new Response($content);
    }
}