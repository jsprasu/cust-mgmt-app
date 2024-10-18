<?php

declare(strict_types=1);

namespace App\Application\Actions\Customer;

use App\Application\Actions\ActionPayload;
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
        
        $customer = $this->customerRepository->create($postData);
        $data = [
            'message' => 'Customer details has been successfully inserted.',
            'customer' => $customer,
        ];
        $payload = new ActionPayload(201, $data);

        $this->logger->info("Customer was added.");

        return $this->respond($payload)
            ->withHeader('Content-Type', 'json');
    }
}
