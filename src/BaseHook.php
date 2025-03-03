<?php

namespace PayKassa;

use PayKassa\Exceptions\BadTypeException;
use ReflectionException;

class BaseHook extends BaseInteraction
{
    /**
     * @param string $merchantId
     * @param string $secretKey
     * @param string $webhookUrl
     *
     * @throws BadTypeException
     * @throws ReflectionException
     * @return void
     */
    public function hook(string $merchantId, string $secretKey, string $webhookUrl): void
    {
        $request = file_get_contents('php://input');
        $headers = array_change_key_case(getallheaders());

        if (empty($request)) {
            throw new BadTypeException('Request not found');
        }
        if (empty($headers['x-merchant-id'])) {
            throw new BadTypeException('X-Merchant-Id not found');
        }
        $x_merchantId = $headers['x-merchant-id'];
        if ($merchantId !== $x_merchantId) {
            throw new BadTypeException(sprintf('X-Merchant-Id[%s] not math with merchantId[%s]', $x_merchantId,
                $merchantId));
        }
        if (empty($headers['x-webhook-signature'])) {
            throw new BadTypeException('Signature not found');
        }

        $this->checkSignature($merchantId, $secretKey, $webhookUrl, $request, $headers);
        $decodedRequest = $this->decodeRequest($request);

        $this->fill($decodedRequest);
    }

    /**
     * @throws BadTypeException
     */
    private function checkSignature(
        string $merchantId,
        string $secretKey,
        string $webhookUrl,
        string $request,
        array $headers
    ): void {
        $signBody = 'POST'.PHP_EOL.
            $webhookUrl.PHP_EOL.
            $merchantId.PHP_EOL.
            $request;

        $signBody = str_replace(PHP_EOL, "\n", $signBody);

        if ($headers['x-webhook-signature'] !== self::getSignature($signBody, $secretKey)) {
            throw new BadTypeException('Signature error');
        }
    }

    /**
     * @throws BadTypeException
     */
    private function decodeRequest(string $request): array
    {
        $decodedRequest = json_decode($request, true);
        if ($decodedRequest === null && json_last_error() !== JSON_ERROR_NONE) {
            $request = stripcslashes($request);
            $request_enc = json_decode($request, true);
            if ($request_enc === null && json_last_error() !== JSON_ERROR_NONE) {
                throw new BadTypeException('Can\'t decode JSON: '.json_last_error_msg());
            }
        }

        return $decodedRequest;
    }

    /**
     * @param string $body
     * @param string $secretKey
     *
     * @return string
     */
    private static function getSignature(string $body, string $secretKey): string
    {
        if (empty($body)) {
            return ";";
        }

        return hash_hmac("sha256", $body, $secretKey);
    }
}

if (!function_exists('getallheaders')) {

    /**
     * Get all HTTP header key/values as an associative array for the current request.
     *
     * @return array[string] The HTTP header key/value pairs.
     */
    function getallheaders(): array
    {
        $headers = [];

        $copy_server = [
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        ];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }

        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = $_SERVER['PHP_AUTH_PW'] ?? '';
                $headers['Authorization'] = 'Basic '.base64_encode($_SERVER['PHP_AUTH_USER'].':'.$basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }

        return $headers;
    }
}
