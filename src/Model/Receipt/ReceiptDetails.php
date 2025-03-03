<?php

namespace PayKassa\Model\Receipt;

use PayKassa\BaseRequest;

class ReceiptDetails extends BaseRequest
{
    public function __construct(
        public ?ClientDetails $client = null,
        public ?CompanyDetails $company = null,
        public ?AgentInfoDetails $agent_info = null,
        public ?SupplierInfoDetails $supplier_info = null,
        public ReceiptItems $items = new ReceiptItems(),
        public ReceiptPayments $payments = new ReceiptPayments(),
        public ReceiptVats $vats = new ReceiptVats(),
        public ?float $total = null,
        public ?string $additional_check_props = null,
        public ?string $cashier = null,
        public ?AdditionalUserPropsDetails $additional_user_props = null,
    ) {
    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        return [
            'client'                 => $this->client->makeRequest(),
            'company'                => $this->company->makeRequest(),
            'agent_info'             => $this->agent_info?->makeRequest(),
            'supplier_info'          => $this->supplier_info?->makeRequest(),
            'items'                  => $this->items->makeRequest(),
            'payments'               => $this->payments->makeRequest(),
            'vats'                   => $this->vats->makeRequest(),
            'total'                  => $this->total !== null
                ? number_format($this->total, 2, '.', '')
                : null,
            'additional_check_props' => $this->additional_check_props,
            'cashier'                => $this->cashier,
            'additional_user_props'  => $this->additional_user_props,
        ];
    }

    public function getInteractionObject($fieldName = ''): ?object
    {
        return match ($fieldName) {
            'client' => new ClientDetails(),
            'company' => new CompanyDetails(),
            'agent_info' => new AgentInfoDetails(),
            'supplier_info' => new SupplierInfoDetails(),
            'items' => new ReceiptItems(),
            'payments' => new ReceiptPayments(),
            'vats' => new ReceiptVats(),
            'additional_user_props' => new AdditionalUserPropsDetails(),
            default => null,
        };
    }
}

