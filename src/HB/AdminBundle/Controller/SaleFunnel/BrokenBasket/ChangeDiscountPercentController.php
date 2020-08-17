<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\BrokenBasket;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelBrokenBasket;
use HB\AdminBundle\Form\SaleFunnel\BrokenBasket\BrokenBasketType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class ChangeDiscountPercentController
{
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
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var CourseAccessService
     */
    private $access;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * ChangeDiscountPercentController constructor.
     *
     * @param RouterInterface        $router
     * @param FormFactoryInterface   $formFactory
     * @param \Twig_Environment      $twig
     * @param FormHandler            $formHandler
     * @param CourseAccessService    $access
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RouterInterface $router, FormFactoryInterface $formFactory, \Twig_Environment $twig, FormHandler $formHandler, CourseAccessService $access, EntityManagerInterface $entityManager)
    {
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->formHandler = $formHandler;
        $this->access = $access;
        $this->entityManager = $entityManager;
    }


    /**
     * @param Course  $course
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Course $course, Request $request)
    {
        $this->access->resolveViewAccess($course);

        $this->access->registerEvent(IntercomEvents::ACCESS_FUNNEL_CART_SALE);

        $activate = (bool) $request->get('activate');

        $funnel = $course->getSalesFunnelBrokenBasket() ?: new SalesFunnelBrokenBasket($course);

        $form = $this->formFactory->create(BrokenBasketType::class, $funnel, [
            'method' => Request::METHOD_POST,
            'action' => $this->router->generate('hb.sale_funnel.broken_basket.change_discount_percent', ['id' => $course->getId()]),
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {
            if ($activate) {
                $this->access->resolveUpdateAccess($course);
                $course->setSalesFunnelBrokenBasket($funnel);

                $this->formHandler->handle($funnel, $request, $form);
            } else {
                $this->access->resolveDeleteAccess($course);

                $course->setSalesFunnelBrokenBasket(null);
                $this->entityManager->persist($course);
                $this->entityManager->remove($funnel);
            }

            $this->entityManager->flush();

            return new RedirectResponse($this->router->generate('hb.course.edit', ['id' => $course->getId()]));
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/BrokenBasket/form.html.twig', [
            'form'   => $form->createView(),
            'course' => $course,
        ]);

        return new Response($content);
    }
}
