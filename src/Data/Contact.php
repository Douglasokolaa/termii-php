<?php

namespace Okolaa\TermiiPHP\Data;

use Okolaa\TermiiPHP\Data\Contracts\ConvertsArrayToDTO;

class Contact implements ConvertsArrayToDTO
{
    public function __construct(
        public readonly string  $phoneNumber,
        public readonly string  $email,
        public readonly string  $firstName,
        public readonly string  $lastName,
        public readonly string  $companyName,
        public readonly ?int    $id = null,
        public readonly ?string $countryCode = null,
        public readonly ?string $created = null,
        public readonly ?string $updated = null,
    )
    {
    }

    public static function fromArray(array $data): Contact
    {
        return new self(
            phoneNumber: $data['phone_number'],
            email: $data['email_address'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            companyName: $data['company_name'],
            id: $data['id'],
            countryCode: $data['country_code'],
            created: $data['create_at'],
            updated: $data['updated_at']
        );
    }

    public function toRequestArray(): array
    {
        return [
            'phonebook_name' => $this->phoneNumber,
            'email_address' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'company' => $this->companyName,
            'country_code' => $this->countryCode,
        ];
    }
}