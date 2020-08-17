<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Lesson;

use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonElement;
use HB\AdminBundle\Entity\QuizQuestion;
use HB\AdminBundle\Entity\QuizQuestionAnswer;
use HB\AdminBundle\Entity\Types\LessonElementType;
use HB\AdminBundle\Form\Lesson\LessonElementConsultationType;
use HB\AdminBundle\Form\Lesson\LessonElementIframeType;
use HB\AdminBundle\Form\Lesson\LessonElementTextType;
use HB\AdminBundle\Form\Lesson\LessonElementWebinarType;
use HB\AdminBundle\Form\Quiz\QuizQuestionType;
use HB\AdminBundle\Service\CourseAccessService;
use HB\AdminBundle\Service\FormHandler;
use HB\AdminBundle\Service\Intercom\IntercomEvents;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Router;

class EditController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var Router
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
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * EditController constructor.
     *
     * @param \Twig_Environment      $twig
     * @param FormFactory            $formFactory
     * @param Router                 $router
     * @param FormHandler            $formHandler
     * @param FlashBagInterface      $flashBag
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(\Twig_Environment $twig, FormFactory $formFactory, Router $router, FormHandler $formHandler, FlashBagInterface $flashBag, EntityManagerInterface $entityManager, CourseAccessService $courseAccessService)
    {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->formHandler = $formHandler;
        $this->flashBag = $flashBag;
        $this->entityManager = $entityManager;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param Lesson  $lesson
     * @param Request $request
     *
     * @return Response
     */
    public function handleAction(Lesson $lesson, Request $request): Response
    {
        $this->courseAccessService->resolveCreateAccess($lesson->getSection()->getCourse());
        $this->courseAccessService->registerEvent(IntercomEvents::ACCESS_LESSON, [
            'description' => $lesson->getTitle()
        ]);

        $lessonElementQuiz = $this->entityManager->getRepository(LessonElement::class)->findOneBy([
            'lesson' => $lesson,
            'type'   => LessonElementType::QUESTION,
        ]);

        /** lesson can contain only 1 quiz */
        if (!$lessonElementQuiz) {
            $elementQuiz = new LessonElement($lesson, new LessonElementType(LessonElementType::QUESTION));
        } else {
            $elementQuiz = $lessonElementQuiz;
        }

        $elementText = new LessonElement($lesson, new LessonElementType(LessonElementType::TEXT));
        $formText = $this->formFactory->create(LessonElementTextType::class, $elementText);

        $elementIframe = new LessonElement($lesson, new LessonElementType(LessonElementType::IFRAME));
        $formIframe = $this->formFactory->create(LessonElementIframeType::class, $elementIframe);

        $elementWebinar = new LessonElement($lesson, new LessonElementType(LessonElementType::WEBINAR));
        $elementWebinar->setWebinarAt((new \DateTime())->setTime(12, 0, 0));
        $formWebinar = $this->formFactory->create(LessonElementWebinarType::class, $elementWebinar);

        $elementConsultation = new LessonElement($lesson, new LessonElementType(LessonElementType::CONSULTATION));
        $elementConsultation->setConsultationAt((new \DateTime())->setTime(12, 0, 0));
        $formConsultation = $this->formFactory->create(LessonElementConsultationType::class, $elementConsultation);

        $quizQuestion = new QuizQuestion();
        $quizQuestion->setLessonElement($elementQuiz);
        $formQuestion = $this->formFactory->create(QuizQuestionType::class, $quizQuestion);

        if ($this->formHandler->handle($elementText, $request, $formText)) {
            $this->flashBag->add('success', 'Текст сохранен');
        }

        if ($this->formHandler->handle($elementIframe, $request, $formIframe)) {
            $this->flashBag->add('success', 'Iframe сохранен');
        }

        if ($this->formHandler->handle($elementWebinar, $request, $formWebinar)) {
            $this->flashBag->add('success', 'Вебинар сохранен');
        }

        if ($this->formHandler->handle($elementConsultation, $request, $formConsultation)) {
            $this->flashBag->add('success', 'Консультация сохранена');
        }

        $formQuestion->handleRequest($request);
        if ($formQuestion->isSubmitted()) {

            /** @var QuizQuestionAnswer $answer */
            foreach ($quizQuestion->getAnswers() as $answer) {
                $answer->setQuestion($quizQuestion);
            }

            $this->entityManager->persist($elementQuiz);
            $this->entityManager->persist($quizQuestion);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'Вопрос добавлен');

            return new RedirectResponse($this->router->generate('hb.lesson.edit', ['id' => $lesson->getId(), 'tab' => 'quiz']));
        }

        $content = $this->twig->render('@HBAdmin/Lesson/edit.html.twig', [
            'quiz'             => $elementQuiz,
            'lesson'           => $lesson,
            'formText'         => $formText->createView(),
            'formQuestion'     => $formQuestion->createView(),
            'formIframe'       => $formIframe->createView(),
            'formWebinar'      => $formWebinar->createView(),
            'formConsultation' => $formConsultation->createView(),
        ]);

        return new Response($content);
    }
}
