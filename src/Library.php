<?php

namespace PayKassa;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use PayKassa\Enum\PKMethodsEnum;
use PayKassa\Exceptions\BadTypeException;
use PayKassa\Exceptions\PKResponseException;
use PayKassa\Hook\HookReceipt;
use PayKassa\Request\CreateCheckRequest;
use PayKassa\Request\StatusRequest;
use PayKassa\Response\CheckReceiptResponse;
use PayKassa\Response\CreateReceiptResponse;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;

class Library
{
    protected string $merchantId;
    protected string $secretKey;
    protected string $apiUrl;
    protected Client $client;
    private array $configParams;

    /**
     * @param string|null $filePath
     */
    public function __construct(string $filePath = null)
    {
        $this->loadConfiguration($filePath);
    }

    /**
     * @param array $config
     *
     * @return $this
     */
    public function setConfiguration(array $config): Library
    {
        $paramsArray = array_merge($this->configParams, $config);
        $this->configParams = $paramsArray;

        return $this;
    }

    /**
     * @see https://docs.pay-kassa.com/#tag/Operacii/operation/Create%20receipt
     * Формирование кассового онлайн-чека
     *
     * @param CreateCheckRequest $request
     *
     * @throws GuzzleException
     * @throws ReflectionException
     * @return BaseResponse|CreateReceiptResponse
     */
    public function createReceipt(CreateCheckRequest $request): BaseResponse|CreateReceiptResponse
    {
        $method = PKMethodsEnum::CREATE_RECEIPT->value;
        $this->createClient();
        $req = $request->makeRequest();

        return $this->request(sprintf($method, $this->merchantId), $req, 'POST', new CreateReceiptResponse());
    }

    /**
     * @see https://docs.pay-kassa.com/#tag/Operacii/operation/Check%20receipt
     * Получение информации по чеку
     *
     * @param string $id
     *
     * @throws GuzzleException
     * @throws ReflectionException
     * @return BaseResponse|CheckReceiptResponse
     */
    public function checkReceipt(string $id): BaseResponse|CheckReceiptResponse
    {
        $method = PKMethodsEnum::CHECK_RECEIPT->value;
        $this->createClient();

        $data = new StatusRequest($id);

        return $this->request(sprintf($method, $data->id), $data->makeRequest(), 'GET', new CheckReceiptResponse());
    }

    /**
     * @see https://docs.pay-kassa.com/#tag/Webhooks-or-Vebhuk/operation/receipt
     * Система отправляет запрос на веб-адрес вебхука с информацией о данном чеке
     *
     * @throws BadTypeException
     * @throws ReflectionException
     * @return HookReceipt
     */
    public function hookPay(): HookReceipt
    {
        $hook = new HookReceipt();
        $merchantId = $this->configParams['merchant_id'];
        $secretKey = $this->configParams['secret_key'];
        $webhookUrl = $this->configParams['webhook_url'];
        $hook->hook($merchantId, $secretKey, $webhookUrl);

        return $hook;
    }

    /**
     * @param string $method
     * @param string $requestMethod
     * @param array  $postData
     *
     * @throws GuzzleException
     * @return ResponseInterface
     */
    protected function sendRequest(string $method, string $requestMethod, array $postData = []): ResponseInterface
    {
        $uid = $this->getIdempotenceKey();
        $msg = $requestMethod.PHP_EOL.
            '/'.$method.PHP_EOL.
            $this->merchantId.PHP_EOL.
            $uid.PHP_EOL.
            json_encode($postData);

        $msg = str_replace(PHP_EOL, "\n", $msg);

        $headers = [
            'X-Request-ID'        => $uid,
            'X-Request-Signature' => $this->getSignature($msg, $this->secretKey),
        ];

        $options = ['json' => $postData];
        $options['headers'] = $headers;

        if ('GET' === $requestMethod) {
            return $this->client->get($method, $options);
        } else {
            return $this->client->post($method, $options);
        }
    }

    /**
     * @param string            $method
     * @param array             $postData
     * @param string            $requestMethod
     * @param BaseResponse|null $pkResponse
     *
     * @throws GuzzleException
     * @throws ReflectionException
     * @return BaseResponse
     */
    protected function request(
        string $method,
        array $postData = [],
        string $requestMethod = 'POST',
        ?BaseResponse $pkResponse = null
    ): BaseResponse {
        $response = $this->sendRequest($method, $requestMethod, $postData);

        $pkResponse = $pkResponse ?? new BaseResponse();

        return $pkResponse->fillByResponse($response);
    }

    /**
     * @return string
     */
    private function getIdempotenceKey(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * @param string $body
     * @param string $secretKey
     *
     * @return string
     */
    private function getSignature(string $body, string $secretKey): string
    {
        return hash_hmac('sha256', $body, $secretKey);
    }

    /**
     * @param $filePath
     *
     * @return void
     */
    private function loadConfiguration($filePath = null): void
    {
        if ($filePath) {
            $data = file_get_contents($filePath);
        } else {
            $data = file_get_contents(__DIR__.DIRECTORY_SEPARATOR."configuration.json");
        }

        $paramsArray = json_decode($data, true);
        $this->configParams = $paramsArray;
    }

    /**
     * @return void
     */
    private function createClient(): void
    {
        $this->apiUrl = $this->configParams['api_url'];
        $this->merchantId = $this->configParams['merchant_id'];
        $this->secretKey = $this->configParams['secret_key'];

        $stack = HandlerStack::create();
        $stack->push($this->httpErrorHandler());

        $this->client = new Client([
            'headers'  => ['X-Merchant-ID' => $this->merchantId],
            'base_uri' => $this->apiUrl,
            'expect'   => false,
            'handler'  => $stack,
        ]);
    }

    function httpErrorHandler(): Closure
    {
        return function (callable $handler) {
            return function (
                $request,
                array $options
            ) use ($handler) {
                return $handler($request, $options)->then(
                    function (ResponseInterface $response) use ($request) {
                        $code = $response->getStatusCode();
                        if ($code < 400) {
                            return $response;
                        }

                        $level = (int)floor($code / 100);
                        if ($level === 4) {
                            $message = 'Client error.';
                        } elseif ($level === 5) {
                            $message = 'Server error.';
                        } else {
                            $message = 'Unsuccessful request.';
                        }

                        $body = json_decode((string)$response->getBody(), true);

                        if (!json_last_error()) {
                            if (isset($body['detail'])) {
                                $details = json_encode($body['detail']);
                                $message .= sprintf(' Error detail: %s.', $details);
                            }
                        }

                        throw new PKResponseException($message, $request, $response, null, []);
                    }
                );
            };
        };
    }

}
