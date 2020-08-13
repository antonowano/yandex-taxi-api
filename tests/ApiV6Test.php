<?php

namespace Tests\Antonowano\ApiYandexTaxi;

use Antonowano\ApiYandexTaxi\ApiV6;
use PHPUnit\Framework\TestCase;

class ApiV6Test extends TestCase
{
    const API_KEY = 'e921c69caf41620fef42693674b46b76';

    /**
     * @var ApiV6
     */
    private $api;

    public function setUp(): void
    {
        $this->api = new ApiV6(self::API_KEY);
    }

    public function testGetDriverList()
    {
        $driverList = $this->api->getDriverList();

        if ($this->api->getStatusCode() != 200) {
            $this->fail('Status code is not 200');
            return null;
        }

        if (!is_array($driverList)) {
            $this->fail('Driver list is not array');
            return null;
        }

        if (count($driverList['drivers']) == 0) {
            $this->fail('Driver list is empty');
            return null;
        }

        $driverId = key($driverList['drivers']);
        $driver = $driverList['drivers'][$driverId];
        $this->assertNotEmpty($driver['FirstName']);
        $this->assertNotEmpty($driver['LastName']);
        $this->assertNotEmpty($driver['Surname']);
        $this->assertNotEmpty($driver['LicenseNumber']);
        $this->assertNotEmpty($driver['Phones']);
        return $driverId;
    }

    /**
     * @depends testGetDriverList
     */
    public function testChangeBalance(string $driverId)
    {
        $this->api->changeBalanceMinus([
            'driver' => $driverId,
            'sum' => 1
        ]);

        $this->assertEquals(200, $this->api->getStatusCode());

        $this->api->changeBalancePlus([
            'driver' => $driverId,
            'sum' => 1
        ]);

        $this->assertEquals(200, $this->api->getStatusCode());
    }

    /**
     * @depends testGetDriverList
     */
    public function testGetDriver(string $driverId)
    {
        $response = $this->api->getDriver($driverId);

        if ($this->api->getStatusCode() != 200) {
            $this->fail('Status code is not 200');
            return;
        }

        $this->assertNotEmpty($response['driver']);
        $this->assertNotEmpty($response['carId']);
        $this->assertNotEmpty($response['car']);
        $this->assertNotEmpty($response['balance']);
    }

    public function testGetBalance()
    {
        $response = $this->api->getBalance();

        if ($this->api->getStatusCode() != 200) {
            $this->fail('Status code is not 200');
            return;
        }

        if (!is_array($response)) {
            $this->fail('Balance list is not array');
            return;
        }

        if (count($response) == 0) {
            $this->fail('Balance list is empty');
            return;
        }

        $this->assertEquals(200, $this->api->getStatusCode());
    }
}
