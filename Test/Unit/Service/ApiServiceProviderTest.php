<?php

namespace InPost\Shipment\Test\Unit\Service\Api;

use InPost\Shipment\Service\Api\ApiServiceProvider;
use InPost\Shipment\Service\Api\GetShipmentService;
use InPost\Shipment\Service\Api\GetShipmentServiceFactory;
use InPost\Shipment\Service\Api\PointsApiService;
use InPost\Shipment\Service\Api\PointsApiServiceFactory;
use InPost\Shipment\Service\Api\TrackingService;
use InPost\Shipment\Service\Api\TrackingServiceFactory;
use PHPUnit\Framework\TestCase;

class ApiServiceProviderTest extends TestCase
{
    /** @var ApiServiceProvider */
    private $apiServiceProvider;

    /** @var PointsApiServiceFactory */
    private $pointsApiServiceFactory;

    /** @var TrackingServiceFactory */
    private $trackingServiceFactory;

    /** @var GetShipmentServiceFactory */
    private $getShipmentServiceFactory;

    protected function setUp(): void
    {
        $this->pointsApiServiceFactory = $this->createMock(PointsApiServiceFactory::class);
        $this->trackingServiceFactory = $this->createMock(TrackingServiceFactory::class);
        $this->getShipmentServiceFactory = $this->createMock(GetShipmentServiceFactory::class);

        $this->apiServiceProvider = new ApiServiceProvider(
            $this->pointsApiServiceFactory,
            $this->trackingServiceFactory,
            $this->getShipmentServiceFactory
        );
    }

    public function testGetPointsApiService(): void
    {
        $pointsApiService = $this->createMock(PointsApiService::class);

        $this->pointsApiServiceFactory->expects($this->once())
            ->method('create')
            ->willReturn($pointsApiService);

        $this->assertSame($pointsApiService, $this->apiServiceProvider->getPointsApiService());
    }

    public function testGetTrackingService(): void
    {
        $trackingService = $this->createMock(TrackingService::class);

        $this->trackingServiceFactory->expects($this->once())
            ->method('create')
            ->willReturn($trackingService);

        $this->assertSame($trackingService, $this->apiServiceProvider->getTrackingService());
    }

    public function testGetGetShipmentService(): void
    {
        $getShipmentService = $this->createMock(GetShipmentService::class);

        $this->getShipmentServiceFactory->expects($this->once())
            ->method('create')
            ->willReturn($getShipmentService);

        $this->assertSame($getShipmentService, $this->apiServiceProvider->getGetShipmentService());
    }
}
