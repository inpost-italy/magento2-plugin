<?php

namespace InPost\Shipment\Service\Api;

class ApiServiceProvider
{
    /** @var PointsApiServiceFactory */
    protected $pointsApiService;

    /** @var TrackingServiceFactory */
    protected $trackingService;

    /** @var GetShipmentServiceFactory */
    protected $getShipmentService;

    /**
     * @param PointsApiServiceFactory $pointsApiService
     * @param TrackingServiceFactory $trackingService
     * @param GetShipmentServiceFactory $getShipmentService
     */
    public function __construct(
        PointsApiServiceFactory $pointsApiService,
        TrackingServiceFactory $trackingService,
        GetShipmentServiceFactory $getShipmentService
    ) {
        $this->pointsApiService = $pointsApiService;
        $this->trackingService = $trackingService;
        $this->getShipmentService = $getShipmentService;
    }

    public function getPointsApiService(): PointsApiService
    {
        return $this->pointsApiService->create();
    }

    public function getTrackingService(): TrackingService
    {
        return $this->trackingService->create();
    }

    public function getGetShipmentService(): GetShipmentService
    {
        return $this->getShipmentService->create();
    }
}
