<?php

namespace HB\AdminBundle\Entity\SaleFunnel\HotLeads;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelHotLeads;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_hot_leads_success_history")
 * @ORM\Entity()
 *
 */
class SuccessHistory
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
     * @var SalesFunnelHotLeads
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\SaleFunnel\SalesFunnelHotLeads", inversedBy="histories")
     * @ORM\JoinColumn(name="funnel_hot_leads_id", referencedColumnName="id")
     */
    private $funnelHotLeads;

    /**
     * @var string | null
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * Constructor
     *
     * @param SalesFunnelHotLeads $funnelHotLeads
     */
    public function __construct(SalesFunnelHotLeads $funnelHotLeads)
    {
        $this->funnelHotLeads = $funnelHotLeads;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return SalesFunnelHotLeads
     */
    public function getFunnelHotLeads(): SalesFunnelHotLeads
    {
        return $this->funnelHotLeads;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string | null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
