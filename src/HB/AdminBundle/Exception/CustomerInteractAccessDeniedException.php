<?php

declare(strict_types = 1);


namespace HB\AdminBundle\Exception;

/**
 * Throws if some of actions with customer is denied for current customer
 */
class CustomerInteractAccessDeniedException extends \Exception
{
}