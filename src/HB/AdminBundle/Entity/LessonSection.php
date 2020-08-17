<?php

namespace HB\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * LessonSection
 *
 * @ORM\Table(name="lesson_section")
 * @ORM\Entity()
 */
class LessonSection
{
    public const TYPE_DEFAULT = 'DEFAULT';
    public const TYPE_AFTER_DAYS = 'AFTER_DAYS';
    public const TYPE_BY_DATE = 'BY_DATE';

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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var Course
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="lessonSections")
     * @ORM\JoinColumn(name="id_course", referencedColumnName="id", onDelete="CASCADE")
    */
    private $course;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\Lesson", mappedBy="section", cascade={"persist", "remove"})
    */
    private $lessons;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var \DateTime | null
     *
     * @ORM\Column(name="by_date", type="datetime", nullable=true)
     */
    private $byDate;

    /**
     * @var int | null
     *
     * @ORM\Column(name="after_days", type="integer", nullable=true)
     */
    private $afterDays;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", nullable=false)
     */
    private $priority;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\CoursePriceBlock")
     * @ORM\JoinTable(name="lesson_sections_price_blocks",
     *      joinColumns={@ORM\JoinColumn(name="lesson_section_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="price_block_id", referencedColumnName="id")}
     *      )
     */
    private $priceBlocks;


    public function __construct(Course $course)
    {
        $this->type = self::TYPE_DEFAULT;
        $this->course = $course;
        $this->lessons = new ArrayCollection();
        $this->priority = 0;
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
     * @return ArrayCollection
     */
    public function getLessons()
    {
        return $this->lessons;
    }

    /**
     * @param ArrayCollection $lessons
     */
    public function setLessons($lessons)
    {
        $this->lessons = $lessons;
    }

    /**
     * @param Lesson $lesson
     */
    public function addLesson(Lesson $lesson)
    {
        $this->lessons->add($lesson);
    }


    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * @return Course
     */
    public function getCourse(): Course
    {
        return $this->course;
    }

    /**
     * @param Course $course
     */
    public function setCourse(Course $course)
    {
        $this->course = $course;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return LessonSection
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return \DateTime|null
     */
    public function getByDate(): ?\DateTime
    {
        return $this->byDate;
    }

    /**
     * @param \DateTime|null $byDate
     */
    public function setByDate(?\DateTime $byDate): void
    {
        $this->byDate = $byDate;
    }

    /**
     * @return int|null
     */
    public function getAfterDays(): ?int
    {
        return $this->afterDays;
    }

    /**
     * @param int|null $afterDays
     */
    public function setAfterDays(?int $afterDays): void
    {
        $this->afterDays = $afterDays;
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
     * @return ArrayCollection
     */
    public function getPriceBlocks()
    {
        return $this->priceBlocks;
    }

    /**
     * @param CoursePriceBlock $priceBlock
     */
    public function addPriceBlock(CoursePriceBlock $priceBlock): void
    {
        $this->priceBlocks[] = $priceBlock;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function hasPriceBlock(int $id)
    {
        /** @var CoursePriceBlock $block */
        foreach ($this->getPriceBlocks() as $block) {
            if ($block->getId() === $id) {
                return true;
            }
        }

        return false;
    }


    /**
     * @param CoursePriceBlock $priceBlock
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePriceBlock(CoursePriceBlock $priceBlock)
    {
        return $this->priceBlocks->removeElement($priceBlock);
    }
}
