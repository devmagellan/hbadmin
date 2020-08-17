<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="one_time_offer")
 * @ORM\Entity()
 */
class OneTimeOffer
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
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOneTimeOffer", inversedBy="offers")
     * @ORM\JoinColumn(name="id_funnel", referencedColumnName="id")
     */
    private $funnel;

    /**
     * @var float | null
     *
     * @ORM\Column(name="full_price", type="float", nullable=true)
     */
    private $fullPrice;

    /**
     * @var float | null
     *
     * @ORM\Column(name="discount_price", type="float", nullable=true)
     */
    private $discountPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

    /**
     * Constructor
     *
     * @param SalesFunnelOneTimeOffer $funnel
     */
    public function __construct(SalesFunnelOneTimeOffer $funnel)
    {
        $this->funnel = $funnel;
        $this->fullPrice = 0.0;
        $this->discountPrice = 0.0;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return SalesFunnelOneTimeOffer
     */
    public function getFunnel(): SalesFunnelOneTimeOffer
    {
        return $this->funnel;
    }

    /**
     * @param SalesFunnelOneTimeOffer $funnel
     */
    public function setFunnel(SalesFunnelOneTimeOffer $funnel): void
    {
        $this->funnel = $funnel;
    }

    /**
     * @return float|null
     */
    public function getFullPrice(): ?float
    {
        return $this->fullPrice;
    }

    /**
     * @param float|null $fullPrice
     */
    public function setFullPrice(?float $fullPrice): void
    {
        $this->fullPrice = $fullPrice;
    }

    /**
     * @return float|null
     */
    public function getDiscountPrice(): ?float
    {
        return $this->discountPrice;
    }

    /**
     * @param float|null $discountPrice
     */
    public function setDiscountPrice(?float $discountPrice): void
    {
        $this->discountPrice = $discountPrice;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

}
