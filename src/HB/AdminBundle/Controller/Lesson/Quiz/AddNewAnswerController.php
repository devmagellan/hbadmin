<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Lesson\Quiz;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\QuizQuestion;
use HB\AdminBundle\Entity\QuizQuestionAnswer;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class AddNewAnswerController
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
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * AddNewAnswerController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param RouterInterface        $router
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, RouterInterface $router, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param QuizQuestion $question
     * @param Request      $request
     *
     * @return RedirectResponse
     */
    public function handleAction(QuizQuestion $question, Request $request): RedirectResponse
    {
        $course = $this->entityManager->getRepository(QuizQuestion::class)->getCourseViaQuestion($question);
        $this->courseAccessService->resolveCreateAccess($course);

        $isRight = (bool) $request->get('is_right');
        $text = $request->get('question_text');

        $answer = new QuizQuestionAnswer();
        $answer->setQuestion($question);
        $answer->setIsRight($isRight);
        $answer->setAnswerText($text);

        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.lesson.edit', [
            'id' => $question->getLessonElement()->getLesson()->getId(),
            'tab' => 'quiz'
        ]));
    }
}
