<?php

declare(strict_types=1);

namespace Tests\Application\Actions\Customer;

use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\Customer\CustomerDetailsIncompleteException;
use App\Domain\Customer\CustomerEmailIncompleteException;
use App\Domain\Customer\CustomerRepository;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

class AddCustomerActionTest extends TestCase
{
    public function testActionThrowsCustomerEmailIncompleteException()
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
        $postData = [
            'name' => 'abc',
            'email' => 'abc',
            'phone_number' => '123456789'
        ];
        $custRepositoryProphecy
            ->create($postData)
            ->willThrow(new CustomerEmailIncompleteException())
            ->shouldBeCalledOnce();

        $container->set(CustomerRepository::class, $custRepositoryProphecy->reveal());

        $request = $this->createRequest('POST', '/customers');
        $request = $request->withParsedBody($postData);
        //$request->getBody()->rewind();
        $response = $app->handle($request);
        $response->getBody()->rewind();
        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::SERVER_ERROR, 'Invalid email.');
        $expectedPayload = new ActionPayload(500, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }

    public function testActionThrowsCustomerDetailsIncompleteException()
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
        $postData = [
            'name' => 'abc',
        ];
        $custRepositoryProphecy
            ->create($postData)
            ->willThrow(new CustomerDetailsIncompleteException())
            ->shouldBeCalledOnce();

        $container->set(CustomerRepository::class, $custRepositoryProphecy->reveal());

        $request = $this->createRequest('POST', '/customers');
        $request = $request->withParsedBody($postData);
        //$request->getBody()->rewind();
        $response = $app->handle($request);
        $response->getBody()->rewind();
        $payload = (string) $response->getBody();
        $expectedError = new ActionError(ActionError::SERVER_ERROR, 'Name, email & phone number are all required fields for a customer record.');
        $expectedPayload = new ActionPayload(500, null, $expectedError);
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        $this->assertEquals($serializedPayload, $payload);
    }
}