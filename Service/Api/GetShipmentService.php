<?php

namespace InPost\Shipment\Service\Api;

use InPost\Shipment\Api\Data\PointDataFactory;
use InPost\Shipment\Api\Data\PointsServiceResponseFactory;
use InPost\Shipment\Api\Data\ShipmentResponse;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Http\ClientFactory;

class GetShipmentService
{
    /** @var ClientFactory */
    private $httpClientFactory;

    /** @var PointDataFactory */
    private $pointDataFactory;

    /** @var PointsServiceResponseFactory */
    private $pointsServiceResponseFactory;

    /** @var ConfigProvider */
    private $config;

    /**
     * @param ClientFactory $httpClient
     * @param PointDataFactory $pointDataFactory
     * @param ConfigProvider $config
     * @param PointsServiceResponseFactory $pointsServiceResponseFactory
     */
    public function __construct(
        ClientFactory $httpClient,
        PointDataFactory $pointDataFactory,
        ConfigProvider $config,
        PointsServiceResponseFactory $pointsServiceResponseFactory
    ) {
        $this->httpClientFactory = $httpClient;
        $this->pointDataFactory = $pointDataFactory;
        $this->pointsServiceResponseFactory = $pointsServiceResponseFactory;
        $this->config = $config;
    }

    public function getShipmentById($shipmentId): ShipmentResponse
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

    public function getShipmentByTrackingId($trackingId): array
    {
        $client = $this->httpClientFactory->create();

        $client->setAuthToken($this->config->getApiKey());
        $response = $client->get(
            $this->config->getShipXBaseUrl() . "/v1/organizations/{$this->config->getMerchantId()}/shipments",
            ['tracking_number' => $trackingId]
        );

        return json_decode($response->getBody()->getContents(), true);
    }
}
