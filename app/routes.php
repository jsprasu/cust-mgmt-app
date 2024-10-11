<?php

declare(strict_types=1);

use App\Application\Actions\Customer\AddCustomerAction;
use App\Application\Actions\Customer\DeleteCustomerAction;
use App\Application\Actions\Customer\ListCustomersAction;
use App\Application\Actions\Customer\UpdateCustomerAction;
use App\Application\Actions\Customer\ViewCustomerAction;
use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->group('/users', function (Group $group) {
        $group->get('', ListUsersAction::class);
        $group->get('/{id}', ViewUserAction::class);
    });

    /**
     * Customer management app routes
     */
    $app->group('/customers', function (Group $group) {
        // List customers with search options
        $group->get('', ListCustomersAction::class);
        // Read customer details by id
        $group->get('/{id}', ViewCustomerAction::class);
        // Create a customer
        $group->post('', AddCustomerAction::class);
        // update an existing customer by id
        $group->put('/{id}', UpdateCustomerAction::class);
        // Delete customer by id
        $group->delete('/{id}', DeleteCustomerAction::class);
    });
};
