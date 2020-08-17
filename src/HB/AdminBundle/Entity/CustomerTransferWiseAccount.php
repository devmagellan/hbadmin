<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Customer
 *
 * @ORM\Table(name="customer_transferwise_account")
 * @ORM\Entity()
 */
class CustomerTransferWiseAccount
{
    public const TYPE_UA = 'UA';
    public const TYPE_RU_LOCAl = 'RU_LOCAL';
    public const TYPE_RU_CARD = 'RU_CARD';


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
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\Customer", mappedBy="transferWiseAccount")
     * @ORM\JoinColumn(name="customer_id", onDelete="CASCADE")
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="account_type", type="string", nullable=false)
     */
    private $accountType;

    /**
     * @var string | null
     *
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    private $city;

    /**
     * @var string | null
     *
     * @ORM\Column(name="address", type="string", nullable=true)
     */
    private $address;

    /**
     * @var string | null
     *
     * @ORM\Column(name="zip", type="string", nullable=true)
     */
    private $zip;

    /**
     * @var string | null
     *
     * @ORM\Column(name="ru_first_name", type="string", nullable=true)
     */
    private $ruFirstName;

    /**
     * @var string | null
     *
     * @ORM\Column(name="ru_surname", type="string", nullable=true)
     */
    private $ruSurname;

    /**
     * Father name
     * @var string | null
     *
     * @ORM\Column(name="ru_patronymic", type="string", nullable=true)
     */
    private $ruPatronymic;

    /**
     * @var string | null
     *
     * @ORM\Column(name="ua_full_name", type="string", nullable=true)
     */
    private $uaFullName;

    /**
     * @var int | null
     *
     * @ORM\Column(name="ua_last_four_digits", type="integer", nullable=true)
     */
    private $uaLastFourDigits;

    /**
     * @var string | null
     *
     * @ORM\Column(name="ua_phone", type="string", nullable=true)
     */
    private $uaPhone;

    /**
     * @var string | null
     *
     * @ORM\Column(name="ru_region", type="string", nullable=true)
     */
    private $ruRegion;

    /**
     * @var string | null
     *
     * @ORM\Column(name="ru_local_account", type="string", nullable=true)
     */
    private $ruLocalAccount;

    /**
     * @var string | null
     *
     * @ORM\Column(name="ru_local_bank_code", type="string", nullable=true)
     */
    private $ruLocalBankCode;

    /**
     * @var string | null
     *
     * @ORM\Column(name="ru_card_number", type="string", nullable=true)
     */
    private $ruCardNumber;

    /**
     * Constructor.
     *
     * @param Customer $customer
     * @param string $accountType
     */
    public function __construct(Customer $customer, string $accountType)
    {
        if (!in_array($accountType, [self::TYPE_RU_CARD, self::TYPE_RU_LOCAl, self::TYPE_UA])) {
            throw new \InvalidArgumentException('Invalid transferwise account type');
        }

        $this->accountType = $accountType;
        $this->customer = $customer;
        $customer->setTransferWiseAccount($this);

    }

    /**
     * @return int
     */
    public function getId(): ?int
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
     * @return string
     */
    public function getAccountType(): string
    {
        return $this->accountType;
    }

    /**
     * @param string $accountType
     */
    public function setAccountType(string $accountType): void
    {
        $this->accountType = $accountType;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     */
    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string|null
     */
    public function getZip(): ?string
    {
        return $this->zip;
    }

    /**
     * @param string|null $zip
     */
    public function setZip(?string $zip): void
    {
        $this->zip = $zip;
    }

    /**
     * @return string|null
     */
    public function getRuFirstName(): ?string
    {
        return $this->ruFirstName;
    }

    /**
     * @param string|null $ruFirstName
     */
    public function setRuFirstName(?string $ruFirstName): void
    {
        $this->ruFirstName = $ruFirstName;
    }

    /**
     * @return string|null
     */
    public function getRuSurname(): ?string
    {
        return $this->ruSurname;
    }

    /**
     * @param string|null $ruSurname
     */
    public function setRuSurname(?string $ruSurname): void
    {
        $this->ruSurname = $ruSurname;
    }

    /**
     * @return string|null
     */
    public function getRuPatronymic(): ?string
    {
        return $this->ruPatronymic;
    }

    /**
     * @param string|null $ruPatronymic
     */
    public function setRuPatronymic(?string $ruPatronymic): void
    {
        $this->ruPatronymic = $ruPatronymic;
    }

    /**
     * @return string|null
     */
    public function getUaFullName(): ?string
    {
        return $this->uaFullName;
    }

    /**
     * @param string|null $uaFullName
     */
    public function setUaFullName(?string $uaFullName): void
    {
        $this->uaFullName = $uaFullName;
    }

    /**
     * @return int|null
     */
    public function getUaLastFourDigits()
    {
        return $this->uaLastFourDigits;
    }

    /**
     * @param string|null $uaLastFourDigits
     */
    public function setUaLastFourDigits(?string $uaLastFourDigits): void
    {
        $this->uaLastFourDigits = $uaLastFourDigits;
    }

    /**
     * @return string|null
     */
    public function getUaPhone(): ?string
    {
        return $this->uaPhone;
    }

    /**
     * @param string|null $uaPhone
     */
    public function setUaPhone(?string $uaPhone): void
    {
        $this->uaPhone = $uaPhone;
    }

    /**
     * @return string|null
     */
    public function getRuRegion(): ?string
    {
        return $this->ruRegion;
    }

    /**
     * @param string|null $ruRegion
     */
    public function setRuRegion(?string $ruRegion): void
    {
        $this->ruRegion = $ruRegion;
    }

    /**
     * @return string|null
     */
    public function getRuLocalAccount(): ?string
    {
        return $this->ruLocalAccount;
    }

    /**
     * @param string|null $ruLocalAccount
     */
    public function setRuLocalAccount(?string $ruLocalAccount): void
    {
        $this->ruLocalAccount = $ruLocalAccount;
    }

    /**
     * @return string|null
     */
    public function getRuLocalBankCode(): ?string
    {
        return $this->ruLocalBankCode;
    }

    /**
     * @param string|null $ruLocalBankCode
     */
    public function setRuLocalBankCode(?string $ruLocalBankCode): void
    {
        $this->ruLocalBankCode = $ruLocalBankCode;
    }

    /**
     * @return string|null
     */
    public function getRuCardNumber(): ?string
    {
        return $this->ruCardNumber;
    }

    /**
     * @param string|null $ruCardNumber
     */
    public function setRuCardNumber(?string $ruCardNumber): void
    {
        $this->ruCardNumber = $ruCardNumber;
    }
}
