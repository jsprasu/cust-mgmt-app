<?php

declare(strict_types=1);

namespace App\Application\Actions\Customer;

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
        // Read the post data
        $postData = $this->request->getParsedBody();

        // Validations
        if (
            !isset($postData['name']) ||
            !isset($postData['email']) ||
            !isset($postData['phone_number']) ||
            empty($postData['name']) ||
            empty($postData['email']) ||
            empty($postData['phone_number'])
            ) {
                throw new CustomerDetailsIncompleteException();
        }

        if (!filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new CustomerEmailIncompleteException();
        }

        $custId = (int) $this->resolveArg('id');

        // Read the customer details
        // and throw exception if customer not found
        $customer = $this->customerRepository->findUserOfId($custId, true);
        
        $this->customerRepository->update(
            $customer,
            $postData['name'], 
            $postData['email'], 
            $postData['phone_number']
        );

        $this->logger->info("Customer was updated.");

        return $this->respondWithData('Customer details has been successfully updated.')
            ->withHeader('Content-Type', 'json');
    }
}
