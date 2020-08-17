<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Validator;


use HB\AdminBundle\Entity\Customer;
use HB\AdminBundle\Entity\CustomerPaymentAccount;

class PaymentAccountValidator
{
    /**
     * @param CustomerPaymentAccount $account
     * @param Customer $customer
     *
     * @return bool
     */
    public static function isValid(CustomerPaymentAccount $account, Customer $customer): bool
    {
        if (self::isValidBank($account) || self::isValidPayoneer($account) || self::isValidPaypal($account) || $customer->getTransferWiseAccount()) {
            return true;
        }

        return false;
    }

    /**
     * @param CustomerPaymentAccount $account
     *
     * @return bool
     */
    private static function isValidBank(CustomerPaymentAccount $account): bool
    {
        if (
            $account->getBankAccountCode() &&
            $account->getBankAddress() &&
            $account->getBankCorrespondent() &&
            $account->getBankCustomerName() &&
            $account->getBankName() &&
            $account->getBankSwiftCode()
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param CustomerPaymentAccount $account
     *
     * @return bool
     */
    private static function isValidPayoneer(CustomerPaymentAccount $account)
    {
        return (bool) $account->getPayoneerEmail();
    }

    /**
     * @param CustomerPaymentAccount $account
     *
     * @return bool
     */
    private static function isValidPaypal(CustomerPaymentAccount $account)
    {
        return (bool) $account->getPaypalEmail();
    }
}