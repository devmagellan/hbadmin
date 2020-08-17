<?php

namespace HB\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Lesson
 *
 * @ORM\Table(name="lesson")
 * @ORM\Entity(repositoryClass="HB\AdminBundle\Repository\LessonRepository")
 */
class Lesson
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreatedAt", type="datetime")
     */
    private $dateCreatedAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="isFree", type="boolean")
     */
    private $isFree;

    /**
     * @var LessonSection
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\LessonSection", inversedBy="lessons")
     * @ORM\JoinColumn(name="id_section", referencedColumnName="id", nullable=false, onDelete="CASCADE")
    */
    private $section;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\LessonElement", mappedBy="lesson", cascade={"remove", "persist"})
     */
    private $elements;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", nullable=false)
     */
    private $priority;


    public function __construct()
    {
        $this->setDateCreatedAt(new \DateTime("now"));
        $this->isFree = false;
        $this->priority = 0;
        $this->elements = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return LessonSection
     */
    public function getSection(): ?LessonSection
    {
        return $this->section;
    }

    /**
     * @param LessonSection $section
     */
    public function setSection(LessonSection $section)
    {
        $this->section = $section;
    }

    /**
     * @param string $title
     *
     * @return Lesson
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param \DateTime $dateCreatedAt
     *
     * @return Lesson
     */
    public function setDateCreatedAt($dateCreatedAt)
    {
        $this->dateCreatedAt = $dateCreatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreatedAt()
    {
        return $this->dateCreatedAt;
    }

    /**
     * @param bool $isFree
     */
    public function setIsFree(bool $isFree): void
    {
        $this->isFree = $isFree;
    }

    /**
     * @return bool
     */
    public function getIsFree(): bool
    {
        return $this->isFree;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return ArrayCollection
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @param ArrayCollection $elements
     */
    public function setElements(ArrayCollection $elements): void
    {
        $this->elements = $elements;
    }
}
