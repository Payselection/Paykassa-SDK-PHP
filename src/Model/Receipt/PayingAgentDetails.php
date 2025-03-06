<?php

namespace PayKassa\Model\Receipt;

use PayKassa\BaseRequest;

class PayingAgentDetails extends BaseRequest
{
    public function __construct(
        public ?string $operation = null,
        public ?array $phones = null,
    ) {
    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        return [
            'operation' => $this->operation,
            'phones'    => $this->phones,
        ];
    }
}
