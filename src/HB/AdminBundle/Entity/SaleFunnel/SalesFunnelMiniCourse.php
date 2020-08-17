<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\MiniCourse\MiniLesson;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_mini_course")
 * @ORM\Entity()
 *
 */
class SalesFunnelMiniCourse implements CourseAccessInterface
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
     * @var string | null
     *
     * @ORM\Column(name="title", type="string", nullable=true)
     */
    private $title;

    /**
     * @var Course
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="salesFunnelMiniCourse")
     * @ORM\JoinColumn(name="id_course", referencedColumnName="id", onDelete="SET NULL")
     */
    private $course;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\MiniCourse\MiniLesson", cascade={"persist", "remove"}, mappedBy="funnelMiniCourse")
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
        $this->lessons = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
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
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param MiniLesson $lesson
     */
    public function addLesson(MiniLesson $lesson)
    {
        $this->lessons[] = $lesson;
    }

    /**
     * @param MiniLesson $lesson
     *
     * @return bool
     */
    public function removeLesson(MiniLesson $lesson)
    {
        return $this->lessons->removeElement($lesson);
    }

    /**
     * @return ArrayCollection
     */
    public function getLessons()
    {
        return $this->lessons;
    }

}
