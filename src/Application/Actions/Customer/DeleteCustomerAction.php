<?php

declare(strict_types=1);

namespace App\Application\Actions\Customer;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * DeleteCustomerAction will be used when deleting a customer
 */
class DeleteCustomerAction extends CustomerAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $custId = (int) $this->resolveArg('id');
        $customer = $this->customerRepository->findUserOfId($custId, true);

        $this->customerRepository->delete($customer);

        $this->logger->info("Customer of id " . $custId . " was deleted.");

        return $this->respondWithData('Customer successfully deleted.')
            ->withHeader('Content-Type', 'json');
    }
}
