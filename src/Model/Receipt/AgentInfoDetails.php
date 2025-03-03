<?php

namespace PayKassa\Model\Receipt;

use PayKassa\BaseRequest;
use PayKassa\Enum\Receipt\AgentInfoType;

class AgentInfoDetails extends BaseRequest
{
    public function __construct(
        public ?AgentInfoType $type = null,
        public ?PayingAgentDetails $paying_agent = null,
        public ?ReceivePaymentsOperatorDetails $receive_payments_operator = null,
        public ?MoneyTransferOperatorDetails $money_transfer_operator = null,
    ) {
    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        return [
            'type'                      => $this->type?->value,
            'paying_agent'              => $this->paying_agent?->makeRequest(),
            'receive_payments_operator' => $this->receive_payments_operator?->makeRequest(),
            'money_transfer_operator'   => $this->money_transfer_operator?->makeRequest(),
        ];
    }

    public function getInteractionObject($fieldName = ''): ?object
    {
        return match ($fieldName) {
            'paying_agent' => new PayingAgentDetails(),
            'receive_payments_operator' => new ReceivePaymentsOperatorDetails(),
            'money_transfer_operator' => new MoneyTransferOperatorDetails(),
            default => null,
        };
    }
}
