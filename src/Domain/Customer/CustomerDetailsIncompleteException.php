<?php

declare(strict_types=1);

namespace App\Domain\Customer;

use Exception;

/**
 * CustomerDetailsIncompleteException will be used when there are validations errors
 * when creating or updating a customer
 */
class CustomerDetailsIncompleteException extends Exception
{
    public $message = 'Name, email & phone number are all required fields for a customer record.';
}
