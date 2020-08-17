<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Twig;

use HB\AdminBundle\Entity\SaleFunnel\SalesFunnelOrganic;
use HB\AdminBundle\Service\PriceBlockSettingService;
use Twig\Extension\AbstractExtension;

class PriceBlockExtension extends AbstractExtension
{
    /**
     * @var PriceBlockSettingService
     */
    private $priceBlockSettingService;

    /**
     * PriceBlockExtension constructor.
     *
     * @param PriceBlockSettingService $priceBlockSettingService
     */
    public function __construct (PriceBlockSettingService $priceBlockSettingService)
    {
        $this->priceBlockSettingService = $priceBlockSettingService;
    }


    public function getFilters()
    {
        return [
            new \Twig_Filter('getPriceBlock', [$this, 'getPriceBlock']),
        ];
    }

    public function getPriceBlock(SalesFunnelOrganic $funnel, string $type)
    {
        return $this->priceBlockSettingService->getPriceBlock($funnel, $type);
    }
}
