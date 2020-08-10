<?php

namespace Tests\Antonowano\ApiYandexTaxi;

use Antonowano\ApiYandexTaxi\ApiV7;
use PHPUnit\Framework\TestCase;

class ApiV7Test extends TestCase
{
    const CLIENT_ID = 'taxi/park/11383f19ad0c90e2f2f64671d045cde8';
    const PARK_ID = '11383f19ad0c90e2f2f64671d045cde8';
    const API_KEY = 'CDNHzvGqtLWCIVLmAzNVGmVtUFwzBrgzL';

    /**
     * @var ApiV7
     */
    private $api;

    public function setUp(): void
    {
        $this->api = new ApiV7(self::CLIENT_ID, self::PARK_ID, self::API_KEY);
    }

    public function testDriverProfileList()
    {
        $countDrivers = 3;
        $driverId = null;
        $response = $this->api->getDriverList([
            'fields' => [
                'account' => ['balance'],
                'car' => [],
                'current_status' => [],
                'driver_profile' => ['id']
            ],
            'limit' => $countDrivers,
            'offset' => 0,
        ]);

        if ($this->api->getStatusCode() != 200) {
            $this->fail('Status code is not 200' . PHP_EOL . 'Message: ' . $response['message']);
            return null;
        }

        if (!isset($response['driver_profiles'])
            || !is_array($response['driver_profiles'])
            || count($response['driver_profiles']) != $countDrivers) {
            $this->fail('The number of driver profiles must be ' . $countDrivers);
            return null;
        }

        foreach ($response['driver_profiles'] as $profile) {
            $this->assertTrue(count($profile['accounts']) > 0);

            if (isset($profile['driver_profile']['id'])) {
                $driverId = $profile['driver_profile']['id'];
            } else {
                $this->fail('Missing driver profile id');
            }
        }

        $this->assertEquals($countDrivers, $response['limit']);
        $this->assertTrue(in_array(self::PARK_ID, array_column($response['parks'], 'id')));

        return $driverId;
    }

    /**
     * @depends testDriverProfileList
     */
    public function testOrderList(string $driverId)
    {
        $countOrders = 1;
        $response = $this->api->getOrderList([
            'query' => [
                'park' => [
                    'driver_profile' => [
                        'id' => $driverId,
                    ],
                    'order' => [
                        'brooker_at' => [
                            'from' => '1970-01-01T00:00:00-0400',
                            'to' => date('Y-m-d') . 'T23:59:59-0400'
                        ],
                        'ended_at' => [
                            'from' => '1970-01-01T00:00:00-0400',
                            'to' => date('Y-m-d') . 'T23:59:59-0400'
                        ],
                        'statuses' => [
                            'complete'
                        ]
                    ],
                ]
            ],
            'limit' => $countOrders,
        ]);

        if ($this->api->getStatusCode() != 200) {
            $this->fail('Status code is not 200' . PHP_EOL . 'Message: ' . $response['message']);
            return null;
        }

        if (!isset($response['orders'])
            || !is_array($response['orders'])
            || count($response['orders']) != $countOrders) {
            $this->fail('The number of orders must be ' . $countOrders);
            return null;
        }

        foreach ($response['orders'] as $order) {
            $this->assertSame('complete', $order['status']);
        }
    }
}
