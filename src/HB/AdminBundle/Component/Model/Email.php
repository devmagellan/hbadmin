<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Component\Model;

/**
 * The value object for store email.
 */
class Email
{
    /**
     * @var string
     */
    private $value;

    /**
     * Constructor.
     *
     * @param string $email
     */
    public function __construct(string $email)
    {
        if (!\filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(sprintf(
                'The value "%s" is not valid email.',
                $email
            ));
        }

        $this->value = $email;
    }

    /**
     * Get the value of email
     *
     * @return string
     */
    public function getValue(): string
    {
        return mb_strtolower($this->value);
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
