<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Lesson\Quiz;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\QuizQuestionAnswer;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;

class ChangeAnswerRightController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * ChangeAnswerRightController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param QuizQuestionAnswer $answer
     *
     * @return JsonResponse
     */
    public function handleAction(QuizQuestionAnswer $answer): JsonResponse
    {
        $course = $this->entityManager->getRepository(QuizQuestionAnswer::class)->getCourseViaQuestionAnswer($answer);
        $this->courseAccessService->resolveUpdateAccess($course);

        if ($answer->isRight()) {
            $answer->setIsRight(false);
        } else {
            $answer->setIsRight(true);
        }

        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success']);
    }
}