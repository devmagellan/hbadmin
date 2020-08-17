<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\SaleFunnel\Webinar;

use Doctrine\ORM\Mapping as ORM;

/**
 * Webinar thesis
 * This class used with webinar sale funnel
 *
 * @ORM\Table(name="webinar_thesis")
 * @ORM\Entity()
 */
class WebinarThesis
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