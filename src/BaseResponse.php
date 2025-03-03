<?php

namespace PayKassa;

use Psr\Http\Message\ResponseInterface;
use ReflectionException;

class BaseResponse extends BaseInteraction
{
    /**
     * @throws ReflectionException
     */
    public function fillByResponse(ResponseInterface $response): self
    {
        $responseContent = json_decode($response->getBody(), true);
        $this->fill($responseContent);

        return $this;
    }
}
