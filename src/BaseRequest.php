<?php

namespace PayKassa;

abstract class BaseRequest extends BaseInteraction
{
    public function makeRequest(): array
    {
        return [];
    }
}
