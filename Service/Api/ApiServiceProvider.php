<?php

namespace InPost\Shipment\Service\Api;

class ApiServiceProvider
{
    public function __construct(
        \InPost\Shipment\Service\Api\PointsApiServiceFactory $pointsApiService,
        \InPost\Shipment\Service\Api\TrackingServiceFactory $trackingService,
        \InPost\Shipment\Service\Api\GetShipmentServiceFactory $getShipmentService
    ) {
        $this->pointsApiService = $pointsApiService;
        $this->trackingService = $trackingService;
        $this->getShipmentService = $getShipmentService;
    }

    public function getPointsApiService() : PointsApiService
    {
        return $this->pointsApiService->create();
    }

    public function getTrackingService() : TrackingService
    {
        return $this->trackingService->create();
    }

    public function getGetShipmentService() : GetShipmentService
    {
        return $this->getShipmentService->create();
    }
}