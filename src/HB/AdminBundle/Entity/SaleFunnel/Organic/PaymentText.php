<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\SaleFunnel\Organic;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment text
 * This class used with organic sale funnel
 *
 * @ORM\Table(name="payment_text")
 * @ORM\Entity()
 */
class PaymentText
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
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;

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
    public function getDescription(): ?string
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