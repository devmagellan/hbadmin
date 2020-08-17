<?php

namespace HB\AdminBundle\Repository;


use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\QuizQuestionAnswer;

class QuizQuestionAnswerRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param QuizQuestionAnswer $answer
     *
     * @return Course|null
     */
    public function getCourseViaQuestionAnswer(QuizQuestionAnswer $answer): ?Course
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('course')
            ->from(Course::class, 'course')
            ->leftJoin('course.lessonSections', 'lesson_section')
            ->leftJoin('lesson_section.lessons', 'lesson')
            ->leftJoin('lesson.elements', 'element')
            ->leftJoin('element.questions', 'question')
            ->leftJoin('question.answers', 'answer')
            ->where('answer.id = :answer_id')
            ->setParameter('answer_id', $answer->getId())
            ->getQuery()->getOneOrNullResult();
    }
}
