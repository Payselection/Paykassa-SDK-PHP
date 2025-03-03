<?php

namespace PayKassa\Hook;

use PayKassa\BaseHook;
use PayKassa\Model\Receipt\ReceiptDetails;

class HookReceipt extends BaseHook
{
    public ?string $status = null;
    public ?string $download_link = null;
    public ?string $check_id = null;
    public ?string $tran_id = null;
    public ?ReceiptDetails $receipt = null;

    public function getInteractionObject($fieldName = ''): ?object
    {
        return match ($fieldName) {
            'receipt' => new ReceiptDetails(),
            default => null,
        };
    }
}
