<?php

declare(strict_types = 1);


namespace HB\AdminBundle\DTO;


class StatsDTO
{
    /**
     * Общая сумма покупок
     *
     * @var int
     */
    private $sales = 0;

    /**
     * Общий доход
     *
     * @var int
     */
    private $income = 0;

    /**
     * Выплаты
     *
     * @var int
     */
    private $payments = 0;

    /**
     * Возвраты
     *
     * @var int
     */
    private $refunds = 0;

    /**
     * StatsDTO constructor.
     *
     * @param int $sales
     * @param int $income
     * @param int $payments
     * @param int $refunds
     */
    public function __construct(int $sales, int $income, int $payments, int $refunds)
    {
        $this->sales = $sales;
        $this->income = $income;
        $this->payments = $payments;
        $this->refunds = $refunds;
    }

    /**
     * @return int
     */
    public function getSales(): int
    {
        return $this->sales;
    }

    /**
     * @return int
     */
    public function getIncome(): int
    {
        return $this->income;
    }

    /**
     * @return int
     */
    public function getPayments(): int
    {
        return $this->payments;
    }

    /**
     * @return int
     */
    public function getRefunds(): int
    {
        return $this->refunds;
    }

    /**
     * @return int
     */
    public function getBalance(): int
    {
        return (int) ($this->income - $this->payments);
    }

}