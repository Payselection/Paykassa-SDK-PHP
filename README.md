# Paykassa api Library

## Оглавление

- [Установка](#установка)
- [Начало работы](#начало-работы)
- [Методы API](#методы-api)
    - [Create Receipt](#create-receipt)
    - [Check Receipt](#check-receipt)
- [Работа с webhooks](#webhooks)

## Установка <a name="установка"></a>

Установить библиотеку можно с помощью composer:

```
composer require paykassa/paykassa-php-client
```

## Начало работы <a name="начало-работы"></a>

1. Создайте экземпляр объекта клиента.
```php
$apiClient = new \PaySelection\Library();
$apiClient->setConfiguration([
    "api_url" : "https://api.pay-kassa.com",
    "merchant_id" : "123",
    "secret_key" : "###########",
    "webhook_url" : "https://webhook.site/notification/"
]);
```

Значение `webhook_url` должно совпадать со значением `WebhookUrl` из запросов

2. Вызовите нужный метод API. 

## Методы API <a name="методы-api"></a>

### Create Receipt <a name="create-receipt"></a>

[Формирование кассового онлайн-чека в документации](https://docs.pay-kassa.com/#tag/Operacii/operation/Create%20receipt)

Запрос используется для формирования кассового онлайн-чека. Используется идентификатор организации merchant_id, который можно получить/увидеть в ЛК Paykassa

```php
try {
    $receiptItems = new ReceiptItems();
    $receiptItems->add(
        new ReceiptItemDetails(
            name: 'test name',
            price: 100,
            quantity: 1,
            sum: 100,
            payment_method: PaymentMethodType::FULL_PREPAYMENT,
            payment_object: PaymentObjectType::COMMODITY,
            vat: new VatDetails(
                type: VatType::NONE,
            )
        )
    );

    $receiptPayments = new ReceiptPayments();
    $receiptPayments->add(
        new PaymentDetails(
            type: 1,
            sum: 100,
        )
    );

    $receiptVats = new ReceiptVats();
    $receiptVats->add(
        new VatDetails(
            type: VatType::NONE,
        )
    );

    $receipt = new ReceiptDetails(
        new ClientDetails(
            email: 'test@test.com'
        ),
        new CompanyDetails(
            inn: '220221121221',
            payment_address: 'https://site.ru/'
        ),
        items: $receiptItems,
        payments: $receiptPayments,
        vats: $receiptVats,
        total: 100,
    );

    $response = $apiClient->createReceipt(new CreateCheckRequest(
        operation_type: OperationType::INCOME,
        order_number: 'Test receipt',
        receipt: $receipt
    ));
} catch (\Exception $e) {
    $response = $e->getMessage();
}

var_dump($response);
```

### Check Receipt <a name="check-receipt"></a>
[Получение информации по чеку в документации](https://docs.pay-kassa.com/#tag/Operacii/operation/Check%20receipt)

Запрос используется для получения информации по чеку, а также уточнение о его текущем статусе.

```php
try {
    $response = $apiClient->checkReceipt('123');
} catch (\Exception $e) {
    $response = $e->getMessage();
}

var_dump($response);
```

## Работа с webhooks <a name="webhooks"></a>

[Webhook в документации](https://docs.pay-kassa.com/#tag/Webhooks-or-Vebhuk/operation/receipt)

```php
try {
    $result = $apiClient->hookPay();
} catch (\Exception $e) {
    $response = $e->getMessage();
}

var_dump($result);
```

## License

MIT
