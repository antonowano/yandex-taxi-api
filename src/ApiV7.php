<?php

namespace Antonowano\ApiYandexTaxi;

use GuzzleHttp\Client;

class ApiV7
{
    private const BASE_URL = 'https://fleet-api.taxi.yandex.net';

    /** @var string */
    private $clientId;

    /** @var string */
    private $parkId;

    /** @var string */
    private $apiKey;

    /** @var string */
    private $language;

    /** @var int|null */
    private $statusCode;

    public function __construct(string $clientId, string $parkId, string $apiKey, string $language = 'en')
    {
        $this->clientId = $clientId;
        $this->parkId = $parkId;
        $this->apiKey = $apiKey;
        $this->language = $language;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function getDriverList(array $body): ?array
    {
        return $this->request('POST', '/v1/parks/driver-profiles/list', array_replace_recursive([
            'query' => [
                'park' => [
                    'id' => $this->parkId,
                ],
            ],
        ], $body));
    }

    public function getOrderList(array $body): ?array
    {
        return $this->request('POST', '/v1/parks/orders/list', array_replace_recursive([
            'query' => [
                'park' => [
                    'id' => $this->parkId,
                ],
            ],
        ], $body));
    }

    public function request(string $method, string $url, array $body): ?array
    {
        $client = new Client();
        $response = $client->request($method, self::BASE_URL . $url, [
            'json' => $body,
            'headers' => [
                'X-Client-ID' => $this->clientId,
                'X-API-Key' => $this->apiKey,
                'Accept-Language' => $this->language,
            ],
            'http_errors' => false
        ]);

        $this->statusCode = $response->getStatusCode();

        return json_decode($response->getBody()->getContents(), true);
    }
}
