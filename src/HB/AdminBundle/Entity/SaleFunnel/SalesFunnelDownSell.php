<?php

namespace HB\AdminBundle\Entity\SaleFunnel;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\LessonSection;

/**
 * SalesFunnel
 *
 * @ORM\Table(name="sales_funnel_down_sell")
 * @ORM\Entity()
 */
class SalesFunnelDownSell implements CourseAccessInterface
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
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="term", type="integer")
     */
    private $term;

    /**
     * @var Course
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Course", inversedBy="saleFunnelDownSells")
     * @ORM\JoinColumn(name="id_course", referencedColumnName="id")
     */
    private $course;

    /**
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\LessonSection", cascade={"persist"})
     * @ORM\JoinTable(name="sales_funnel_down_sell_lesson_sections",
     *      joinColumns={@ORM\JoinColumn(name="sales_funnel_down_sell_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="section_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $sections;

    /**
     * @var string | null
     *
     * @ORM\Column(name="external_link", type="string", nullable=true)
     */
    private $externalLink;

    /**
     * Constructor
     *
     * @param Course $course
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
        $this->price = 0;
        $this->term = 0;

        $this->sections = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
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
     * @param Course $course
     */
    public function setCourse(Course $course): void
    {
        $this->course = $course;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return sprintf('%d разделов по цене %s, на %d дня (дней)',
            \count($this->sections),
            $this->price,
            $this->term
            );
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * @param LessonSection $section
     */
    public function addSection(LessonSection $section): void
    {
        $this->sections[] = $section;
    }

    /**
     * @param LessonSection $section
     */
    public function removeSection(LessonSection $section): void
    {
        $this->sections->removeElement($section);
    }

    /**
     * @return ArrayCollection | LessonSection[]
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param mixed $sections
     */
    public function setSections($sections): void
    {
        $this->sections = $sections;
    }


    /**
     * @param LessonSection $section
     *
     * @return bool
     */
    public function hasSection(LessonSection $section)
    {
        return $this->sections->contains($section);
    }

    /**
     * @return int
     */
    public function getTerm(): int
    {
        return $this->term;
    }

    /**
     * @param int $term
     */
    public function setTerm(int $term): void
    {
        $this->term = $term;
    }

    /**
     * @return string|null
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return string|null
     */
    public function getExternalLink(): ?string
    {
        return $this->externalLink;
    }

    /**
     * @param string|null $externalLink
     */
    public function setExternalLink(?string $externalLink): void
    {
        $this->externalLink = $externalLink;
    }
}
