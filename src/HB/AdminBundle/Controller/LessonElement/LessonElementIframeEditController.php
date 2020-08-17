<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonElement;


use HB\AdminBundle\Entity\LessonElement;
use HB\AdminBundle\Entity\Types\LessonElementType;
use HB\AdminBundle\Form\Lesson\LessonElementIframeType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class LessonElementIframeEditController
{
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
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * LessonElementIframeEditController constructor.
     *
     * @param RouterInterface      $router
     * @param FormHandler          $formHandler
     * @param FlashBagInterface    $flashBag
     * @param FormFactoryInterface $formFactory
     * @param \Twig_Environment    $twig
     * @param CourseAccessService  $courseAccessService
     */
    public function __construct(RouterInterface $router, FormHandler $formHandler, FlashBagInterface $flashBag, FormFactoryInterface $formFactory, \Twig_Environment $twig, CourseAccessService $courseAccessService)
    {
        $this->router = $router;
        $this->formHandler = $formHandler;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
        $this->twig = $twig;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param LessonElement $element
     * @param Request       $request
     *
     * @return RedirectResponse|Response
     */
    public function handleAction(LessonElement $element, Request $request): Response
    {
        $this->courseAccessService->resolveUpdateAccess($element->getLesson()->getSection()->getCourse());

        $referer = $request->headers->get('referer', $this->router->generate('hb.lesson.edit', ['id' => $element->getLesson()->getId()]));

        if (LessonElementType::IFRAME !== $element->getType()->getValue()) {
            $this->flashBag->add('error', 'Ошибка редактирования iframe');
            return new RedirectResponse($referer);
        }

        $form = $this->formFactory->create(LessonElementIframeType::class, $element);

        if ($this->formHandler->handle($element, $request, $form)) {
            $this->flashBag->add('success', 'Iframe сохранен');
            return new RedirectResponse($this->router->generate('hb.lesson.edit', ['id' => $element->getLesson()->getId()]));
        }

        $content = $this->twig->render('@HBAdmin/LessonElement/element_iframe_edit.html.twig', [
            'form' => $form->createView(),
            'element' => $element
        ]);

        return new Response($content);
    }
}