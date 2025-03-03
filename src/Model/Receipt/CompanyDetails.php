<?php

namespace PayKassa\Model\Receipt;

use PayKassa\BaseRequest;
use PayKassa\Enum\Receipt\SnoType;

class CompanyDetails extends BaseRequest
{
    public function __construct(
        public ?string $email = null,
        public ?SnoType $sno = null,
        public ?string $inn = null,
        public ?string $payment_address = null,
    ) {
    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        return [
            'email'           => $this->email,
            'sno'             => $this->sno?->value,
            'inn'             => $this->inn,
            'payment_address' => $this->payment_address,
        ];
    }
}
