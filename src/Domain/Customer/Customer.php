<?php

namespace App\Domain\Customer;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'customers')]
final class Customer
{
    /**
     * Fields
     */
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Column(type: 'string', nullable: false)]
    private string $name;

    #[Column(type: 'string', unique: true, nullable: false)]
    private string $email;

    #[Column(type: 'string', nullable: false)]
    private string $phone_number;
    /**
     * End - Fields
     */

    public function __construct(string $name, string $email, string $phone_number)
    {
        $this->name = $name;
        $this->email = $email;
        $this->phone_number = $phone_number;
    }

    /**
     * Getters
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhoneNumber(): string
    {
        return $this->phone_number;
    }
    /**
     * End - Getters
     */

     /**
      * Setters
      */
    public function setName(string $name): string
    {
        return $this->name = $name;
    }

    public function setEmail(string $email): string
    {
        return $this->email = $email;
    }

    public function setPhoneNumber(string $phoneNumber): string
    {
        return $this->phone_number = $phoneNumber;
    }
    /**
     * End - Setters
     */
}