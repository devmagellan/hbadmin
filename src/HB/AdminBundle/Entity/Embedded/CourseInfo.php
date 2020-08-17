<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\Embedded;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Embeddable
 */
class CourseInfo
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 10,
     *      max = 120,
     *      minMessage = "Название должно содержать миимум {{ limit }} символов",
     *      maxMessage = "Название должно содержать максимум {{ limit }} символов"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="sub_title", type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 10,
     *      max = 200,
     *      minMessage = "Подзаголовок должен содержать миимум {{ limit }} символов",
     *      maxMessage = "Подзаголовок должен содержать максимум {{ limit }} символов"
     * )
     */
    private $subTitle;

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }

    /**
     * @param string $subTitle
     */
    public function setSubTitle(string $subTitle): void
    {
        $this->subTitle = $subTitle;
    }
}
