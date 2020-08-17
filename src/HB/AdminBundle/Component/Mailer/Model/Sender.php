<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Component\Mailer\Model;

use HB\AdminBundle\Component\Model\Email;

class Sender
{
    /**
     * @var Email
     */
    private $email;

    /**
     * @var string | null
     */
    private $name;

    /**
     * Sender constructor.
     *
     * @param Email       $email
     * @param string|null $name
     */
    public function __construct(Email $email, string $name = null)
    {
        $this->email = $email;
        $this->name = $name;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}