<?php

declare(strict_types=1);

namespace App\Domain\Customer;

use App\Domain\DomainException\DomainRecordNotFoundException;

/**
 * CustomerNotFoundException will be used when a customer is not found
 */
class CustomerNotFoundException extends DomainRecordNotFoundException
{
    public $message = 'The customer you requested does not exist.';
}
