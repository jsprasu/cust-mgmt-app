<?php

declare(strict_types=1);

namespace App\Application\Actions\Customer;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * ViewCustomerAction will be used to read customer details
 */
class ViewCustomerAction extends CustomerAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $custId = (int) $this->resolveArg('id');
        $customer = $this->customerRepository->findUserOfId($custId);

        $this->logger->info("Customer of id " . $custId . " was viewed.");

        return $this->respondWithData($customer)
            ->withHeader('Content-Type', 'json');
    }
}
