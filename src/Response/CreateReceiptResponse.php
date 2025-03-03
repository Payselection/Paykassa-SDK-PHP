<?php

namespace PayKassa\Response;

use PayKassa\BaseResponse;

class CreateReceiptResponse extends BaseResponse
{
    public ?string $status = null;
    public ?string $check_id = null;
}
