<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Types\FunnelType;

/**
 * FunnelPriority
 *
 * @ORM\Table(name="funnel_priority", uniqueConstraints={
 *        @ORM\UniqueConstraint(name="course_funnel_priority_unique",
 *            columns={"course_id", "funnel_key"})
 *    })
 * @ORM\Entity()
 *
 */
class FunnelPriority implements CourseAccessInterface
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
     * @var Course
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Course")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $course;

    /**
     * @var string
     *
     * @ORM\Column(name="funnel_key", type="string", nullable=false)
     */
    private $funnelKey;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", nullable=false)
     */
    private $priority;


    /**
     * Constructor
     *
     * @param Course     $course
     * @param FunnelType $funnelType
     * @param int | null $priority
     */
    public function __construct(Course $course, FunnelType $funnelType, int $priority = 1)
    {
        $this->course = $course;
        $this->funnelKey = $funnelType->getValue();
        $this->priority = $priority;
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
     * @return FunnelType
     */
    public function getFunnelType(): FunnelType
    {
        return new FunnelType($this->funnelKey);
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
}
