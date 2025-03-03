<?php

namespace PayKassa\Exceptions;

use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class PKResponseException extends BadResponseException
{
    public ?array $errorBody = null;
    public ?array $errorDetails = null;

    public function __construct(
        string $message,
        RequestInterface $request,
        ResponseInterface $response,
        Throwable $previous = null,
        array $handlerContext = []
    ) {
        parent::__construct($message, $request, $response, $previous, $handlerContext);

        if ($response->getBody()) {
            $body = json_decode((string)$response->getBody(), true);

            if (!json_last_error() && is_array($body)) {
                $this->errorBody = $body;
                $this->errorDetails = $body['detail'] ?? null;
            }
        }
    }
}
