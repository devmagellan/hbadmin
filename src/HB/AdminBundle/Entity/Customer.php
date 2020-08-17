<?php

namespace HB\AdminBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Component\Model\Email;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Customer
 *
 * @ORM\Table(name="customer")
 * @ORM\Entity(repositoryClass="HB\AdminBundle\Repository\CustomerRepository")
 * @UniqueEntity("email")
 */
class Customer implements AdvancedUserInterface, \Serializable
{
    public const PACKET_SUBSCRIPTION_NONE = 0;
    public const PACKET_SUBSCRIPTION_MONTH = 1;
    public const PACKET_SUBSCRIPTION_YEAR = 2;

    public const PACKET_SUBSCRIPTIONS = [
        self::PACKET_SUBSCRIPTION_NONE  => 'Нет',
        self::PACKET_SUBSCRIPTION_MONTH => 'Месячная',
        self::PACKET_SUBSCRIPTION_YEAR  => 'Годовая',
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created_at", type="datetime")
     */
    private $dateCreatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var UploadCareFile
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\UploadCareFile", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="file_id", referencedColumnName="id")
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(name="about_info", type="text", nullable=true)
     */
    private $aboutInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="firstName", type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\CustomerRole", inversedBy="customers")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    private $role;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255, nullable=true)
     */
    private $surname;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    private $plainPassword;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\Course", mappedBy="owner")
     */
    private $courses;

    /**
     * @Assert\NotBlank()
     *
     * @var CustomerPacket
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\CustomerPacket", inversedBy="customers")
     * @ORM\JoinColumn(name="packet_id", referencedColumnName="id", nullable=true)
     */
    private $packet;

    /**
     * @var \DateTime | null
     *
     * @ORM\Column(name="packet_until_date", type="datetime", nullable=true)
     */
    private $packetUntilDate;

    /**
     * @var int
     *
     * @ORM\Column(name="packet_subscription", type="smallint", nullable=false)
     */
    private $packetSubscription = 0;

    /**
     * @var CustomerPacket | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\CustomerPacket")
     * @ORM\JoinColumn(name="requested_packet_id", referencedColumnName="id", nullable=true)
     */
    private $requestedPacket;

    /**
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Customer", inversedBy="customers")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $owner;

    /**
     * @var Customer | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Customer")
     * @ORM\JoinColumn(name="producer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $producer;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="HB\AdminBundle\Entity\Customer")
     * @ORM\JoinTable(name="managers_authors",
     *      joinColumns={@ORM\JoinColumn(name="manager_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="author_id", referencedColumnName="id")}
     *      )
     */
    private $authors;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="HB\AdminBundle\Entity\Customer", mappedBy="owner")
     */
    private $customers;

    /**
     * @var CustomerPaymentAccount
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\CustomerPaymentAccount", inversedBy="customer", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="payment_account_id", nullable=true, onDelete="SET NULL")
     */
    private $paymentAccount;

    /**
     * @var CustomerTransferWiseAccount | null
     *
     * @ORM\OneToOne(targetEntity="HB\AdminBundle\Entity\CustomerTransferWiseAccount", inversedBy="customer", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="transferwise_account_id", nullable=true, onDelete="SET NULL")
     */
    private $transferWiseAccount;

    /**
     * @var string | null
     *
     * @ORM\Column(name="password_recovery_hash", type="string", nullable=true)
     */
    private $passwordRecoveryHash;

    /**
     * @var string | null
     *
     * @ORM\Column(name="signup_hash", type="string", nullable=true)
     */
    private $signupHash;

    /**
     * @var string | null
     *
     * @ORM\Column(name="confirm_sms_code", type="string", nullable=true)
     */
    private $confirmSmsCode;

    /**
     * @var bool
     *
     * @ORM\Column(name="zapier_profile_complited_event", type="boolean")
     */
    private $zapierProfileCompletedEvent = false;

    /**
     * @var \DateTime | null
     *
     * @ORM\Column(name="last_activity_at", type="datetime", nullable=true)
     */
    private $lastActivityAt;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->setDateCreatedAt(new \DateTime());
        $this->status = true;
        $this->customers = new ArrayCollection();
        $this->authors = new ArrayCollection();

