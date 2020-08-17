<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_post_sale")
 * @ORM\Entity()
 *
 */
class SalesFunnelPostSale implements CourseAccessInterface
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
     * @var bool
     *
     * @ORM\Column(name="activate_after_days", type="boolean")
     */
    private $activateAfterDays;

    /**
     * @var float
     *
     * @ORM\Column(name="discount_percent", type="float")
     */
    private $discountPercent;

    /**
     * @var Course
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="salesFunnelPostSale")
     * @ORM\JoinColumn(name="id_course", referencedColumnName="id", onDelete="SET NULL")
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
        $this->discountPercent = 0;
        $this->activateAfterDays = false;
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
     * @return float
     */
    public function getDiscountPercent(): float
    {
        return $this->discountPercent;
    }

    /**
     * @param float $discountPercent
     */
    public function setDiscountPercent(float $discountPercent): void
    {
        if (in_array($discountPercent, self::getPossibleDiscounts())) {
            $this->discountPercent = $discountPercent;
        } else {
            throw new \InvalidArgumentException('Неверный размер скидки');
        }
    }

    /**
     * @return bool
     */
    public function isActivateAfterDays(): bool
    {
        return $this->activateAfterDays;
    }

    /**
     * @param bool $activateAfterDays
     */
    public function setActivateAfterDays(bool $activateAfterDays): void
    {
        $this->activateAfterDays = $activateAfterDays;
    }

    /**
     * @return array
     */
    public static function getPossibleDiscounts(): array
    {
        return [5, 10, 15, 20, 25, 30, 35, 40, 45, 50];
    }
}
