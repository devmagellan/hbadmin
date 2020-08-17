<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Teachable\TeachableComment;
use HB\AdminBundle\Entity\Teachable\TeachableLectureProgress;
use HB\AdminBundle\Entity\Teachable\TeachableQuizResponse;

class StudentProgressAggregator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * StudentProgressAggregator constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $email
     * @param int    $courseId
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function update(string $email, int $courseId): void
    {
        $commentsRating = $this->entityManager->createQueryBuilder()
            ->select('SUM(com.rating) as commentsRating')
            ->from(TeachableComment::class, 'com')
            ->where('com.course_id = :course_id')
            ->andWhere('com.studentEmail = :email')
            ->setParameters([
                'course_id' => $courseId,
                'email'     => $email,
            ])
            ->getQuery()->getSingleScalarResult();

        $correctAnswers = $this->entityManager->createQueryBuilder()
            ->select('SUM(q.correctAnswers) as correctAnswers')
            ->from(TeachableQuizResponse::class, 'q')
            ->where('q.course_id = :course_id')
            ->andWhere('q.studentEmail = :email')
            ->setParameters([
                'course_id' => $courseId,
                'email'     => $email,
            ])
            ->getQuery()->getSingleScalarResult();

        $progress = $this->entityManager->getRepository(TeachableLectureProgress::class)->findOneBy([
            'course_id'    => $courseId,
            'studentEmail' => $email,
        ]);

        if ($progress) {
            $progress->updateStatistic((int) $commentsRating, (int) $correctAnswers);

            $this->entityManager->persist($progress);
            $this->entityManager->flush();
        }
    }
}