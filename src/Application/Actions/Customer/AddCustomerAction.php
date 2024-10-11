<?php

declare(strict_types=1);

namespace App\Application\Actions\Customer;

use App\Domain\Customer\CustomerDetailsIncompleteException;
use App\Domain\Customer\CustomerEmailIncompleteException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * AddCustomerAction will be used when creating a customer
 */
class AddCustomerAction extends CustomerAction
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
        
        $this->customerRepository->create(
            $postData['name'], 
            $postData['email'], 
            $postData['phone_number']
        );

        $this->logger->info("Customer was added.");

        return $this->respondWithData('Customer details has been successfully inserted.')
            ->withHeader('Content-Type', 'json');
    }
}
