<?php

namespace PayKassa\Response;

use PayKassa\BaseResponse;
use PayKassa\Model\Check\CheckItems;

class CheckReceiptResponse extends BaseResponse
{
    public ?int $id = null;
    public ?string $doc_number = null;
    public ?string $created_at = null;
    public ?string $check_create_date = null;
    public ?string $turn_number = null;
    public ?string $operation_type = null;
    public ?string $status = null;
    public ?string $kassa_serial_number = null;
    public ?string $merchant_name = null;
    public ?float $total = null;
    public ?string $error_code = null;
    public ?string $error_description = null;
    public ?string $uid = null;
    public ?string $download_link = null;
    public ?string $kassa_registration_number = null;
    public ?int $fiscal_doc_number = null;
    public ?string $fn_serial_number = null;
    public ?string $qr = null;
    public ?string $merchant_inn = null;
    public ?string $merchant_address = null;
    public ?string $merchant_site = null;
    public ?string $email_check_sender = null;
    public ?CheckItems $items = null;
    public ?int $merchant_id = null;
    public ?string $fpd = null;
    public ?string $ffd_version = null;
    public ?string $fns_site = null;
    public ?string $phone_or_email_customer = null;
    public ?string $order_number = null;
    public ?string $payment_address = null;
    public ?string $operator_ofd = null;
    public ?string $correction_date = null;
    public ?string $correction_number = null;

    function __construct()
    {
        $this->items = new CheckItems();
    }

    public function getInteractionObject($fieldName = ''): ?object
    {
        return match ($fieldName) {
            'items' => new CheckItems(),
            default => null,
        };
    }
}
