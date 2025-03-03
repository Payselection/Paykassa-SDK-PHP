<?php

namespace PayKassa\Model\Receipt;

use PayKassa\BaseRequest;

class PaymentDetails extends BaseRequest
{
    public function __construct(
        public ?int $type = null,
        public ?float $sum = null,
    ) {
    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        return [
            'type' => $this->type,
            'sum'  => $this->sum !== null
                ? number_format($this->sum, 2, '.', '')
                : null,
        ];
    }
}
