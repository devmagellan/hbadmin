<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Controller\Course;

use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Form\StopLessonsType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class StopLessonsController
{
    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * StopLessonsController constructor.
     *
     * @param CourseAccessService  $courseAccessService
     * @param FormHandler          $formHandler
     * @param \Twig_Environment    $twig
     * @param RouterInterface      $router
     * @param FormFactoryInterface $formFactory
     * @param FlashBagInterface    $flashBag
     */
    public function __construct(CourseAccessService $courseAccessService, FormHandler $formHandler, \Twig_Environment $twig, RouterInterface $router, FormFactoryInterface $formFactory, FlashBagInterface $flashBag)
    {
        $this->courseAccessService = $courseAccessService;
        $this->formHandler = $formHandler;
        $this->twig = $twig;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
    }


    /**
     * @param Course  $course
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Course $course, Request $request)
    {
        $this->courseAccessService->resolveViewAccess($course);

        $form = $this->formFactory->create(StopLessonsType::class, $course->getStopLessons(), [
            'method' => 'POST',
            'action' => $this->router->generate('hb.course.stop_lessons', ['id' => $course->getId()])
        ]);

        if ($this->courseAccessService->resolveUpdateAccess($course) && $this->formHandler->handle($course, $request, $form)) {
            $this->flashBag->add('success', 'Данные успешно сохранены');

            return new RedirectResponse($this->router->generate('hb.course.edit', ['id' => $course->getId()]));
        }


        $content = $this->twig->render('@HBAdmin/Course/stop_lessons.html.twig', [
            'form' => $form->createView(),
            'course' => $course
        ]);

        return new Response($content);
    }
}