<?php

namespace HB\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;

/**
 * CustomerRole
 *
 * @ORM\Table(name="customer_role")
 * @ORM\Entity()
 */
class CustomerRole extends Role
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_PRODUCER = 'ROLE_PRODUCER';
    public const ROLE_AUTHOR = 'ROLE_AUTHOR';
    public const ROLE_MANAGER = 'ROLE_MANAGER';

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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\Customer", mappedBy="role")
    */
    private $customers;

    public function __construct(string $role)
    {
        parent::__construct($role);

        $this->customers = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $name = $this->name;

        if ($this->name === self::ROLE_AUTHOR) {
            $name = 'Автор';
        } elseif ($this->name === self::ROLE_MANAGER) {
            $name = 'Менеджер';
        } elseif ($this->name === self::ROLE_PRODUCER) {
            $name = 'Продюсер';
        } elseif ($this->name === self::ROLE_ADMIN) {
            $name = 'Администратор';
        }

        return $name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}

