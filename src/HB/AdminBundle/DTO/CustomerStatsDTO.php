<?php

declare(strict_types = 1);


namespace HB\AdminBundle\DTO;


use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPayment;
use HB\AdminBundle\Entity\Teachable\TeachableTransaction;

class CustomerStatsDTO
{
    /**
     * @var Customer
     */
    private $customer;

    /**
     * @var float
     */
    private $income = 0.0;

    /**
     * @var float
     */
    private $paid = 0.0;

    /**
     * CustomerStatsDTO constructor.
     *
     * @param Customer $customer
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * In cents
     *
     * @param TeachableTransaction $teachableTransaction
     */
    public function increaseIncome(TeachableTransaction $teachableTransaction)
    {
        $this->income += $teachableTransaction->getIncomeAmount();
    }

    /**
     * @param CustomerPayment $payment
     */
    public function increasePaid(CustomerPayment $payment)
    {
        $this->paid += $payment->getAmount();
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->getIncome() - $this->paid;
    }

    /**
     * @return float
     */
    public function getIncome(): float
    {
        return round($this->income /100, 2);
    }

    /**
     * @return float
     */
    public function getPaid(): float
    {
        return $this->paid;
    }
}