<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Course;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Form\CourseType;
use HB\AdminBundle\Service\CustomerAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class CreateController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CustomerAccessService
     */
    private $customerAccessService;

    /**
     * CreateController constructor.
     *
     * @param FormFactoryInterface   $formFactory
     * @param \Twig_Environment      $twig
     * @param RouterInterface        $router
     * @param FormHandler            $formHandler
     * @param EntityManagerInterface $entityManager
     * @param CustomerAccessService  $customerAccessService
     */
    public function __construct(FormFactoryInterface $formFactory, \Twig_Environment $twig, RouterInterface $router, FormHandler $formHandler, EntityManagerInterface $entityManager, CustomerAccessService $customerAccessService)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->router = $router;
        $this->formHandler = $formHandler;
        $this->entityManager = $entityManager;
        $this->customerAccessService = $customerAccessService;
    }


    /**
     * @param Request $request
     * @param string  $type
     *
     * @return Response
     */
    public function handleAction(Request $request): Response
    {
        $course = new Course();
        $funnelOrganic = new SalesFunnelOrganic($course);
        $course->setSalesFunnelOrganic($funnelOrganic);
        $form = $this->formFactory->create(CourseType::class, $course, [
            'authors' => $this->customerAccessService->getAvailableAuthorsForCourseCreate()
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {
            if ($this->formHandler->handle($course, $request, $form)) {
                return new RedirectResponse($this->router->generate('hb.sale_funnel.organic.price_blocks', ['id' => $funnelOrganic->getId()]));
            }
        }

        $content = $this->twig->render("@HBAdmin/Course/create.html.twig", [
            'form'   => $form->createView(),
            'course' => $course,
        ]);

        return new Response($content);
    }
}