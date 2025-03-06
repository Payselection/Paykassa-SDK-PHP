<?php

namespace PayKassa\Model\Receipt;

use PayKassa\BaseRequest;

class ReceivePaymentsOperatorDetails extends BaseRequest
{
    public function __construct(
        public ?array $phones = null,
    ) {

    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        return [
            'phones' => $this->phones,
        ];
    }
}
