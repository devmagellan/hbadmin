<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use HB\AdminBundle\Entity\Teachable\TeachableComment;
use HB\AdminBundle\Entity\Teachable\TeachableCourseStudent;
use HB\AdminBundle\Entity\Teachable\TeachableCourseStudentInterface;
use HB\AdminBundle\Entity\Teachable\TeachableEnrollment;
use HB\AdminBundle\Entity\Teachable\TeachableLectureProgress;
use HB\AdminBundle\Entity\Teachable\TeachableQuizResponse;
use HB\AdminBundle\Entity\Teachable\Webhook\CommentCreated;
use HB\AdminBundle\Entity\Teachable\Webhook\EnrollmentCreated;
use HB\AdminBundle\Entity\Teachable\Webhook\LectureProgressCreated;
use HB\AdminBundle\Entity\Teachable\Webhook\ResponseCreated;

class TeachableWebhooksMapper
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var StudentProgressAggregator
     */
    private $studentProgressAggregator;

    /**
     * TeachableWebhooksMapper constructor.
     *
     * @param EntityManagerInterface    $entityManager
     * @param StudentProgressAggregator $studentProgressAggregator
     */
    public function __construct(EntityManagerInterface $entityManager, StudentProgressAggregator $studentProgressAggregator)
    {
        $this->entityManager = $entityManager;
        $this->studentProgressAggregator = $studentProgressAggregator;
    }

    /**
     * @param EnrollmentCreated $enrollmentCreated
     *
     * @throws \Exception
     */
    public function updateEnrollment(EnrollmentCreated $enrollmentCreated)
    {
        $hookId = $enrollmentCreated->getBodyData()['hook_event_id'];
        $exist = $this->entityManager->getRepository(TeachableEnrollment::class)->findOneBy(['hookEventId' => $hookId]);

        $enrollment = TeachableEnrollment::fromWebhook($enrollmentCreated->getBodyData(), $exist);

        $this->entityManager->persist($enrollment);
        $this->entityManager->flush();

        $this->updateOrCreateStudent($enrollment);
    }

    /**
     * @param LectureProgressCreated $lectureProgressCreated
     *
     * @throws \Exception
     */
    public function updateLectureProgress(LectureProgressCreated $lectureProgressCreated = null)
    {
        $hooks = [];
        if ($lectureProgressCreated) {
            $hooks[] = $lectureProgressCreated;
        } else {
            $hooks = $this->entityManager->getRepository(TeachableLectureProgress::class)->findBy([], ['id' => 'ASC']);
        }

        foreach ($hooks as $lectureProgressCreated) {
            $data = $lectureProgressCreated->getBodyData();
            $newProgress = TeachableLectureProgress::fromWebhook($data);

            $exist = $this->entityManager->getRepository(TeachableLectureProgress::class)->findOneBy([
                'course_id'    => $newProgress->getCourseId(),
                'studentEmail' => $newProgress->getStudentEmail(),
            ]);

            if ($exist) {
                $lectureProgress = TeachableLectureProgress::fromWebhook($data, $exist);
            } else {
                $lectureProgress = TeachableLectureProgress::fromWebhook($data);
            }

            $this->entityManager->persist($lectureProgress);
            $this->entityManager->flush();

            $this->updateOrCreateStudent($lectureProgress);
        }
    }

    /**
     * @param TeachableCourseStudentInterface $courseStudentData
     *
     * @return TeachableCourseStudent
     *
     * @throws \Exception
     */
    public function updateOrCreateStudent(TeachableCourseStudentInterface $courseStudentData): TeachableCourseStudent
    {
        $exist = $this->entityManager->getRepository(TeachableCourseStudent::class)->findOneBy([
            'course_id'    => $courseStudentData->getCourseId(),
            'studentEmail' => $courseStudentData->getStudentEmail(),
        ]);

        if ($exist) {
            $student = $exist;
            $student->update($courseStudentData);
        } else {
            $student = TeachableCourseStudent::fromInterface($courseStudentData);
        }

        if ($courseStudentData instanceof TeachableEnrollment) {
            $student->updateLastActivity($courseStudentData);
            $student->setLastSignInAt($courseStudentData->getLastSignedAt());
        } else if ($courseStudentData instanceof TeachableLectureProgress) {
            $student->updateLastSignIn($courseStudentData);
        }

        $this->entityManager->persist($student);
        $this->entityManager->flush();

        return $student;
    }

    public function updateResponseCreated(ResponseCreated $webhook = null)
    {
        $hooks = [];
        if ($webhook) {
            $hooks[] = $webhook;
        } else {
            $hooks = $this->entityManager->getRepository(ResponseCreated::class)->findBy([], ['id' => 'DESC']);
        }

        foreach ($hooks as $hook) {
            $data = $hook->getBodyData();
            $newResponse = TeachableQuizResponse::fromWebhook($data);

            $exist = $this->entityManager->getRepository(TeachableQuizResponse::class)->findOneBy([
                'studentEmail' => $newResponse->getStudentEmail(),
                'lecture_id'   => $newResponse->getLectureId(),
            ]);

            if ($exist) {
                $response = TeachableQuizResponse::fromWebhook($data, $exist);
            } else {
                $response = TeachableQuizResponse::fromWebhook($data);
            }

            $lectureProgress = $this->entityManager->getRepository(TeachableLectureProgress::class)->findOneBy([
                'lecture_id' => $response->getLectureId(),
            ]);

            if ($lectureProgress) {
                $response->setCourseId($lectureProgress->getCourseId());
            }

            $this->entityManager->persist($response);
            $this->entityManager->flush();

            if ($response->getCourseId()) {
                $this->studentProgressAggregator->update($response->getStudentEmail(), $response->getCourseId());
            }
        }
    }

    public function updateCommentCreated(CommentCreated $webhook = null)
    {
        if ($webhook) {
            $webhooks = [$webhook];
        } else {
            $webhooks = $this->entityManager->getRepository(CommentCreated::class)->findBy([], ['id' => 'ASC']);
        }

        /** @var CommentCreated $webhook */
        foreach ($webhooks as $hook) {
            $data = $hook->getBodyData();
            $hookId = $data['hook_event_id'];
            $exist = $this->entityManager->getRepository(TeachableComment::class)->findOneBy(['hookEventId' => $hookId]);

            if ($exist) {
                $comment = TeachableComment::fromWebhook($data, $exist);
            } else {
                $comment = TeachableComment::fromWebhook($data);
            }

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $this->studentProgressAggregator->update($comment->getStudentEmail(), $comment->getCourseId());
        }
    }
}