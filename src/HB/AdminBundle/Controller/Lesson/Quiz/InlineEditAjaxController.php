<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Lesson\Quiz;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\QuizQuestion;
use HB\AdminBundle\Entity\QuizQuestionAnswer;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class InlineEditAjaxController
{
    private const QUESTION_FIELD = 'question';
    private const ANSWER_FIELD = 'answer';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * InlineEditAjaxController constructor.
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
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function handleAction(Request $request): JsonResponse
    {
        $id = $request->get('pk');
        $field = $request->get('name');
        $value = $request->get('value');

        if (!$value || $value === '') {
            return new JsonResponse('Empty value does not allowed', 400);
        }

        if (!in_array($field, [self::ANSWER_FIELD, self::QUESTION_FIELD])) {
            return new JsonResponse('Invalid field name ' . $field, 400);
        }

        if (self::QUESTION_FIELD === $field) {
            $question = $this->entityManager->getRepository(QuizQuestion::class)->find($id);

            if (!$question) {
                return new JsonResponse('Invalid request', 400);
            }

            $course = $this->entityManager->getRepository(QuizQuestion::class)->getCourseViaQuestion($question);
            if ($course) {
                $this->courseAccessService->resolveUpdateAccess($course);
            }


            $question->setQuestionText($value);
            $this->entityManager->persist($question);
            $this->entityManager->flush();

            return new JsonResponse('true', 200);
        } elseif (self::ANSWER_FIELD === $field) {
            $answer = $this->entityManager->getRepository(QuizQuestionAnswer::class)->find($id);

            if (!$answer) {
                return new JsonResponse('Invalid request', 400);
            }

            $course = $this->entityManager->getRepository(QuizQuestionAnswer::class)->getCourseViaQuestionAnswer($answer);
            if ($course) {
                $this->courseAccessService->resolveUpdateAccess($course);
            }

            $answer->setAnswerText($value);
            $this->entityManager->persist($answer);
            $this->entityManager->flush();

            return new JsonResponse('true', 200);
        }


        return new JsonResponse("System Error ", 400);
    }
}
