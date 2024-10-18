<?php

declare(strict_types=1);

namespace App\Application\Actions\Customer;

use App\Application\Actions\ActionPayload;
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

        $data = [
            'message' => 'Customer successfully deleted.',
        ];
        $payload = new ActionPayload(204, $data);

        return $this->respond($payload)
            ->withHeader('Content-Type', 'json');
    }
}
