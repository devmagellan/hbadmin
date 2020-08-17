<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Customer
 *
 * @ORM\Table(name="customer_payment_report", uniqueConstraints={
 *        @ORM\UniqueConstraint(name="customer_report_date_unique", columns={"customer_id", "report_date"})
 * })
 * @ORM\Entity()
 * @UniqueEntity(fields={"customer", "reportDate"}, message="Отчет за этот месяц уже создан", errorPath="reportMonth" )
 */
class CustomerPaymentReport
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
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", nullable=false, onDelete="CASCADE")
     */
    private $customer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="report_date", type="datetime")
     */
    private $reportDate;

    /**
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile")
     * @ORM\JoinColumn(name="report_file_id", referencedColumnName="id", nullable=true)
     */
    private $file;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->reportDate = $this->modifyDate(new \DateTime());
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return \DateTime
     */
    public function getReportDate(): \DateTime
    {
        return $this->reportDate;
    }

    /**
     * @param \DateTime $reportDate
     */
    public function setReportDate(\DateTime $reportDate): void
    {
        $this->reportDate = $this->modifyDate($reportDate);
    }

    /**
     * @return UploadCareFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param UploadCareFile $file
     */
    public function setFile(UploadCareFile $file): void
    {
        $this->file = $file;
    }

    /**
     * @return int
     */
    public function getReportMonth()
    {
        return (int) $this->reportDate->format("m");
    }

    /**
     * @param int $month
     */
    public function setReportMonth(int $month)
    {
        $date = clone $this->reportDate;
        $date->setDate((int) $this->reportDate->format("Y"), $month,1);
        $this->setReportDate($date);
    }

    /**
     * @return int
     */
    public function getReportYear()
    {
        return (int) $this->reportDate->format("Y");
    }

    /**
     * @param int $year
     */
    public function setReportYear(int $year)
    {
        $date = clone $this->reportDate;
        $date->setDate($year, (int) $date->format("m"), 1);
        $this->setReportDate($date);
    }

    /**
     * Modify DateTime for get last day of month
     *
     * @param \DateTime $dateTime
     *
     * @return \DateTime
     */
    private function modifyDate(\DateTime $dateTime)
    {
        $dateTime->setTime(0, 0, 0);

        return $dateTime;
    }

}
