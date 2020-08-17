<?php

namespace HB\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Types\LessonElementType;

/**
 * LessonElement
 * This is complex lesson element, witch can contain different types of content
 *
 * @ORM\Table(name="lesson_element")
 * @ORM\Entity()
 */
class LessonElement
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
     * @ORM\Column(name="lesson_element_type", type="string", length=255, nullable=false)
     */
    private $type;

    /**
     * @var Lesson
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Lesson", inversedBy="elements")
     * @ORM\JoinColumn(name="id_lesson", referencedColumnName="id", nullable=false, onDelete="CASCADE")
    */
    private $lesson;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\QuizQuestion", mappedBy="lessonElement", cascade={"remove", "persist"})
     */
    private $questions;

    /**
     * @var string | null
     *
     * @ORM\Column(name="content_text", type="text", nullable=true)
     */
    private $text;

    /**
     * @var string | null
     *
     * @ORM\Column(name="content_iframe", type="text", nullable=true)
     */
    private $iframe;

    /**
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     */
    private $file;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", nullable=false)
     */
    private $priority;

    /**
     * @var \DateTime | null
     *
     * @ORM\Column(name="consultation_at", type="datetime", nullable=true)
     */
    private $consultationAt;

    /**
     * @var \DateTime | null
     *
     * @ORM\Column(name="webinar_at", type="datetime", nullable=true)
     */
    private $webinarAt;

    /**
     * @var string | null
     *
     * @ORM\Column(name="consultation_timezone", type="string", nullable=true)
     */
    private $consultationTimezone;

    /**
     * @var string | null
     *
     * @ORM\Column(name="webinar_timezone", type="string", nullable=true)
     */
    private $webinarTimezone;

    /**
     * LessonElement constructor.
     *
     * @param Lesson            $lesson
     * @param LessonElementType $type
     */
    public function __construct(Lesson $lesson, LessonElementType $type)
    {
        $this->priority = 0;
        $this->type = $type->getValue();
        $this->lesson = $lesson;
        $this->consultationTimezone = 'Europe/Kiev';
        $this->webinarTimezone = 'Europe/Kiev';
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return LessonElementType
     */
    public function getType(): LessonElementType
    {
        return new LessonElementType($this->type);
    }

    /**
     * @param LessonElementType $type
     */
    public function setType(LessonElementType $type): void
    {
        $this->type = $type->getValue();
    }

    /**
     * @return Lesson
     */
    public function getLesson(): Lesson
    {
        return $this->lesson;
    }

    /**
     * @param Lesson $lesson
     */
    public function setLesson(Lesson $lesson): void
    {
        $this->lesson = $lesson;
    }

    /**
     * @return ArrayCollection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param ArrayCollection $questions
     */
    public function setQuestions(ArrayCollection $questions): void
    {
        $this->questions = $questions;
    }

    /**
     * @return null|string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param null|string $text
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return null|string
     */
    public function getIframe(): ?string
    {
        return $this->iframe;
    }

    /**
     * @param null|string $iframe
     */
    public function setIframe(?string $iframe): void
    {
        $this->iframe = $iframe;
    }

    /**
     * @return UploadCareFile
     */
    public function getFile(): ?UploadCareFile
    {
        return $this->file;
    }

    /**
     * @param UploadCareFile $file
     */
    public function setFile(UploadCareFile $file): void
    {
        $this->file = $file;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return \DateTime|null
     */
    public function getConsultationAt(): ?\DateTime
    {
        return $this->consultationAt;
    }

    /**
     * @param \DateTime|null $consultationAt
     */
    public function setConsultationAt(?\DateTime $consultationAt): void
    {
        $this->consultationAt = $consultationAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getWebinarAt(): ?\DateTime
    {
        return $this->webinarAt;
    }

    /**
     * @param \DateTime|null $webinarAt
     */
    public function setWebinarAt(?\DateTime $webinarAt): void
    {
        $this->webinarAt = $webinarAt;
    }

    /**
     * @return string|null
     */
    public function getConsultationTimezone(): ?string
    {
        return $this->consultationTimezone;
    }

    /**
     * @param string|null $consultationTimezone
     */
    public function setConsultationTimezone(?string $consultationTimezone): void
    {
        $this->consultationTimezone = $consultationTimezone;
    }

    /**
     * @return string|null
     */
    public function getWebinarTimezone(): ?string
    {
        return $this->webinarTimezone;
    }

    /**
     * @param string|null $webinarTimezone
     */
    public function setWebinarTimezone(?string $webinarTimezone): void
    {
        $this->webinarTimezone = $webinarTimezone;
    }
}
