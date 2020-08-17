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
    private $courseAccessService;

    /**
     * EditController constructor.
     *
     * @param \Twig_Environment    $twig
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface      $router
     * @param FormHandler          $formHandler
     * @param FlashBagInterface    $flashBag
     * @param CourseAccessService  $courseAccessService
     */
    public function __construct(\Twig_Environment $twig, FormFactoryInterface $formFactory, RouterInterface $router, FormHandler $formHandler, FlashBagInterface $flashBag, CourseAccessService $courseAccessService)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->formHandler = $formHandler;
        $this->flashBag = $flashBag;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param LessonSection $lessonSection
     * @param Request       $request
     *
     * @return Response
     */
    public function handleAction(LessonSection $lessonSection, Request $request): Response
    {
        $this->courseAccessService->resolveUpdateAccess($lessonSection->getCourse());

        $form = $this->formFactory->create(LessonSectionType::class, $lessonSection);

        if ($this->formHandler->handle($lessonSection, $request, $form)) {
            $this->flashBag->add('success', 'Урок сохранен.');

            return new RedirectResponse($this->router->generate('hb.course.edit', ['id' => $lessonSection->getCourse()->getId()]));
        }

        $content = $this->twig->render('@HBAdmin/LessonSection/edit.html.twig',[
            'section' => $lessonSection,
            'form' => $form->createView()
        ]);

        return new Response($content);
    }

}