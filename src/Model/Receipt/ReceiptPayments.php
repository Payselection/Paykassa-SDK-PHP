<?php

namespace PayKassa\Model\Receipt;

use PayKassa\Container;

class ReceiptPayments extends Container
{
    /**
     * @param object $item
     *
     * @return void
     */
    public function add(object $item): void
    {
        parent::add($item);
    }

    public function getInteractionObject($fieldName = ''): ?object
    {
        return new PaymentDetails();
    }
}
