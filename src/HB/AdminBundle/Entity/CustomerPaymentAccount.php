<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Customer
 *
 * @ORM\Table(name="customer_payment_account")
 * @ORM\Entity()
 */
class CustomerPaymentAccount
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
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\Customer", mappedBy="paymentAccount")
     * @ORM\JoinColumn(name="customer_id", onDelete="CASCADE")
     */
    private $customer;

    /**
     * @var string | null
     *
     * @ORM\Column(name="paypal_email", type="string", nullable=true)
     */
    private $paypalEmail;

    /**
     * @var string | null
     *
     * @ORM\Column(name="payoneer_email", type="string", nullable=true)
     */
    private $payoneerEmail;

    /**
     * @var string | null
     *
     * @ORM\Column(name="bank_customer_name", type="string", nullable=true)
     */
    private $bankCustomerName;

    /**
     * @var string | null
     *
     * @ORM\Column(name="bank_name", type="string", nullable=true)
     */
    private $bankName;

    /**
     * @var string | null
     *
     * @ORM\Column(name="bank_address", type="string", nullable=true)
     */
    private $bankAddress;

    /**
     * @var string | null
     *
     * @ORM\Column(name="bank_swift_code", type="string", nullable=true)
     */
    private $bankSwiftCode;

    /**
     * @var string | null
     *
     * @ORM\Column(name="bank_account_code", type="string", nullable=true)
     */
    private $bankAccountCode;

    /**
     * @var string | null
     *
     * @ORM\Column(name="bank_correspondent", type="string", nullable=true)
     */
    private $bankCorrespondent;

    /**
     * Constructor.
     *
     * @param Customer $customer
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
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
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @return string|null
     */
    public function getPaypalEmail(): ?string
    {
        return $this->paypalEmail;
    }

    /**
     * @param string|null $paypalEmail
     */
    public function setPaypalEmail(?string $paypalEmail): void
    {
        $this->paypalEmail = $paypalEmail;
    }

    /**
     * @return string|null
     */
    public function getPayoneerEmail(): ?string
    {
        return $this->payoneerEmail;
    }

    /**
     * @param string|null $payoneerEmail
     */
    public function setPayoneerEmail(?string $payoneerEmail): void
    {
        $this->payoneerEmail = $payoneerEmail;
    }

    /**
     * @return string|null
     */
    public function getBankCustomerName(): ?string
    {
        return $this->bankCustomerName;
    }

    /**
     * @param string|null $bankCustomerName
     */
    public function setBankCustomerName(?string $bankCustomerName): void
    {
        $this->bankCustomerName = $bankCustomerName;
    }

    /**
     * @return string|null
     */
    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    /**
     * @param string|null $bankName
     */
    public function setBankName(?string $bankName): void
    {
        $this->bankName = $bankName;
    }

    /**
     * @return string|null
     */
    public function getBankAddress(): ?string
    {
        return $this->bankAddress;
    }

    /**
     * @param string|null $bankAddress
     */
    public function setBankAddress(?string $bankAddress): void
    {
        $this->bankAddress = $bankAddress;
    }

    /**
     * @return string|null
     */
    public function getBankSwiftCode(): ?string
    {
        return $this->bankSwiftCode;
    }

    /**
     * @param string|null $bankSwiftCode
     */
    public function setBankSwiftCode(?string $bankSwiftCode): void
    {
        $this->bankSwiftCode = $bankSwiftCode;
    }

    /**
     * @return string|null
     */
    public function getBankAccountCode(): ?string
    {
        return $this->bankAccountCode;
    }

    /**
     * @param string|null $bankAccountCode
     */
    public function setBankAccountCode(?string $bankAccountCode): void
    {
        $this->bankAccountCode = $bankAccountCode;
    }

    /**
     * @return string|null
     */
    public function getBankCorrespondent(): ?string
    {
        return $this->bankCorrespondent;
    }

    /**
     * @param string|null $bankCorrespondent
     */
    public function setBankCorrespondent(?string $bankCorrespondent): void
    {
        $this->bankCorrespondent = $bankCorrespondent;
    }

}
