<?php

namespace PayKassa\Model\Receipt;

use PayKassa\BaseRequest;

class ClientDetails extends BaseRequest
{
    public function __construct(
        public ?string $name = null,
        public ?string $inn = null,
        public ?string $email = null,
        public ?string $phone = null,
    ) {
    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        return [
            'name'  => $this->name,
            'inn'   => $this->inn,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
