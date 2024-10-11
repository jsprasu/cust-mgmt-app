<?php

declare(strict_types=1);

namespace App\Domain\Customer;

use Exception;

/**
 * CustomerEmailIncompleteException will be used when email is not complete
 * when creating or updating a customer
 */
class CustomerEmailIncompleteException extends Exception
{
    public $message = 'Invalid email.';
}
