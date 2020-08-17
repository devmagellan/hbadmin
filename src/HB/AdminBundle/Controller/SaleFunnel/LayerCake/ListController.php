<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\LayerCake;


use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelLayerCake;
use HB\AdminBundle\Form\SaleFunnel\LayerCake\LayerCakeType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use HB\AdminBundle\Validator\SaleFunnelLayerCakeValidator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ListController
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
     * @var CourseAccessService
     */
    private $access;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * ListController constructor.
     *
     * @param FormHandler          $formHandler
     * @param RouterInterface      $router
     * @param FormFactoryInterface $formFactory
     * @param \Twig_Environment    $twig
     * @param CourseAccessService  $access
     * @param FlashBagInterface  $flashBag
     */
    public function __construct(FormHandler $formHandler, RouterInterface $router, FormFactoryInterface $formFactory, \Twig_Environment $twig, CourseAccessService $access, FlashBagInterface $flashBag)
    {
        $this->formHandler = $formHandler;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->access = $access;
        $this->flashBag = $flashBag;
    }

    /**
     * @param Course  $course
     * @param Request $request
     */
    public function handleAction(Course $course, Request $request)
    {
        $layerCake = new SalesFunnelLayerCake($course);

        $this->access->resolveCreateAccess($layerCake);
        $this->access->registerEvent(IntercomEvents::ACCESS_FUNNEL_SLICED);

        $form = $this->formFactory->create(LayerCakeType::class, $layerCake, [
            'method' => Request::METHOD_POST,
            'action' => $this->router->generate('hb.sale_funnel.layer_cake.list', ['id' => $course->getId()]),
        ]);

        if ($this->formHandler->handle($layerCake, $request, $form)) {
            return new RedirectResponse($this->router->generate('hb.sale_funnel.layer_cake.edit', ['id' => $layerCake->getId()]));
        }

        $errors = [];
        foreach ($course->getSaleFunnelLayerCakes() as $funnel) {
            $errors = array_merge($errors, SaleFunnelLayerCakeValidator::validate($funnel));
        }

        foreach ($errors as $error) {
            $this->flashBag->add('error', $error);
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/LayerCake/list.html.twig', [
            'form'   => $form->createView(),
            'course' => $course,
        ]);

        return new Response($content);
    }
}