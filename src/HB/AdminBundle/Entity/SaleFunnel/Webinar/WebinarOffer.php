<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\SaleFunnel\Webinar;

use Doctrine\ORM\Mapping as ORM;

/**
 * Webinar offer
 * This class used with webinar sale funnel
 *
 * @ORM\Table(name="webinar_offer")
 * @ORM\Entity()
 */
class WebinarOffer
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
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var int | null
     *
     * @ORM\Column(name="months", type="integer", nullable=true)
     */
    private $months;

    /**
     * WebinarOffer constructor.
     */
    public function __construct()
    {
        $this->price = 0;
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
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return float
     */
    public function getPrice(): ?float
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

    /**
     * @return int|null
     */
    public function getMonths(): ?int
    {
        return $this->months;
    }

    /**
     * @param int|null $months
     */
    public function setMonths(?int $months): void
    {
        $this->months = $months;
    }

}