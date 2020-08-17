<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\PostSale;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelPostSale;
use HB\AdminBundle\Form\SaleFunnel\PostSale\PostSaleType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class EditController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RouterInterface
     */
    private $router;

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
    private $access;

    /**
     * EditController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param FormFactoryInterface   $formFactory
     * @param FormHandler            $formHandler
     * @param \Twig_Environment      $twig
     * @param CourseAccessService    $access
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, FormFactoryInterface $formFactory, FormHandler $formHandler, \Twig_Environment $twig, CourseAccessService $access)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
        $this->twig = $twig;
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
        $funnel = $course->getSalesFunnelPostSale();

        $this->access->resolveUpdateAccess($course);
        $this->access->registerEvent(IntercomEvents::ACCESS_FUNNEL_POST_SALE);

        if (!$funnel) {
            $funnel = new SalesFunnelPostSale($course);

            $this->access->resolveCreateAccess($course);

            $course->setSalesFunnelPostSale($funnel);
        }

        $form = $this->formFactory->create(PostSaleType::class, $funnel, [
            'method' => 'POST',
            'action' => $this->router->generate('hb.sale_funnel.post_sale.edit', ['id' => $course->getId()]),
        ]);

        if ($this->formHandler->handle($funnel, $request, $form)) {

            if (!$funnel->isActivateAfterDays()) {
                $course->setSalesFunnelPostSale(null);
                $this->entityManager->remove($funnel);
                $this->entityManager->flush();
            }

            return new RedirectResponse($this->router->generate('hb.course.edit', ['id' => $course->getId()]));
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/PostSale/form.html.twig', [
            'form'   => $form->createView(),
            'funnel' => $funnel,
        ]);

        return new Response($content);
    }
}
