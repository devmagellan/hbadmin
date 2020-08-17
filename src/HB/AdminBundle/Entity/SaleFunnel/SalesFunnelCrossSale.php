<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\CrossSale\DiscountCourse;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_cross_sale")
 * @ORM\Entity()
 *
 */
class SalesFunnelCrossSale implements CourseAccessInterface
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
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\CrossSale\DiscountCourse", mappedBy="funnelCrossSale", cascade={"remove"})
     */
    private $discountCourses;

    /**
     * @var boolean
     *
     * @ORM\Column(name="participate_author_club", type="boolean")
     */
    private $participateAuthorClub;

    /**
     * @var Course
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="salesFunnelCrossSale", cascade={"persist"})
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id", onDelete="CASCADE")
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
        $this->discountCourses = new ArrayCollection();
        $this->participateAuthorClub = false;
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
     * @return bool
     */
    public function isParticipateAuthorClub(): bool
    {
        return $this->participateAuthorClub;
    }

    /**
     * @param bool $participateAuthorClub
     */
    public function setParticipateAuthorClub(bool $participateAuthorClub): void
    {
        $this->participateAuthorClub = $participateAuthorClub;
    }

    /**
     * @return array
     */
    public static function getPossibleDiscounts(): array
    {
        return [5, 10, 15, 20, 25, 30, 35, 40, 45, 50];
    }

    /**
     * @return ArrayCollection
     */
    public function getDiscountCourses()
    {
        return $this->discountCourses;
    }

    /**
     * @param DiscountCourse $course
     */
    public function removeDiscountCourse(DiscountCourse $course)
    {
        $course->setFunnelCrossSale(null);
        $this->discountCourses->removeElement($course);
    }
}
