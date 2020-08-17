<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\Embedded;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class Integrations
{
    /**
     * @var string | null
     *
     * @ORM\Column(name="google_analytics", type="text", nullable=true)
     */
    private $googleAnalytics;

    /**
     * @var string | null
     *
     * @ORM\Column(name="facebook_pixel", type="text", nullable=true)
     */
    private $facebookPixel;

    /**
     * @var string | null
     *
     * @ORM\Column(name="yandex_metrics", type="text", nullable=true)
     */
    private $yandexMetrics;

    /**
     * @var string | null
     *
     * @ORM\Column(name="vk", type="text", nullable=true)
     */
    private $vk;

    /**
     * @var string | null
     *
     * @ORM\Column(name="ok", type="text", nullable=true)
     */
    private $ok;

    /**
     * @var string | null
     *
     * @ORM\Column(name="twitter", type="text", nullable=true)
     */
    private $twitter;

    /**
     * @var string | null
     *
     * @ORM\Column(name="linkedin", type="text", nullable=true)
     */
    private $linkedin;

    /**
     * @var string | null
     *
     * @ORM\Column(name="pinterest", type="text", nullable=true)
     */
    private $pinterest;

    /**
     * @return string|null
     */
    public function getGoogleAnalytics(): ?string
    {
        return $this->googleAnalytics;
    }

    /**
     * @param string|null $googleAnalytics
     */
    public function setGoogleAnalytics(?string $googleAnalytics): void
    {
        $this->googleAnalytics = $googleAnalytics;
    }

    /**
     * @return string|null
     */
    public function getFacebookPixel(): ?string
    {
        return $this->facebookPixel;
    }

    /**
     * @param string|null $facebookPixel
     */
    public function setFacebookPixel(?string $facebookPixel): void
    {
        $this->facebookPixel = $facebookPixel;
    }

    /**
     * @return string|null
     */
    public function getYandexMetrics(): ?string
    {
        return $this->yandexMetrics;
    }

    /**
     * @param string|null $yandexMetrics
     */
    public function setYandexMetrics(?string $yandexMetrics): void
    {
        $this->yandexMetrics = $yandexMetrics;
    }

    /**
     * @return string|null
     */
    public function getVk(): ?string
    {
        return $this->vk;
    }

    /**
     * @param string|null $vk
     */
    public function setVk(?string $vk): void
    {
        $this->vk = $vk;
    }

    /**
     * @return string|null
     */
    public function getOk(): ?string
    {
        return $this->ok;
    }

    /**
     * @param string|null $ok
     */
    public function setOk(?string $ok): void
    {
        $this->ok = $ok;
    }

    /**
     * @return string|null
     */
    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    /**
     * @param string|null $twitter
     */
    public function setTwitter(?string $twitter): void
    {
        $this->twitter = $twitter;
    }

    /**
     * @return string|null
     */
    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    /**
     * @param string|null $linkedin
     */
    public function setLinkedin(?string $linkedin): void
    {
        $this->linkedin = $linkedin;
    }

    /**
     * @return string|null
     */
    public function getPinterest(): ?string
    {
        return $this->pinterest;
    }

    /**
     * @param string|null $pinterest
     */
    public function setPinterest(?string $pinterest): void
    {
        $this->pinterest = $pinterest;
    }
}
