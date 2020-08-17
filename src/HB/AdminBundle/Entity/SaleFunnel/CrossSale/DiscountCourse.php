<?php

namespace HB\AdminBundle\Entity\SaleFunnel\CrossSale;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelCrossSale;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_cross_sale_discount_course",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="course_funnel_cross_sale_unique", columns={"course_id", "funnel_cross_sale_id"})})
 * @ORM\Entity()
 * @UniqueEntity(
 *     fields={"course", "funnelCrossSale"},
 *     errorPath="funnelCrossSale",
 *     message="Курс уже добавлен."
 * )
 *
 */
class DiscountCourse
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
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $course;

    /**
     * @var SalesFunnelCrossSale
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelCrossSale", inversedBy="discountCourses")
     * @ORM\JoinColumn(name="funnel_cross_sale_id", referencedColumnName="id")
     */
    private $funnelCrossSale;

    /**
     * @var float
     *
     * @ORM\Column(name="discount_percent", type="float")
     */
    private $discountPercent;

    /**
     * Constructor
     *
     * @param SalesFunnelCrossSale $funnelCrossSale
     */
    public function __construct(SalesFunnelCrossSale $funnelCrossSale)
    {
        $this->funnelCrossSale = $funnelCrossSale;
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
    public function getCourse(): ?Course
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
     * @return SalesFunnelCrossSale
     */
    public function getFunnelCrossSale(): SalesFunnelCrossSale
    {
        return $this->funnelCrossSale;
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
     * @param SalesFunnelCrossSale | null $funnelCrossSale
     */
    public function setFunnelCrossSale(?SalesFunnelCrossSale $funnelCrossSale): void
    {
        $this->funnelCrossSale = $funnelCrossSale;
    }

    /**
     * @return array
     */
    public static function getPossibleDiscounts(): array
    {
        return [5, 10, 15, 20, 25, 30, 35, 40, 45, 50];
    }
}
