<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\Partners;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelPartner;
use HB\AdminBundle\Form\SaleFunnel\Partner\PartnerType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ListController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var CourseAccessService
     */
    private $accessService;

    /**
     * ListController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     * @param FormFactoryInterface   $formFactory
     * @param RouterInterface        $router
     * @param FormHandler            $formHandler
     * @param FlashBagInterface      $flashBag
     * @param CourseAccessService    $accessService
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, FormFactoryInterface $formFactory, RouterInterface $router, FormHandler $formHandler, FlashBagInterface $flashBag, CourseAccessService $accessService)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->formHandler = $formHandler;
        $this->flashBag = $flashBag;
        $this->accessService = $accessService;
    }


    /**
     * @param Course  $course
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Course $course, Request $request)
    {
        $this->accessService->resolveViewAccess($course);
        $this->accessService->registerEvent(IntercomEvents::ACCESS_FUNNEL_PARTNER);

        $partners = $this->entityManager->getRepository(SalesFunnelPartner::class)->findBy(['course' => $course->getId()]);

        $funnel = new SalesFunnelPartner($course);
        $form = $this->formFactory->create(PartnerType::class, $funnel, [
            'action' => $this->router->generate('hb.sale_funnel.partner.list', ['id' => $course->getId()]),
        ]);

        if ($this->formHandler->handle($funnel, $request, $form)) {
            $this->flashBag->add('success', 'Партнер добавлен');

            return new RedirectResponse($this->router->generate('hb.sale_funnel.partner.list', ['id' => $course->getId()]));
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/Partner/list.html.twig', [
            'course'   => $course,
            'form'     => $form->createView(),
            'partners' => $partners,
        ]);

        return new Response($content);
    }
}