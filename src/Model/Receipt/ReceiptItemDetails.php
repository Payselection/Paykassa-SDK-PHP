<?php

namespace PayKassa\Model\Receipt;

use PayKassa\BaseRequest;
use PayKassa\Enum\Receipt\PaymentMethodType;
use PayKassa\Enum\Receipt\PaymentObjectType;

class ReceiptItemDetails extends BaseRequest
{
    public function __construct(
        public ?string $name = null,
        public ?int $price = null,
        public ?float $quantity = null,
        public ?float $sum = null,
        public ?string $measurement_unit = null,
        public ?PaymentMethodType $payment_method = null,
        public ?PaymentObjectType $payment_object = null,
        public ?string $nomenclature_code = null,
        public ?VatDetails $vat = null,
        public ?AgentInfoDetails $agent_info = null,
        public ?SupplierInfoDetails $supplier_info = null,
        public ?string $user_data = null,
        public ?int $excise = null,
        public ?string $country_code = null,
        public ?string $declaration_number = null,
    ) {
    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        return [
            'name'               => $this->name,
            'price'              => $this->price,
            'quantity'           => $this->quantity !== null
                ? number_format($this->quantity, 3, '.', '')
                : null,
            'sum'                => $this->sum !== null
                ? number_format($this->sum, 2, '.', '')
                : null,
            'measurement_unit'   => $this->measurement_unit,
            'payment_method'     => $this->payment_method?->value,
            'payment_object'     => $this->payment_object?->value,
            'nomenclature_code'  => $this->nomenclature_code,
            'vat'                => $this->vat->makeRequest(),
            'agent_info'         => $this->agent_info?->makeRequest(),
            'supplier_info'      => $this->supplier_info?->makeRequest(),
            'user_data'          => $this->user_data,
            'excise'             => $this->excise,
            'country_code'       => $this->country_code,
            'declaration_number' => $this->declaration_number,
        ];
    }

    public function getInteractionObject($fieldName = ''): ?object
    {
        return match ($fieldName) {
            'vat' => new VatDetails(),
            'agent_info' => new AgentInfoDetails(),
            'supplier_info' => new SupplierInfoDetails(),
            default => null,
        };
    }
}
