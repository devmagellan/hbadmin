<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\SaleFunnel\Webinar;

use Doctrine\ORM\Mapping as ORM;

/**
 * Webinar promo code
 * This class used with webinar sale funnel
 *
 * @ORM\Table(name="webinar_promo_code")
 * @ORM\Entity()
 */
class WebinarPromoCode
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
     * @ORM\Column(name="code", type="text", nullable=false)
     */
    private $code;

    /**
     * @var float
     *
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * WebinarPromoCode constructor.
     *
     * @param string $code
     * @param float  $price
     */
    public function __construct(string $code = 'code', float $price = 0)
    {
        $this->code = $code;
        $this->price = $price;
    }


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}