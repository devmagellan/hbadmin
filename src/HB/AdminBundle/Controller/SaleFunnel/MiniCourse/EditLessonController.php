<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\MiniCourse;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\SaleFunnel\MiniCourse\MiniLesson;
use HB\AdminBundle\Form\SaleFunnel\MiniCourse\MiniLessonEditType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class EditLessonController
{
    /**
     * @var RouterInterface
     */
    private $router;

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
    private $access;

    /**
     * EditLessonController constructor.
     *
     * @param RouterInterface        $router
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     * @param FormFactoryInterface   $formFactory
     * @param FormHandler            $formHandler
     * @param FlashBagInterface      $flashBag
     * @param CourseAccessService    $access
     */
    public function __construct(RouterInterface $router, EntityManagerInterface $entityManager, \Twig_Environment $twig, FormFactoryInterface $formFactory, FormHandler $formHandler, FlashBagInterface $flashBag, CourseAccessService $access)
    {
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->formHandler = $formHandler;
        $this->flashBag = $flashBag;
        $this->access = $access;
    }


    /**
     * @param MiniLesson $lesson
     * @param Request    $request
     *
     * @return Response
     */
    public function handleAction(MiniLesson $lesson, Request $request): Response
    {
        $funnel = $lesson->getFunnelMiniCourse();
        $this->access->resolveUpdateAccess($funnel);

        $form = $this->formFactory->create(MiniLessonEditType::class, $lesson, [
            'method' => "POST",
            'action' => $this->router->generate('hb.sale_funnel.mini_course.edit_lesson', ['id' => $lesson->getId()]),
        ]);

        if ($this->formHandler->handle($funnel, $request, $form)) {
            $this->flashBag->add('success', 'Сохранено');

            return new RedirectResponse($this->router->generate('hb.sale_funnel.mini_course.edit', ['id' => $funnel->getCourse()->getId()]));
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/MiniCourse/editLesson.html.twig', [
            'form' => $form->createView(),
            'funnel' => $funnel,
            'lesson' => $lesson
        ]);

        return new Response($content);
    }
}