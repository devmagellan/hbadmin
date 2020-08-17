<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActionLog
 *
 * @ORM\Table(name="action_log")
 * @ORM\Entity()
 */
class ActionLog
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
     * @var Course
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="logs")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $course;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="string")
     */
    private $object;

    /**
     * @var int | null
     *
     * @ORM\Column(name="object_id", type="integer", nullable=true)
     */
    private $objectId;

    /**
     * @var string
     *
     * @ORM\Column(name="changeset", type="text", nullable=true)
     */
    private $changeSet;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Customer")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     */
    private $time;

    /**
     * @var bool
     *
     * @ORM\Column(name="published", type="boolean")
     */
    private $published;

    /**
     * @var string|null
     *
     * @ORM\Column(name="funnel", type="string")
     */
    private $funnel;

    /**
     * @var int | null
     *
     * @ORM\Column(name="funnel_id", type="integer", nullable=true)
     */
    private $funnelId;

    /**
     * ActionLog constructor.
     *
     * @param Course   $course
     * @param Customer $user
     * @param array    $changeSet
     * @param string   $object
     * @param int|null $objectId
     * @param string   $funnel
     * @param int|null   $funnelId
     */
    public function __construct(Course $course, Customer $user, array $changeSet, string $object = '', $objectId = null, string $funnel = '', int $funnelId = null)
    {
        $this->course = $course;
        $this->object = $object;
        $this->setChangeSet($changeSet);
        $this->user = $user;
        $this->published = false;
        $this->objectId = $objectId;
        $this->funnel = $funnel;
        $this->funnelId = $funnelId;

        $this->time = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Course
     */
    public function getCourse(): Course
    {
        return $this->course;
    }

    /**
     * @return string
     */
    public function getObject(): string
    {
        return $this->object;
    }

    /**
     * @return array
     */
    public function getChangeSet(): array
    {
        $decoded = \json_decode($this->changeSet, true);

        return $decoded;
    }

    /**
     * @param array $changeSet
     */
    private function setChangeSet(array $changeSet): void
    {
        $this->changeSet = json_encode($changeSet);
    }

    /**
     * @return Customer
     */
    public function getUser(): Customer
    {
        return $this->user;
    }

    /**
     * @return \DateTime
     */
    public function getTime(): \DateTime
    {
        return $this->time;
    }

    /**
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->published;
    }

    /**
     * @param bool $published
     */
    public function setPublished(bool $published): void
    {
        $this->published = $published;
    }

    /**
     * @return int|null
     */
    public function getObjectId(): ?int
    {
        return $this->objectId;
    }

    /**
     * @return string
     */
    public function getFunnel(): string
    {
        return $this->funnel;
    }

    /**
     * @return int|null
     */
    public function getFunnelId(): ?int
    {
        return $this->funnelId;
    }
}
