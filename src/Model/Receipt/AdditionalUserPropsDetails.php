<?php

namespace PayKassa\Model\Receipt;

use PayKassa\BaseRequest;

class AdditionalUserPropsDetails extends BaseRequest
{
    public function __construct(
        public ?string $name = null,
        public ?string $value = null,
    ) {
    }

    /**
     * @return array
     */
    public function makeRequest(): array
    {
        return [
            'name'  => $this->name,
            'value' => $this->value,
        ];
    }
}
