<?php

namespace PayKassa\Enum;

enum PKMethodsEnum: string
{
    /**
     * Create receipt
     */
    case CREATE_RECEIPT = 'ca/v1/check/merchant/%s';

    /**
     * Check receipt
     */
    case CHECK_RECEIPT = 'ca/v1/check/%s';
}
