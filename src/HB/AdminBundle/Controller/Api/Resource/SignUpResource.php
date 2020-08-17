<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Controller\Api\Resource;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @Assert\GroupSequence({"SignUpResource", "Strict"})
 */
class SignUpResource implements QueryResourceInterface
{
    private const SALT_SIMPLE = '12345';
    private const SALT_PROJECT = 'cSNgmTegjU09CA2Qa49E';

    /**
     * @var string
     *
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    public $email;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="8", max="32")
     */
    public $password;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="8", max="255")
     */
    public $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $hash;

    /**
     * @param ExecutionContextInterface $context
     *
     * @Assert\Callback()
     */
    public function verifyHash(ExecutionContextInterface $context)
    {
        $expectedHash = md5(self::SALT_SIMPLE.$this->email.self::SALT_PROJECT);

        if ($expectedHash !== $this->hash) {
            $context->buildViolation('Hash is not valid.')
                ->atPath('email')
                ->addViolation();
        }
    }
}