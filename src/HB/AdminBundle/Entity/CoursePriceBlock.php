<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Customer
 *
 * @ORM\Table(name="course_block")
 * @ORM\Entity()
 * @UniqueEntity("type")
 */
class CoursePriceBlock
{
    const TYPE_BASIC = 'BASIC';
    const TYPE_OPTIMAL = 'OPTIMAL';
    const TYPE_VIP = 'VIP';

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
     * @ORM\Column(name="title", type="string", unique=true, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="block_type", type="string", unique=true, nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

    /**
     * CoursePriceBlock constructor.
     *
     * @param string  $title
     * @param integer $priority
     * @param string  $type
     */
    public function __construct(string $title, int $priority, string $type)
    {
        $this->title = $title;
        $this->priority = $priority;
        $this->type = $type;
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
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title;
    }
}
