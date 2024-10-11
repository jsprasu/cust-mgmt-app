<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Customer;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Customer\CustomerNotFoundException;
use App\Domain\Customer\CustomerRepository;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class ViewCustomerActionTest extends TestCase
{
    public function testActionThrowsCustomerNotFoundException()
    {
        $app = $this->getAppInstance();

        $callableResolver = $app->getCallableResolver();
        $responseFactory = $app->getResponseFactory();

        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
        $errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, false, false);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        $app->add($errorMiddleware);

        /** @var Container $container */
        $container = $app->getContainer();

        $custRepositoryProphecy = $this->prophesize(CustomerRepository::class);
        $custRepositoryProphecy
            ->findUserOfId(0)
            ->willThrow(new CustomerNotFoundException())
            ->shouldBeCalledOnce();

        $container->set(CustomerRepository::class, $custRepositoryProphecy->reveal());

        $request = $this->createRequest('GET', '/customers/0');
        $response = $app->handle($request);

        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::RESOURCE_NOT_FOUND, 'The customer you requested does not exist.');
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}