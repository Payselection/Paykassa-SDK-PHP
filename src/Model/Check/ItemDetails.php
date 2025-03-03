<?php

namespace PayKassa\Model\Check;

use PayKassa\BaseInteraction;

class ItemDetails extends BaseInteraction
{
    public ?string $name = null;
    public ?float $price = null;
    public ?float $quantity = null;
    public ?float $sum = null;
    public ?float $nds = null;
    public ?string $taxation_system = null;
    public ?string $payment_method = null;
    public ?int $postpaid = null;
}
