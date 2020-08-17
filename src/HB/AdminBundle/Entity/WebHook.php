<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebHook
 *
 * @ORM\Table(name="webhook")
 * @ORM\Entity()
 */
class WebHook
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
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var string | null
     *
     * @ORM\Column(name="referer", type="string", nullable=true)
     */
    private $referer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreatedAt", type="datetime")
     */
    private $dateCreatedAt;

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

}
