<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity\Teachable\Webhook;

use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract WebHook
 */
abstract class AbstractWebhook
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    protected $body;

    /**
     * @var string | null
     *
     * @ORM\Column(name="referer", type="string", nullable=true)
     */
    protected $referer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreatedAt", type="datetime")
     */
    protected $dateCreatedAt;

    /**
     * WebHook constructor.
     *
     * @param string|null $body
     * @param string|null $referer
     */
    public function __construct(?string $body, ?string $referer)
    {
        $this->dateCreatedAt = new \DateTime("now");
        $this->body = $body;
        $this->referer = $referer;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getBodyData(): array
    {
        return is_string($this->body) ? json_decode($this->body, true): [];
    }
}
