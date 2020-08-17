<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity\SaleFunnel\Organic;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\UploadCareFile;

/**
 * Company logo
 * This class used with organic sale funnel
 *
 * @ORM\Table(name="company_logo")
 * @ORM\Entity()
 */
class CompanyLogo
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
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="company_logo_file_id", referencedColumnName="id", nullable=true)
     */
    private $logo;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return UploadCareFile | null
     */
    public function getLogo(): ?UploadCareFile
    {
        return $this->logo;
    }

    /**
     * @param UploadCareFile $logo
     */
    public function setLogo(UploadCareFile $logo = null): void
    {
        $this->logo = $logo;
    }

}