<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_walker_start")
 * @ORM\Entity()
 *
 */
class SalesFunnelWalkerStart implements CourseAccessInterface
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
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="salesFunnelWalkerStart")
     * @ORM\JoinColumn(name="id_course", referencedColumnName="id")
     */
    private $course;

    /**
     * Constructor
     *
     * @param Course $course
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
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

}
