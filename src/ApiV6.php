<?php

namespace Antonowano\ApiYandexTaxi;

use GuzzleHttp\Client;

class ApiV6
{
    private const BASE_URL = 'https://taximeter.yandex.rostaxi.org';

    /** @var string */
    private $apiKey;

    /** @var int|null */
    private $statusCode;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function addOrder(array $data): ?array
    {
        return $this->request('/api/request/setcar', $data);
    }

    public function cancelOrder(array $data): ?array
    {
        return $this->request('/api/request/cancel', $data);
    }

    public function getOrderStatus(string $id): ?array
    {
        return $this->request('/api/request/status', [
            'id' => $id
        ]);
    }

    public function getDriverList(): ?array
    {
        return $this->request('/api/driver/list');
    }

    public function getDriver(string $id): ?array
    {
        return $this->request('/api/driver/get', [
            'id' => $id
        ]);
    }

    public function getBalance(): ?array
    {
        return $this->request('/api/driver/balance');
    }

    public function changeBalancePlus(array $data): ?array
    {
        return $this->request('/api/driver/balance/plus', $data);
    }

    public function changeBalanceMinus(array $data): ?array
    {
        return $this->request('/api/driver/balance/minus', $data);
    }

    public function getAllCoordinates(): ?array
    {
        return $this->request('/api/gps/list');
    }

    public function getCoordinates(string $id): ?array
    {
        return $this->request('/api/gps/get', [
            'id' => $id
        ]);
    }

    public function getCarList(): ?array
    {
        return $this->request('/api/car/list');
    }

    public function getCar(string $id): ?array
    {
        return $this->request('/api/car/get', [
            'id' => $id
        ]);
    }

    public function getCompanyList(): ?array
    {
        return $this->request('/api/company/list');
    }

    public function getCompany(string $id): ?array
    {
        return $this->request('/api/company/get', [
            'id' => $id
        ]);
    }

    public function getCompanyResponsible(string $id): ?array
    {
        return $this->request('/api/company/responsible', [
            'id' => $id
        ]);
    }

    public function addPassenger(array $data): ?array
    {
        return $this->request('/api/passenger/add', $data);
    }

    public function getPassenger(string $id): ?array
    {
        return $this->request('/api/passenger/get', [
            'id' => $id
        ]);
    }

    public function getTariffList(): ?array
    {
        return $this->request('/api/tariff/list');
    }

    public function getTariff(string $id): ?array
    {
        return $this->request('/api/tariff/get', [
            'id' => $id
        ]);
    }

    public function request($url, $data = []): ?array
    {
        $client = new Client();
        $response = $client->request('GET', self::BASE_URL . $url, [
            'query' => array_replace_recursive([
                'apikey' => $this->apiKey
            ], $data),
            'http_errors' => false
        ]);

        $this->statusCode = $response->getStatusCode();

        return json_decode($response->getBody()->getContents(), true);
    }
}
