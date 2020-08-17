<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Quiz
 *
 * @ORM\Table(name="quiz_question")
 * @ORM\Entity(repositoryClass="HB\AdminBundle\Repository\QuizQuestionRepository")
 */
class QuizQuestion
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
     * @ORM\Column(name="question_text", type="text", nullable=false)
     */
    private $questionText;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\QuizQuestionAnswer", mappedBy="question", cascade={"remove", "persist"})
     */
    private $answers;

    /**
     * @var LessonElement
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\LessonElement", inversedBy="questions")
     * @ORM\JoinColumn(name="id_lesson_element", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $lessonElement;

    /**
     * QuizQuestion constructor.
     */
    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getQuestionText(): ?string
    {
        return $this->questionText;
    }

    /**
     * @param string $questionText
     */
    public function setQuestionText(string $questionText): void
    {
        $this->questionText = $questionText;
    }

    /**
     * @return ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * @param QuizQuestionAnswer $answer
     */
    public function addAnswer(QuizQuestionAnswer $answer): void
    {
        $this->answers[] = $answer;
    }

    /**
     * @return LessonElement
     */
    public function getLessonElement(): ?LessonElement
    {
        return $this->lessonElement;
    }

    /**
     * @param LessonElement $lessonElement
     */
    public function setLessonElement(LessonElement $lessonElement): void
    {
        $this->lessonElement = $lessonElement;
    }

}
