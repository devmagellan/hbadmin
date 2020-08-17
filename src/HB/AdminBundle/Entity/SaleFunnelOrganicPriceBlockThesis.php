<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Customer
 *
 * @ORM\Table(name="sale_funnel_organic_price_block_thesis")
 * @ORM\Entity()
 */
class SaleFunnelOrganicPriceBlockThesis
{
    const MAX_ADDITIONAL_THESIS = 10;

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
     * @ORM\Column(name="description", type="string", nullable=false)
     */
    private $description;

    /**
     * @var SaleFunnelOrganicPriceBlockSetting
     *
     * @ORM\ManyToOne(targetEntity="SaleFunnelOrganicPriceBlockSetting", inversedBy="theses")
     * @ORM\JoinColumn(name="sale_funnel_organic_price_block_setting_id", referencedColumnName="id")
     */
    private $saleFunnelOrganicPriceBlockSetting;

    /**
     * CoursePriceBlockAdditional constructor.
     *
     * @param string                             $description
     * @param SaleFunnelOrganicPriceBlockSetting $saleFunnelOrganicPriceBlockSetting
     */
    public function __construct(string $description, SaleFunnelOrganicPriceBlockSetting $saleFunnelOrganicPriceBlockSetting)
    {
        $this->description = $description;
        $this->saleFunnelOrganicPriceBlockSetting = $saleFunnelOrganicPriceBlockSetting;
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

    /**
     * @return SaleFunnelOrganicPriceBlockSetting
     */
    public function getSaleFunnelOrganicPriceBlockSetting(): SaleFunnelOrganicPriceBlockSetting
    {
        return $this->saleFunnelOrganicPriceBlockSetting;
    }

    /**
     * @param SaleFunnelOrganicPriceBlockSetting $saleFunnelOrganicPriceBlockSetting
     */
    public function setSaleFunnelOrganicPriceBlockSetting(SaleFunnelOrganicPriceBlockSetting $saleFunnelOrganicPriceBlockSetting): void
    {
        $this->saleFunnelOrganicPriceBlockSetting = $saleFunnelOrganicPriceBlockSetting;
    }

}
