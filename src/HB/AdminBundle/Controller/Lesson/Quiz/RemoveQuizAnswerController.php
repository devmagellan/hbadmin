<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Lesson\Quiz;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\QuizQuestionAnswer;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RemoveQuizAnswerController
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
     * RemoveQuizAnswerController constructor.
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
     * @param QuizQuestionAnswer $answer
     *
     * @return RedirectResponse
     */
    public function handleAction(QuizQuestionAnswer $answer): RedirectResponse
    {
        $course = $this->entityManager->getRepository(QuizQuestionAnswer::class)->getCourseViaQuestionAnswer($answer);
        if ($course) {
            $this->courseAccessService->resolveDeleteAccess($course);
        }

        $lessonId = $answer->getQuestion()->getLessonElement()->getLesson()->getId();

        $this->entityManager->remove($answer);
        $this->entityManager->flush();

        return new RedirectResponse($this->router->generate('hb.lesson.edit', ['id' => $lessonId, 'tab' => 'quiz']));
    }
}

