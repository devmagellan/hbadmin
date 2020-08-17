<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_broken_basket")
 * @ORM\Entity()
 *
 */
class SalesFunnelBrokenBasket implements CourseAccessInterface
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
     * @var float
     *
     * @ORM\Column(name="discount_percent", type="float")
     */
    private $discountPercent;

    /**
     * @var Course
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="salesFunnelBrokenBasket")
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
        $this->discountPercent = 5;
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
        if (in_array($discountPercent, $this->getPossibleDiscounts())) {
            $this->discountPercent = $discountPercent;
        } else {
            throw new \InvalidArgumentException('Неверный размер скидки');
        }

    }

    /**
     * @return array
     */
    public static function getPossibleDiscounts(): array
    {
        return ['0 %' => 0, '5 %' => 5, '10 %' => 10, '15 %' => 15, '20 %' => 20];
    }
}
