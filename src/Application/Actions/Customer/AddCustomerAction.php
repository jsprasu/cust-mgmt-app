<?php

declare(strict_types=1);

namespace App\Application\Actions\Customer;

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
        
        $this->customerRepository->create($postData);

        $this->logger->info("Customer was added.");

        return $this->respondWithData('Customer details has been successfully inserted.')
            ->withHeader('Content-Type', 'json');
    }
}
