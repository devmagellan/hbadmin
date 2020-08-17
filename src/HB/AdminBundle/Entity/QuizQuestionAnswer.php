<?php

namespace HB\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LessonSection
 *
 * @ORM\Table(name="quiz_question_answer")
 * @ORM\Entity(repositoryClass="HB\AdminBundle\Repository\QuizQuestionAnswerRepository")
 */
class QuizQuestionAnswer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $answerText;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_right", type="boolean")
     */
    private $isRight;

    /**
     * @var QuizQuestion
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\QuizQuestion", inversedBy="answers")
     * @ORM\JoinColumn(name="id_quiz_question", referencedColumnName="id", nullable=false, onDelete="CASCADE")
    */
    private $question;

    public function __construct()
    {
        $this->isRight = false;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAnswerText(): ?string
    {
        return $this->answerText;
    }

    /**
     * @param string $answerText
     */
    public function setAnswerText(string $answerText): void
    {
        $this->answerText = $answerText;
    }

    /**
     * @return bool
     */
    public function isRight(): bool
    {
        return $this->isRight;
    }

    /**
     * @param bool $isRight
     */
    public function setIsRight(bool $isRight): void
    {
        $this->isRight = $isRight;
    }

    /**
     * @return QuizQuestion
     */
    public function getQuestion(): ?QuizQuestion
    {
        return $this->question;
    }

    /**
     * @param QuizQuestion $question
     */
    public function setQuestion(QuizQuestion $question): void
    {
        $this->question = $question;
    }

}
