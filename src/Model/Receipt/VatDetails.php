<?php

namespace PayKassa\Model\Receipt;

use PayKassa\BaseRequest;
use PayKassa\Enum\Receipt\VatType;

class VatDetails extends BaseRequest
{
    public function __construct(
        public ?VatType $type = null,
        public ?float $sum = null,
    ) {
    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        return [
            'type' => $this->type?->value,
            'sum'  => $this->sum !== null
                ? number_format($this->sum, 2, '.', '')
                : null,
        ];
    }
}
