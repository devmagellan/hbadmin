<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Types\CustomerPacketType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Customer
 *
 * @ORM\Table(name="customer_packet")
 * @ORM\Entity()
 * @UniqueEntity("type")
 */
class CustomerPacket
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
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\Customer", mappedBy="packet")
     */
    private $customers;

    /**
     * CustomerPacket constructor.
     *
     * @param CustomerPacketType $type
     */
    public function __construct(CustomerPacketType $type)
    {
        $this->type = $type->getValue();
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param CustomerPacketType $type
     */
    public function setType(CustomerPacketType $type): void
    {
        $this->type = $type->getValue();
    }

    /**
     * @return ArrayCollection
     */
    public function getCustomers(): ArrayCollection
    {
        return $this->customers;
    }

    /**
     * @param ArrayCollection $customers
     */
    public function setCustomers(ArrayCollection $customers): void
    {
        $this->customers = $customers;
    }

    public function __toString(): string
    {
        return CustomerPacketType::getName($this->type);
    }
}
