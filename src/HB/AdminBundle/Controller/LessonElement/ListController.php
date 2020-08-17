<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\LessonElement;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonElement;
use HB\AdminBundle\Entity\QuizQuestionAnswer;
use HB\AdminBundle\Helper\TimeZoneList;
use HB\AdminBundle\Service\CourseAccessService;
use Symfony\Component\HttpFoundation\Response;

class ListController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var CourseAccessService
     */
    private $courseAccessService;

    /**
     * ListController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param \Twig_Environment      $twig
     * @param CourseAccessService    $courseAccessService
     */
    public function __construct(EntityManagerInterface $entityManager, \Twig_Environment $twig, CourseAccessService $courseAccessService)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->courseAccessService = $courseAccessService;
    }


    /**
     * @param Lesson $lesson
     *
     * @return Response
     */
    public function handleAction(Lesson $lesson): Response
    {
        $this->courseAccessService->resolveViewAccess($lesson->getSection()->getCourse());

        $elements = $this->entityManager->getRepository(LessonElement::class)->findBy(['lesson' => $lesson], ['priority' => 'ASC']);

        $timeZones = TimeZoneList::getTimezones(\DateTimeZone::EUROPE | \DateTimeZone::ASIA);

        $timeZoneResult = [];

        foreach ($timeZones as $timeZone) {
            $timeZoneResult = array_merge($timeZoneResult, $timeZone);
        }

        ksort($timeZoneResult);

        $content = $this->twig->render('@HBAdmin/LessonElement/list.html.twig', [
            'elements'                       => $elements,
            'hasQuestionsWithoutRightAnswer' => $this->isExistQuestionWithoutRightAnswer($lesson),
            'timezones'                      => json_encode( array_flip($timeZoneResult)),
        ]);

        $this->isExistQuestionWithoutRightAnswer($lesson);


        return new Response($content);
    }

    /**
     * @param Lesson $lesson
     *
     * @return bool
     */
    private function isExistQuestionWithoutRightAnswer(Lesson $lesson): bool
    {
        $countQuestionsWithRightAnswer = $this->entityManager->createQueryBuilder()
            ->select('COUNT(DISTINCT(question))')
            ->from(QuizQuestionAnswer::class, 'answer')
            ->innerJoin('answer.question', 'question')
            ->innerJoin('question.lessonElement', 'lessonElement')
            ->where('answer.isRight = :is_right')
            ->andWhere('lessonElement.lesson = :lesson')
            ->setParameters([
                'is_right' => true,
                'lesson'   => $lesson->getId(),
            ])
            ->getQuery()
            ->getSingleScalarResult();

        $totalQuestions = $this->entityManager->createQueryBuilder()
            ->from(QuizQuestionAnswer::class, 'answer')
            ->select('COUNT(DISTINCT(question))')
            ->innerJoin('answer.question', 'question')
            ->innerJoin('question.lessonElement', 'lessonElement')
            ->where('lessonElement.lesson = :lesson')
            ->setParameter('lesson', $lesson->getId())
            ->getQuery()
            ->getSingleScalarResult();

        return (bool) ($countQuestionsWithRightAnswer !== $totalQuestions);
    }
}