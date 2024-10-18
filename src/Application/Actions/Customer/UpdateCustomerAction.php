<?php

declare(strict_types=1);

namespace App\Application\Actions\Customer;

use App\Application\Actions\ActionPayload;
use App\Domain\Customer\CustomerDetailsIncompleteException;
use App\Domain\Customer\CustomerEmailIncompleteException;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * UpdateCustomerAction will be used when updating an existing customer
 */
class UpdateCustomerAction extends CustomerAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        $custId = (int) $this->resolveArg('id');
        
        // Read the post data
        $postData = $this->request->getParsedBody();

        // Read the customer details
        // and throw exception if customer not found
        $customer = $this->customerRepository->findUserOfId($custId, true);
        $updatedCustomer = $this->customerRepository->update(
            $customer,
            $postData
        );
        $data = [
            'message' => 'Customer details has been successfully updated.',
            'customer' => $updatedCustomer,
        ];
        $payload = new ActionPayload(200, $data);

        $this->logger->info("Customer was updated.");

        return $this->respond($payload)
            ->withHeader('Content-Type', 'json');
    }
}
