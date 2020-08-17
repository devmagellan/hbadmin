<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Twig;

use Twig\Extension\AbstractExtension;

class Extensions extends AbstractExtension
{
    /**
     * @var string
     */
    private $intercomSecret;

    public function __construct(string $intercomSecret)
    {
        $this->intercomSecret = $intercomSecret;
    }

    public function getFilters()
    {
        return [
            new \Twig_Filter('sha_256', [$this, 'sha256']),
            new \Twig_Filter('cash', [$this, 'cash']),
            new \Twig_Filter('cash_format', [$this, 'cashFormat']),
        ];
    }

    public function sha256(string $number)
    {
        return hash_hmac("sha256", $number, $this->intercomSecret);
    }

    public function cash(int $sum)
    {
        $sum = round($sum/100, 2);

        return number_format($sum, 2, '.', ' ');
    }

    public function cashFormat($sum)
    {
        return number_format($sum, 2, '.', ' ');
    }
}
