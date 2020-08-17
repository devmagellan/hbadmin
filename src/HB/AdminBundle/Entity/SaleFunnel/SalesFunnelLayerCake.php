<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Lesson;
use HB\AdminBundle\Entity\LessonSection;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_layer_cake")
 * @ORM\Entity()
 */
class SalesFunnelLayerCake implements CourseAccessInterface
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
     * @ORM\Column(name="title", type="string")
     */
    private $title;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var Course
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="saleFunnelLayerCakes")
     * @ORM\JoinColumn(name="id_course", referencedColumnName="id")
     */
    private $course;

    /**
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\LessonSection", cascade={"persist"})
     * @ORM\JoinTable(name="sales_funnel_layer_cake_lesson_sections",
     *      joinColumns={@ORM\JoinColumn(name="sales_funnel_layer_cake_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="section_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $sections;

    /**
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\Lesson", cascade={"persist"})
     * @ORM\JoinTable(name="sales_funnel_layer_cake_lesson_lessons",
     *      joinColumns={@ORM\JoinColumn(name="sales_funnel_layer_cake_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="lesson_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $lessons;

    /**
     * Constructor
     *
     * @param Course $course
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
        $this->price = 0;

        $this->sections = new ArrayCollection();
        $this->lessons = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
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
    public function setCourse(Course $course): void
    {
        $this->course = $course;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @param LessonSection $section
     */
    public function addSection(LessonSection $section): void
    {
        $this->sections[] = $section;
    }

    /**
     * @param LessonSection $section
     */
    public function removeSection(LessonSection $section): void
    {
        $this->sections->removeElement($section);
    }

    /**
     * @return ArrayCollection
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param Lesson $lesson
     */
    public function addLesson(Lesson $lesson): void
    {
        $this->lessons[] = $lesson;
    }

    /**
     * @param Lesson $lesson
     */
    public function removeLesson(Lesson $lesson): void
    {
        $this->lessons->removeElement($lesson);
    }

    public function getLessons()
    {
        return $this->lessons;
    }

    /**
     * @param LessonSection $section
     *
     * @return bool
     */
    public function hasSection(LessonSection $section)
    {
        return $this->sections->contains($section);
    }

    /**
     * @param Lesson $lesson
     *
     * @return bool
     */
    public function hasLesson(Lesson $lesson)
    {
        return $this->lessons->contains($lesson);
    }
}
