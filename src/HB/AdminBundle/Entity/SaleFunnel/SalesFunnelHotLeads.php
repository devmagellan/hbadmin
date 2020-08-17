<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_hot_leads")
 * @ORM\Entity()
 *
 */
class SalesFunnelHotLeads implements CourseAccessInterface
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
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\SaleFunnel\HotLeads\SuccessHistory", mappedBy="funnelHotLeads", cascade={"remove"})
     */
    private $histories;

    /**
     * @var Course
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="salesFunnelHotLeads")
     * @ORM\JoinColumn(name="id_course", referencedColumnName="id")
     */
    private $course;

    /**
     * @var int | null
     *
     * @ORM\Column(name="installment_plan_months", type="integer", nullable=true)
     */
    private $installmentPlanMonths;

    /**
     * @var bool
     *
     * @ORM\Column(name="installment_plan", type="boolean")
     */
    private $installmentPlan;


    /**
     * Constructor
     *
     * @param Course $course
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
        $this->installmentPlan = false;
        $this->installmentPlanMonths = 0;
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
     * @return ArrayCollection
     */
    public function getHistories()
    {
        return $this->histories;
    }

    /**
     * @return int | null
     */
    public function getInstallmentPlanMonths(): ?int
    {
        return $this->installmentPlanMonths;
    }

    /**
     * @return bool
     */
    public function isInstallmentPlan(): bool
    {
        return $this->installmentPlan;
    }

    /**
     * @param bool $installmentPlan
     */
    public function setInstallmentPlan(bool $installmentPlan): void
    {
        $this->installmentPlan = $installmentPlan;
    }

    /**
     * @param int | null $installmentPlanMonths
     */
    public function setInstallmentPlanMonths(?int $installmentPlanMonths): void
    {
        $this->installmentPlanMonths = $installmentPlanMonths;
    }
}
