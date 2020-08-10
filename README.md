# Yandex Taxi API v7

[Documentation](https://fleet.taxi.yandex.ru/api/docs/)

## Installation

```text
composer require antonowano/yandex-taxi-api
```

## Usage API v7

```php
use Antonowano\ApiYandexTaxi\ApiV7;

$clientId = 'taxi/park/11383f19ad0c90e2f2f64671d045cde8';
$parkId = '11383f19ad0c90e2f2f64671d045cde8';
$apiKey = 'CDNHzvGqtLWCIVLmAzNVGmVtUFwzBrgzL';
$api = new ApiV7($clientId, $parkId, $apiKey);

$response = $api->getDriverList([
    'fields' => [
        'account' => ['balance'],
        'car' => [],
        'current_status' => [],
        'driver_profile' => ['id']
    ],
    'limit' => 3,
    'offset' => 0,
]);

if ($api->getStatusCode() != 200) {
    throw new \Exception($response['message']);
}

foreach ($response['driver_profiles'] as $profile) {
    echo 'Driver Id: ' . $profile['driver_profile']['id'] . PHP_EOL;
    echo 'Balance: ' . $profile['accounts'][0]['balance'] . PHP_EOL;
    echo PHP_EOL;
}
```
