# Yandex Taxi API

### Installation

```text
composer require antonowano/yandex-taxi-api
```

## API v6

[Documentation](https://yadi.sk/i/dgsb8oSsuTnOpA)

### Example

```php
use Antonowano\ApiYandexTaxi\ApiV6;

$apiKey = 'e921c69caf41620fef42693674b46b76';
$api = new ApiV6($apiKey);

$response = $api->getDriverList();

if ($api->getStatusCode() != 200) {
    throw new \Exception($response['message']);
}

foreach ($response['drivers'] as $driverId => $driver) {
    echo 'Driver id: ' . $driverId . PHP_EOL;
    echo 'First name: ' . $driver['FirstName'] . PHP_EOL;
    echo 'Last name: ' . $driver['LastName'] . PHP_EOL;
    echo 'Surname: ' . $driver['Surname'] . PHP_EOL;
    echo 'Phone: ' . $driver['Phones'] . PHP_EOL;
    echo PHP_EOL;
}
```

## API v7

[Documentation](https://fleet.taxi.yandex.ru/api/docs/)

### Example

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