        $this->paymentAccount = new CustomerPaymentAccount($this);
    }

    /**
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    public function serialize()
    {
        return serialize([
            $this->getId(),
            $this->getUsername(),
            $this->getPassword(),
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * @return CustomerRole | null
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param $roleName
     *
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return in_array($roleName, $this->getRoles());
    }

    public function getRoles()
    {
        /** @var CustomerRole $role */
        $role = $this->role;

        return [$role->getName()];
    }

    /**
     * @return array
     */
    public function getRolesForView()
    {
        return [$this->role];
    }

    /**
     * @param CustomerRole $role
     */
    public function setRole(CustomerRole $role): void
    {
        $this->role = $role;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function eraseCredentials()
    {
    }

    /**
     * @return \DateTime
     */
    public function getDateCreatedAt(): \DateTime
    {
        return $this->dateCreatedAt;
    }

    /**
     * @param \DateTime $dateCreatedAt
     */
    public function setDateCreatedAt(\DateTime $dateCreatedAt)
    {
        $this->dateCreatedAt = $dateCreatedAt;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $surname
     */
    public function setSurname(string $surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return string
     */
    public function getSurname(): ?string
    {
        return $this->surname;
    }

    /**
     * Set phone.
     *
     * @param string|null $phone
     */
    public function setPhone(string $phone = null)
    {
        $this->phone = str_replace([')', '-', ' '], '', $phone);
    }

    /**
     * Get phone.
     *
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return UploadCareFile
     */
    public function getPhoto(): ?UploadCareFile
    {
        return $this->photo;
    }

    /**
     * @param UploadCareFile $photo
     */
    public function setPhoto(UploadCareFile $photo): void
    {
        $this->photo = $photo;
    }

    /**
     * Set aboutInfo.
     *
     * @param string|null $aboutInfo
     *
     * @return Customer
     */
    public function setAboutInfo($aboutInfo = null)
    {
        $this->aboutInfo = $aboutInfo;

        return $this;
    }

    /**
     * Get aboutInfo.
     *
     * @return string|null
     */
    public function getAboutInfo()
    {
        return $this->aboutInfo;
    }

    /**
     * enable Customer
     */
    public function enable(): void
    {
        $this->status = true;
    }

    /**
     * disable Customer
     */
    public function disable(): void
    {
        $this->status = false;
    }

    /**
     * Is Customer enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->status;
    }

    /**
     * @return CustomerPacket
     */
    public function getPacket(): ?CustomerPacket
    {
        return $this->packet;
    }

    /**
     * @param CustomerPacket $packet
     */
    public function setPacket(CustomerPacket $packet): void
    {
        $this->packet = $packet;
    }

    /**
     * @return ArrayCollection
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * @param Course $courses
     */
    public function addCourse(Course $courses): void
    {
        $this->courses[] = $courses;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return $this->isEnabled();
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * @return Customer
     */
    public function getOwner(): ?Customer
    {
        return $this->owner;
    }

    /**
     * @param Customer $owner
     */
    public function setOwner(Customer $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return ArrayCollection
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    /**
     * @return Customer|null
     */
    public function getProducer(): ?Customer
    {
        return $this->producer;
    }

    /**
     * @param Customer|null $producer
     */
    public function setProducer(?Customer $producer): void
    {
        $this->producer = $producer;
    }

    public function __toString()
    {
        return $this->firstName.' '.$this->surname.' ['.$this->id.']';
    }

    /**
     * @return bool
     */
    public function isProducer()
    {
        return $this->hasRole(CustomerRole::ROLE_PRODUCER);
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole(CustomerRole::ROLE_ADMIN);
    }

    /**
     * @return bool
     */
    public function isAuthor()
    {
        return $this->hasRole(CustomerRole::ROLE_AUTHOR);
    }

    /**
     * @return bool
     */
    public function isManager()
    {
        return $this->hasRole(CustomerRole::ROLE_MANAGER);
    }

    /**
     * @param Customer $customer
     */
    public function addAuthor(Customer $customer)
    {
        $this->authors[] = $customer;
    }

    /**
     * @param Customer $customer
     */
    public function removeAuthor(Customer $customer)
    {
        $this->authors->removeElement($customer);
    }

    /**
     * @param Customer $customer
     *
     * @return bool
     */
    public function hasAuthor(Customer $customer)
    {
        return $this->authors->contains($customer);
    }

    /**
     * @return ArrayCollection
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * @return CustomerPacket|null
     */
    public function getRequestedPacket(): ?CustomerPacket
    {
        return $this->requestedPacket;
    }

    /**
     * @param CustomerPacket|null $requestedPacket
     */
    public function setRequestedPacket(?CustomerPacket $requestedPacket): void
    {
        $this->requestedPacket = $requestedPacket;
    }

    /**
     * @return CustomerPaymentAccount | null
     */
    public function getPaymentAccount()
    {
        return $this->paymentAccount;
    }

    /**
     * @param CustomerPaymentAccount $customerPaymentAccount
     */
    public function setPaymentAccount(CustomerPaymentAccount $customerPaymentAccount)
    {
        $this->paymentAccount = $customerPaymentAccount;
    }

    /**
     * @param string|null $passwordRecoveryHash
     */
    public function setPasswordRecoveryHash(?string $passwordRecoveryHash): void
    {
        $this->passwordRecoveryHash = $passwordRecoveryHash;
    }

    /**
     * @return string|null
     */
    public function getSignupHash(): ?string
    {
        return $this->signupHash;
    }

    /**
     * @param string|null $signupHash
     */
    public function setSignupHash(?string $signupHash): void
    {
        $this->signupHash = $signupHash;
    }

    /**
     * @return string|null
     */
    public function getConfirmSmsCode(): ?string
    {
        return $this->confirmSmsCode;
    }

    /**
     * @param string|null $confirmSmsCode
     */
    public function setConfirmSmsCode(?string $confirmSmsCode): void
    {
        $this->confirmSmsCode = $confirmSmsCode;
    }

    /**
     * @return Email
     */
    public function getValidEmail(): Email
    {
        return new Email($this->email);
    }

    /**
     * @return CustomerTransferWiseAccount | null
     */
    public function getTransferWiseAccount(): ?CustomerTransferWiseAccount
    {
        return $this->transferWiseAccount;
    }

    /**
     * @param CustomerTransferWiseAccount $transferWiseAccount
     */
    public function setTransferWiseAccount(?CustomerTransferWiseAccount $transferWiseAccount): void
    {
        $this->transferWiseAccount = $transferWiseAccount;
    }

    /**
     * @return bool
     */
    public function isZapierProfileCompletedEvent(): bool
    {
        return $this->zapierProfileCompletedEvent;
    }

    /**
     * @param bool $zapierProfileCompletedEvent
     */
    public function setZapierProfileCompletedEvent(bool $zapierProfileCompletedEvent): void
    {
        $this->zapierProfileCompletedEvent = $zapierProfileCompletedEvent;
    }

    /**
     * @return int
     */
    public function getPacketSubscription(): int
    {
        return $this->packetSubscription;
    }

    /**
     * @param int $packetSubscription
     */
    public function setPacketSubscription(int $packetSubscription): void
    {
        if (!in_array($packetSubscription, array_keys(self::PACKET_SUBSCRIPTIONS))) {
            throw new \InvalidArgumentException('Неверный тип подписки');
        }

        $this->packetSubscription = $packetSubscription;
    }

    /**
     * Use for calculate transactions percent
     * if has subscription - percent = 0
     *
     * @return bool
     */
    public function hasPacketSubscription()
    {
        return (bool) $this->packetSubscription;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastActivityAt(): ?\DateTime
    {
        return $this->lastActivityAt;
    }

    /**
     * @param \DateTime $lastActivityAt
     */
    public function setLastActivityAt(\DateTime $lastActivityAt): void
    {
        $this->lastActivityAt = $lastActivityAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getPacketUntilDate(): ?\DateTime
    {
        return $this->packetUntilDate;
    }

    /**
     * @param \DateTime|null $packetUntilDate
     */
    public function setPacketUntilDate(?\DateTime $packetUntilDate): void
    {
        $this->packetUntilDate = $packetUntilDate;
    }
}

