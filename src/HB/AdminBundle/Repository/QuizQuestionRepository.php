<?php

namespace HB\AdminBundle\Repository;


use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\QuizQuestion;

class QuizQuestionRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param QuizQuestion $question
     *
     * @return Course|null
     */
    public function getCourseViaQuestion(QuizQuestion $question): ?Course
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('course')
            ->from(Course::class, 'course')
            ->leftJoin('course.lessonSections', 'lesson_section')
            ->leftJoin('lesson_section.lessons', 'lesson')
            ->leftJoin('lesson.elements', 'element')
            ->leftJoin('element.questions', 'question')
            ->where('question.id = :question_id')
            ->setParameter('question_id', $question->getId())
            ->getQuery()->getOneOrNullResult();
    }
}
