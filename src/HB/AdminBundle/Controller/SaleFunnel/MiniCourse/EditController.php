<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\SaleFunnel\MiniCourse;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\MiniCourse\MiniLesson;
use HB\AdminBundle\Form\SaleFunnel\MiniCourse\MiniCourseType;
use HB\AdminBundle\Form\SaleFunnel\MiniCourse\MiniLessonType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class EditController
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
     * EditController constructor.
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
     * @param Course  $course
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Course $course, Request $request): Response
    {
        $funnel = $course->getSalesFunnelMiniCourse();

        $this->access->resolveCreateAccess($funnel);

        $lesson = new MiniLesson($funnel);

        $form = $this->formFactory->create(MiniCourseType::class, $funnel, [
            'method' => "POST",
            'action' => $this->router->generate('hb.sale_funnel.mini_course.edit', ['id' => $course->getId()]),
        ]);

        $formLesson = $this->formFactory->create(MiniLessonType::class, $lesson, [
            'method' => "POST",
            'action' => $this->router->generate('hb.sale_funnel.mini_course.edit', ['id' => $course->getId()]),
        ]);

        if ($this->formHandler->handle($funnel, $request, $form)) {
            $this->flashBag->add('success', 'Сохранено');
        }

        if ($this->formHandler->handle($lesson, $request, $formLesson)) {
            $funnel->addLesson($lesson);
            $this->entityManager->persist($funnel);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'Добавлено');
            return new RedirectResponse($this->router->generate('hb.sale_funnel.mini_course.edit_lesson', ['id' => $lesson->getId()]));
        }

        $content = $this->twig->render('@HBAdmin/SaleFunnel/MiniCourse/edit.html.twig', [
            'form'       => $form->createView(),
            'formLesson' => $formLesson->createView(),
            'funnel'     => $funnel,
        ]);

        return new Response($content);
    }
}