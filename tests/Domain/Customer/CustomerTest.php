<?php

declare(strict_types=1);

namespace Tests\Domain\Customer;

use App\Domain\Customer\Customer;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    public function customerProvider(): array
    {
        return [
            ['Bill', 'bill@example.com', '0123456789'],
            ['Steve', 'steve@example.com', '0123456789'],
            ['Mark', 'mark@example.com', '0123456789'],
            ['Evan', 'evan@example.com', '0123456789'],
            ['Jack', 'jack@example.com', '0123456789'],
        ];
    }

    /**
     * @dataProvider customerProvider
     * @param string $name
     * @param string $email
     * @param string $phoneNumber
     */
    public function testGetters(string $name, string $email, string $phoneNumber)
    {
        $customer = new Customer($name, $email, $phoneNumber);

        $this->assertEquals($name, $customer->getName());
        $this->assertEquals($email, $customer->getEmail());
        $this->assertEquals($phoneNumber, $customer->getPhoneNumber());
    }

    /**
     * @dataProvider customerProvider
     * @param string $name
     * @param string $email
     * @param string $phoneNumber
     */
    public function testJsonSerialize(string $name, string $email, string $phoneNumber)
    {
        $customer = new Customer($name, $email, $phoneNumber);
        $customerPayload = [
            'name' => $customer->getName(),
            'email' => $customer->getEmail(),
            'phone_number' => $customer->getPhoneNumber(),
        ];

        $expectedPayload = json_encode([
            'name' => $name,
            'email' => $email,
            'phone_number' => $phoneNumber,
        ]);

        $this->assertEquals($expectedPayload, json_encode($customerPayload));
    }
}
