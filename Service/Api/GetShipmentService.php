<?php

namespace InPost\Shipment\Service\Api;

use InPost\Shipment\Api\Data\ShipmentResponse;
use InPost\Shipment\Config\ConfigProvider;

class GetShipmentService
{
    private \InPost\Shipment\Service\Http\ClientFactory $httpClientFactory;

    private \InPost\Shipment\Api\Data\PointDataFactory $pointDataFactory;

    private \InPost\Shipment\Api\Data\PointsServiceResponseFactory $pointsServiceResponseFactory;

    private ConfigProvider $config;

    public function __construct(
        \InPost\Shipment\Service\Http\ClientFactory $httpClient,
        \InPost\Shipment\Api\Data\PointDataFactory $pointDataFactory,
        ConfigProvider $config,
        \InPost\Shipment\Api\Data\PointsServiceResponseFactory $pointsServiceResponseFactory
    ) {
        $this->httpClientFactory = $httpClient;
        $this->pointDataFactory = $pointDataFactory;
        $this->pointsServiceResponseFactory = $pointsServiceResponseFactory;
        $this->config = $config;
    }

    /** @var string TODO config */

    public function getShipmentById($shipmentId) : ShipmentResponse
    {
        $client = $this->httpClientFactory->create();
        $client->setAuthToken($this->config->getApiKey());

        $response = $client->get(
             "{$this->config->getShipXBaseUrl()}/v1/organizations/{$this->config->getMerchantId()}/shipments",
            ['id' => $shipmentId]
        );
        $data = json_decode($response->getBody()->getContents(), true);

        return new ShipmentResponse($data);
    }

    public function getShipmentByTrackingId($trackingId) : array
    {
        $client = $this->httpClientFactory->create();

        $client->setAuthToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJzdGFnZS1zaGlweC1hcGktaXQuZWFzeXBhY2syNC5uZXQiLCJzdWIiOiJzdGFnZS1zaGlweC1hcGktaXQuZWFzeXBhY2syNC5uZXQiLCJleHAiOjE2NzE3OTQ4OTEsImlhdCI6MTY3MTc5NDg5MSwianRpIjoiYjJmYzc4YTUtMzk2Ny00YmI4LWEyNjItMDY0NDNiMzI1MjgwIn0.-rIQG99AqNfmybLQ3qGQTJKd-gogQcV350aIz2hd7SCT6uUNUYqqxIux0kr7mAGGQdaxu5ADLlEKkDGCjyVwtg');
        $response = $client->get(
            $this->config->getShipXBaseUrl() . "/v1/organizations/116/shipments",
            ['tracking_number' => $trackingId]
        );

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }
}
