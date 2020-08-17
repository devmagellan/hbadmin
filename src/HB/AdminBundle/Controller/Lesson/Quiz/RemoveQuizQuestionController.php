<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Lesson\Quiz;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\QuizQuestion;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveQuizQuestionController
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
     * RemoveQuizQuestionController constructor.
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
     *
     * @return RedirectResponse
     */
    public function handleAction(QuizQuestion $question): RedirectResponse
    {
        $course = $this->entityManager->getRepository(QuizQuestion::class)->getCourseViaQuestion($question);
        if ($course) {
            $this->courseAccessService->resolveDeleteAccess($course);
        }

        $lessonId = $question->getLessonElement()->getLesson()->getId();

        $this->entityManager->remove($question);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.lesson.edit', ['id' => $lessonId, 'tab' => 'quiz']));
    }
}
