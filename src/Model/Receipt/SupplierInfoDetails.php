<?php

namespace PayKassa\Model\Receipt;

use PayKassa\BaseRequest;

class SupplierInfoDetails extends BaseRequest
{
    public function __construct(
        public ?array $phones = null,
        public ?string $name = null,
        public ?string $inn = null,
    ) {
    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        return [
            'phones' => $this->phones,
            'name'   => $this->name,
            'inn'    => $this->inn,
        ];
    }
}
