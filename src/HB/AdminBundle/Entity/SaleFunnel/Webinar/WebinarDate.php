<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\SaleFunnel\Webinar;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar;

/**
 * Webinar date
 * This class used with webinar sale funnel
 *
 * @ORM\Table(name="webinar_date")
 * @ORM\Entity()
 */
class WebinarDate
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
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string")
     */
    private $timezone;

    /**
     * @var SalesFunnelWebinar
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelWebinar", inversedBy="dates")
     * @ORM\JoinColumn(name="webinar_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $webinar;

    /**
     * WebinarDate constructor.
     */
    public function __construct()
    {
        $this->time = new \DateTime();
        $this->timezone = 'Europe/Kiev';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param \DateTime $time
     */
    public function setTime(?\DateTime $time): void
    {
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone(?string $timezone): void
    {
        $this->timezone = $timezone;
    }

    /**
     * @return SalesFunnelWebinar
     */
    public function getWebinar(): ?SalesFunnelWebinar
    {
        return $this->webinar;
    }

    /**
     * @param SalesFunnelWebinar $webinar
     */
    public function setWebinar(?SalesFunnelWebinar $webinar): void
    {
        $this->webinar = $webinar;
    }

}