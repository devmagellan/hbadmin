<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity\Teachable;

use Doctrine\ORM\Mapping as ORM;
use HB\AdminBundle\Entity\Course;
use HB\AdminBundle\Entity\Types\CustomerPacketType;

/**
 * WebHook
 *
 * @ORM\Table(name="teachable_transaction")
 * @ORM\Entity(repositoryClass="HB\AdminBundle\Repository\TeachableTransactionRepository")
 */
class TeachableTransaction implements TeachableCourseStudentInterface
{
    /** external */
    public const TRANSACTION_STATUS_PAID = 'paid';

    /** internal */
    public const TRANSACTION_STATUS_REFUNDED = 'refunded';

    public const TRANSACTION_COMMISSION_PERCENT = 0.029; //  % / 100
    public const TRANSACTION_COMMISSION_SURCHARGE = 0.3; //  $

    public const TRANSACTION_COMMISSION_PERCENT_PROFESSIONAL = 6;
    public const TRANSACTION_COMMISSION_PERCENT_PREMIUM = 10;
    public const TRANSACTION_COMMISSION_PERCENT_CUSTOM = 0;
    public const TRANSACTION_COMMISSION_PERCENT_VIP = 15;
    public const TRANSACTION_COMMISSION_PERCENT_ZERO = 0;

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
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="hook_event_id", type="integer", nullable=false)
     */
    private $hookEventId;

    /**
     * @var int
     *
     * @ORM\Column(name="transaction_id", type="integer", nullable=false)
     */
    private $transactionId;

    /**
     * @var int
     *
     * @ORM\Column(name="final_price", type="bigint", nullable=false)
     */
    private $finalPrice = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", nullable=false)
     */
    private $currency = 'USD';

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = self::TRANSACTION_STATUS_PAID;

    /**
     * @var int
     *
     * @ORM\Column(name="affiliate_fees", type="bigint", nullable=false)
     */
    private $affiliate_fees = 0;

    /**
     * @var int | null
     *
     * @ORM\Column(name="is_chargeback", type="integer", nullable=true)
     */
    private $is_chargeback;

    /**
     * @var int | null
     *
     * @ORM\Column(name="author_id", type="integer", nullable=true)
     */
    private $author_id;

    /**
     * @var int
     *
     * @ORM\Column(name="course_id", type="integer", nullable=false)
     */
    private $course_id;

    /**
     * @var string
     *
     * @ORM\Column(name="course_name", type="string", nullable=false)
     */
    private $course_name;

    /**
     * @var string | null
     *
     * @ORM\Column(name="product_plan", type="string", nullable=true)
     */
    private $product_plan;

    /**
     * @var string
     *
     * @ORM\Column(name="student_name", type="string", nullable=false)
     */
    private $studentName;

    /**
     * @var string
     *
     * @ORM\Column(name="student_email", type="string", nullable=false)
     */
    private $studentEmail;

    /**
     * @var string | null
     *
     * @ORM\Column(name="affiliate_name", type="string", nullable=true)
     */
    private $affiliateName;

    /**
     * @var Course | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Course")
     * @ORM\JoinColumn(name="internal_course_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $internalCourse;

    /**
     * @var TeachableCourseStudent | null
     *
     * @ORM\ManyToOne(targetEntity="HB\AdminBundle\Entity\Teachable\TeachableCourseStudent")
     * @ORM\JoinColumn(name="teachable_student_id", referencedColumnName="id", nullable=true)
     */
    private $teachableStudent;

    /**
     * @var int
     *
     * @ORM\Column(name="income", type="bigint", nullable=false)
     */
    private $income = 0;

    /**
     * @var int | null
     *
     * @ORM\Column(name="refund_amount", type="bigint", nullable=true)
     */
    private $refundAmount = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="imported_from_csv", type="boolean", nullable=false)
     */
    private $importedFromCsv = false;

    /**
     * @var int
     *
     * @ORM\Column(name="platform_commission", type="bigint", nullable=false)
     */
    private $platformCommission = 0;

    /**
     * TeachableTransaction constructor.
     *
     * @param array $data
     */
    private function __construct(array $data)
    {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Update existed transaction
     *
     * @param array $data
     */
    public function update(array $data)
    {
        foreach ($data as $property => $value) {
            if (property_exists($this, $property)) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Create transaction from CSV data
     *
     * @param array $data
     *
     * @return TeachableTransaction
     */
    public static function fromCsvData(array $data)
    {
        return new self($data);
    }

    /**
     * @param array                $data
     * @param TeachableTransaction $current
     * @param bool                 $isRefunded
     *
     * @return TeachableTransaction
     * @throws \Exception
     */
    public static function fromWebhook(array $data, TeachableTransaction $current = null, bool $isRefunded = false): TeachableTransaction
    {
        $preparedProperties = [];

        $preparedProperties['createdAt'] = new \DateTime($data['created']);
        $preparedProperties['hookEventId'] = $data['hook_event_id'];

        $preparedProperties['transactionId'] = $data['object']['id'];
        $preparedProperties['finalPrice'] = $data['object']['final_price'];
        $preparedProperties['currency'] = $data['object']['currency'];

        if (!$isRefunded) {
            $preparedProperties['status'] = $data['object']['status'] ?: self::TRANSACTION_STATUS_PAID;
        }

        $preparedProperties['affiliate_fees'] = $data['object']['affiliate_fees'];
        $preparedProperties['is_chargeback'] = $data['object']['is_chargeback'];

        $preparedProperties['author_id'] = $data['object']['author_id'];

        $preparedProperties['course_id'] = $data['object']['sale']['course']['id'];
        $preparedProperties['course_name'] = $data['object']['sale']['course']['name'];

        $preparedProperties['product_plan'] = $data['object']['sale']['product']['name'];

        $preparedProperties['studentName'] = $data['object']['user']['name'];
        $preparedProperties['studentEmail'] = $data['object']['user']['email'];

        if (is_array($data['object']['sale']['affiliate'])) {
            $preparedProperties['affiliateName'] = $data['object']['sale']['affiliate']['name'].'('.$data['object']['sale']['affiliate']['email'].')';
        }

        if ($current) {
            $current->update($preparedProperties);

            return $current;
        }


        return new self($preparedProperties);
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getHookEventId(): int
    {
        return $this->hookEventId;
    }

    /**
     * @return int
     */
    public function getTransactionId(): int
    {
        return $this->transactionId;
    }

    /**
     * @return int
     */
    public function getFinalPrice()
    {
        return (int) $this->finalPrice;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getAffiliateFees(): int
    {
        return (int) ($this->isRefunded() ? 0 : $this->affiliate_fees);
    }

    /**
     * @return int|null
     */
    public function getisChargeback(): ?int
    {
        return $this->is_chargeback;
    }

    /**
     * @return int | null
     */
    public function getAuthorId(): ?int
    {
        return $this->author_id;
    }

    /**
     * @return int
     */
    public function getCourseId(): int
    {
        return $this->course_id;
    }

    /**
     * @return string | null
     */
    public function getCourseName(): ?string
    {
        return $this->course_name;
    }

    /**
     * @return string|null
     */
    public function getProductPlan(): ?string
    {
        return $this->product_plan;
    }

    /**
     * @return string
     */
    public function getStudentName(): string
    {
        return $this->studentName;
    }

    /**
     * @return string
     */
    public function getStudentEmail(): string
    {
        return $this->studentEmail;
    }

    /**
     * @return string|null
     */
    public function getAffiliateName(): ?string
    {
        return $this->affiliateName;
    }

    /**
     * @return Course|null
     */
    public function getInternalCourse(): ?Course
    {
        return $this->internalCourse;
    }

    /**
     * @param Course|null $internalCourse
     */
    public function setInternalCourse(?Course $internalCourse): void
    {
        $this->internalCourse = $internalCourse;
        $this->updateIncome();
    }

    /**
     * @return bool
     */
    public function isRefunded(): bool
    {
        return $this->status === self::TRANSACTION_STATUS_REFUNDED;
    }

    public function refund(TeachableTransactionRefunded $transactionRefunded): void
    {
        $this->status = self::TRANSACTION_STATUS_REFUNDED;
        $this->refundAmount = $transactionRefunded->getAmountRefunded();
        $this->updateIncome();
    }

    /**
     * In cents !
     *
     * @return int
     */
    public function getPaymentCommission(): int
    {
        $isRefundedFull = $this->finalPrice === $this->refundAmount ? true : false;

        $sum = $isRefundedFull
            ? 0
            : (int) (round(($this->finalPrice - $this->refundAmount) * self::TRANSACTION_COMMISSION_PERCENT) + self::TRANSACTION_COMMISSION_SURCHARGE * 100);

        return $sum;
    }

    /**
     * @return int
     */
    public function getPlatformCommissionPercent(): int
    {
        // Индивидуальный - 0, Про-6%, Премиум-10%, VIP-15%
        $percent = 0;

        $isRefundedFull = $this->finalPrice === $this->refundAmount ? true : false;
        $owner = $this->internalCourse ? $this->internalCourse->getOwner() : null;

        if ($this->internalCourse && !$isRefundedFull && !$owner->hasPacketSubscription()) {

            $packet = $this->internalCourse->getOwner()->getPacket()->getType();

            if (CustomerPacketType::PROFESSIONAL === $packet) {
                $percent = self::TRANSACTION_COMMISSION_PERCENT_PROFESSIONAL;
            } else if (CustomerPacketType::CUSTOM === $packet) {
                $percent = self::TRANSACTION_COMMISSION_PERCENT_CUSTOM;
            } else if (CustomerPacketType::PREMIUM === $packet) {
                $percent = self::TRANSACTION_COMMISSION_PERCENT_PREMIUM;
            } else if (CustomerPacketType::VIP === $packet) {
                $percent = self::TRANSACTION_COMMISSION_PERCENT_VIP;
            } else if (CustomerPacketType::ONLINE_SCHOOL === $packet || CustomerPacketType::WEBINAR) {
                $percent = self::TRANSACTION_COMMISSION_PERCENT_ZERO;
            }
        }

        return $percent;
    }

    /**
     * @return int
     */
    public function getPlatformCommission()
    {
        return $this->platformCommission;
    }

    /**
     * Доход
     *
     * @return int
     */
    public function getIncomeAmount()
    {
        $refundAmount = (int) $this->refundAmount ?: 0;
        $finalPrice = (int) $this->finalPrice;

        if ($refundAmount && $refundAmount === $finalPrice) {
            $incomeAmount = 0;
        } else if ($refundAmount && $refundAmount < $finalPrice) {
            $incomeAmount = $finalPrice - $this->getPaymentCommission() - $this->getPlatformCommission() - $this->getAffiliateFees() - $refundAmount;
        } else {
            $incomeAmount = $finalPrice - $this->getPaymentCommission() - $this->getPlatformCommission() - $this->getAffiliateFees();
        }


        return $incomeAmount;
    }

    /**
     * @return int
     */
    public function getIncome(): int
    {
        return (int) $this->income ?: 0;
    }

    /**
     * Recalculate income amount
     */
    public function updateIncome()
    {
        $this->updatePlatformCommission();
        $this->income = $this->getIncomeAmount();
    }

    /**
     * Recalculate current platform commission
     */
    private function updatePlatformCommission()
    {
        $this->platformCommission = (int) ($this->getPlatformCommissionPercent() * ($this->finalPrice - $this->refundAmount) / 100);
    }

    /**
     * Get author name
     *
     * @return string
     */
    public function getAuthor()
    {
        $str = '-';

        if ($this->internalCourse) {
            $author = $this->internalCourse->getOwner();
            $str = $author->getFirstName().' '.$author->getSurname().' ['.$author->getRole().' id: '.$author->getId().']';
        }

        return $str;
    }

    /**
     * @return TeachableCourseStudent | null
     */
    public function getTeachableStudent(): ?TeachableCourseStudent
    {
        return $this->teachableStudent;
    }

    /**
     * @param TeachableCourseStudent $teachableStudent
     */
    public function setTeachableStudent(TeachableCourseStudent $teachableStudent): void
    {
        $this->teachableStudent = $teachableStudent;
    }

    /**
     * @return int
     */
    public function getRefundAmount(): int
    {
        return (int) $this->refundAmount;
    }

    /**
     * @return bool
     */
    public function isImportedFromCsv(): bool
    {
        return $this->importedFromCsv;
    }
}
