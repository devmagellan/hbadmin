<?php

declare(strict_types = 1);

namespace HB\AdminBundle\Entity\Teachable;

use Doctrine\ORM\Mapping as ORM;

/**
 * WebHook
 *
 * @ORM\Table(name="teachable_transaction_refunded")
 * @ORM\Entity()
 */
class TeachableTransactionRefunded
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
     * @ORM\Column(name="amount_refunded", type="bigint", nullable=true)
     */
    private $amountRefunded;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="imported_from_csv", type="boolean", nullable=false)
     */
    private $importedFromCsv = false;

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
     * @return TeachableTransactionRefunded
     */
    public static function fromCsvData(array $data)
    {
        return new self($data);
    }

    /**
     * @param array                               $data
     * @param TeachableTransactionRefunded | null $current
     *
     * @return TeachableTransactionRefunded
     *
     * @throws \Exception
     */
    public static function fromWebhook(array $data, TeachableTransactionRefunded $current = null): TeachableTransactionRefunded
    {
        $preparedProperties = [];

        $preparedProperties['createdAt'] = new \DateTime($data['created']);
        $preparedProperties['hookEventId'] = $data['hook_event_id'];

        $preparedProperties['transactionId'] = $data['object']['id'];
        $preparedProperties['amountRefunded'] = $data['object']['amount_refunded'];

        if ($current) {
            $current->update($preparedProperties);
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
    public function getId(): int
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
    public function getTransactionId()
    {
        return (int) $this->transactionId;
    }

    /**
     * @return int
     */
    public function getAmountRefunded(): int
    {
        return (int) $this->amountRefunded ?: 0;
    }
}
