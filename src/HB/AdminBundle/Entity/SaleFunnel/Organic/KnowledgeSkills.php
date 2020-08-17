<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\SaleFunnel\Organic;

use Doctrine\ORM\Mapping as ORM;

/**
 * Knowledge skill
 * This class used with organic sale funnel
 *
 * @ORM\Table(name="knowledge_skills")
 * @ORM\Entity()
 */
class KnowledgeSkills
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
     * @ORM\Column(name="skill", type="text", nullable=false)
     */
    private $skill;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSkill(): ?string
    {
        return $this->skill;
    }

    /**
     * @param string $skill
     */
    public function setSkill(string $skill): void
    {
        $this->skill = $skill;
    }
}