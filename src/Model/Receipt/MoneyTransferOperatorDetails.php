<?php

namespace PayKassa\Model\Receipt;

use PayKassa\BaseRequest;

class MoneyTransferOperatorDetails extends BaseRequest
{
    public function __construct(
        public ?array $phones = null,
        public ?string $name = null,
        public ?string $address = null,
        public ?string $inn = null,
    ) {
    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        return [
            'phones'  => $this->phones ? json_encode($this->phones) : null,
            'name'    => $this->name,
            'address' => $this->address,
            'inn'     => $this->inn,
        ];
    }
}
