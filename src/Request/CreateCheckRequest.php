<?php

namespace PayKassa\Request;

use PayKassa\BaseRequest;
use PayKassa\Enum\Receipt\OperationType;
use PayKassa\Model\Receipt\ReceiptDetails;

class CreateCheckRequest extends BaseRequest
{
    public function __construct(
        public OperationType $operation_type,
        public string $order_number,
        public ReceiptDetails $receipt,
        public ?string $callback_url = null,
        public bool $ism_optional = false,
    ) {
    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        return [
            'operation_type' => $this->operation_type->value,
            'order_number'   => $this->order_number,
            'callback_url'   => $this->callback_url,
            'ism_optional'   => $this->ism_optional,
            'receipt'        => $this->receipt->makeRequest(),
        ];
    }
}
