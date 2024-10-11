<?php

declare(strict_types=1);

namespace App\Application\Actions\Customer;

use Psr\Http\Message\ResponseInterface as Response;

/**
 * ListCustomersAction will be used when listinga ll customerss along with search options
 */
class ListCustomersAction extends CustomerAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        // Read the query parameters
        $queryParams = $this->request->getQueryParams();
        
        $customers = $this->customerRepository->findAll($queryParams);

        $this->logger->info("Customers list was viewed.");

        return $this->respondWithData($customers)
            ->withHeader('Content-Type', 'json');
    }
}
