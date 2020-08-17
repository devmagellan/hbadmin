<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\CrossSale;


use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\CrossSale\DiscountCourse;
use HB\AdminBundle\Form\SaleFunnel\CrossSale\CrossSaleType;
use HB\AdminBundle\Form\SaleFunnel\CrossSale\DiscountCourseType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class EditController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * EditController constructor.
     *
     * @param \Twig_Environment    $twig
     * @param FormFactoryInterface $formFactory
     * @param FormHandler          $formHandler
     * @param FlashBagInterface    $flashBag
     * @param RouterInterface      $router
     * @param CourseAccessService  $access
     */
    public function __construct(\Twig_Environment $twig, FormFactoryInterface $formFactory, FormHandler $formHandler, FlashBagInterface $flashBag, RouterInterface $router, CourseAccessService $access)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
        $this->flashBag = $flashBag;
        $this->router = $router;
        $this->access = $access;
    }


    /**
     * @param Course  $course
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Course $course, Request $request)
    {
        $funnel = $course->getSalesFunnelCrossSale();
        $this->access->resolveUpdateAccess($funnel);
        $this->access->registerEvent(IntercomEvents::ACCESS_FUNNEL_CROSS_SALE);

        $course = $funnel->getCourse();
        $discountCourse = new DiscountCourse($funnel);

        $form = $this->formFactory->create(DiscountCourseType::class, $discountCourse, [
            'course' => $course,
            'method' => 'POST',
            'action' => $this->router->generate('hb.sale_funnel.cross_sale.edit', ['id' => $course->getId()]),
        ]);

        $formFunnel = $this->formFactory->create(CrossSaleType::class, $funnel, [
            'method' => 'POST',
            'action' => $this->router->generate('hb.sale_funnel.cross_sale.edit', ['id' => $course->getId()]),
        ]);

        if ($this->formHandler->handle($funnel, $request, $formFunnel)) {
            $this->flashBag->add('success', 'Сохранено');
        }

        if ($request->isMethod(Request::METHOD_POST)) {

            if ($funnel->getDiscountCourses()->count() === 0) {
                if ($this->formHandler->handle($discountCourse, $request, $form)) {
                    $this->flashBag->add('success', 'Сохранено');
                    return new RedirectResponse($this->router->generate('hb.sale_funnel.cross_sale.edit', ['id' => $course->getId()]));
                }
            }
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/CrossSale/edit.html.twig', [
            'form'       => $form->createView(),
            'formFunnel' => $formFunnel->createView(),
            'funnel'     => $funnel,
        ]);

        return new Response($content);
    }
}