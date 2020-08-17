<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_partner")
 * @ORM\Entity()
 */
class SalesFunnelPartner implements CourseAccessInterface
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
     * @ORM\Column(name="partner", type="string")
     */
    private $partner;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string")
     */
    private $email;

    /**
     * @var float
     *
     * @ORM\Column(name="award_percent", type="float")
     */
    private $awardPercent;

    /**
     * @var Course
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="saleFunnelPartners")
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
        $this->awardPercent = 0;
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
     * @return string
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * @param string $partner
     */
    public function setPartner(string $partner): void
    {
        $this->partner = $partner;
    }

    /**
     * @return int
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return float
     */
    public function getAwardPercent(): float
    {
        return $this->awardPercent;
    }

    /**
     * @param float $awardPercent
     */
    public function setAwardPercent(float $awardPercent): void
    {
        $this->awardPercent = $awardPercent;
    }

}
