<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonSection;


use HB\AdminBundle\Entity\LessonSection;
use HB\AdminBundle\Form\LessonSectionType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class UpdateController
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
     * @var FormHandler
     */
    private $formHandler;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * UpdateController constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param \Twig_Environment    $twig
     * @param FormHandler          $formHandler
     * @param RouterInterface      $router
     * @param FlashBagInterface    $flashBag
     * @param CourseAccessService  $courseAccessService
     */
    public function __construct(FormFactoryInterface $formFactory, \Twig_Environment $twig, FormHandler $formHandler, RouterInterface $router, FlashBagInterface $flashBag, CourseAccessService $courseAccessService)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->formHandler = $formHandler;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->courseAccessService = $courseAccessService;
    }

    /**
     * @param LessonSection $section
     * @param Request       $request
     *
     * @return Response
     */
    public function handleAction(LessonSection $section, Request $request): Response
    {
        $this->courseAccessService->resolveUpdateAccess($section->getCourse());

        $form = $this->formFactory->create(LessonSectionType::class, $section);

        if ($request->isMethod(Request::METHOD_POST)) {
            $this->formHandler->handle($section, $request, $form);
            $this->flashBag->add('success', 'Раздел сохранен');

            return new RedirectResponse($this->router->generate('hb.lesson_section.list', ['id' => $section->getCourse()->getId()]));
        }

        $content = $this->twig->render('@HBAdmin/LessonSection/inline_edit.html.twig', [
            'section' => $section,
            'form' => $form->createView()
        ]);

        return new Response($content);
    }
}