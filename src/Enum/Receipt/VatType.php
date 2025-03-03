<?php

namespace PayKassa\Enum\Receipt;

enum VatType: string
{
    case NONE = 'none';
    case VAT0 = 'vat0';
    case VAT10 = 'vat10';
    case VAT110 = 'vat110';
    case VAT20 = 'vat20';
    case VAT120 = 'vat120';
}
