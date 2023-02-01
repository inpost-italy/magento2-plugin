<?php

namespace InPost\Shipment\Service\Api;

use InPost\Shipment\Api\Data\PointsServiceRequest;
use InPost\Shipment\Api\Data\PointsServiceResponse;
use InPost\Shipment\Service\Http\HttpClientException;
use Magento\Framework\DataObject;

class GetShipmentService
{
    private \InPost\Shipment\Service\Http\ClientFactory $httpClientFactory;

    private \InPost\Shipment\Api\Data\PointDataFactory $pointDataFactory;

    private \InPost\Shipment\Api\Data\PointsServiceResponseFactory $pointsServiceResponseFactory;

    public function __construct(
        \InPost\Shipment\Service\Http\ClientFactory $httpClient,
        \InPost\Shipment\Api\Data\PointDataFactory $pointDataFactory,
        \InPost\Shipment\Api\Data\PointsServiceResponseFactory $pointsServiceResponseFactory
    ) {
        $this->httpClientFactory = $httpClient;
        $this->pointDataFactory = $pointDataFactory;
        $this->pointsServiceResponseFactory = $pointsServiceResponseFactory;
    }

    /** @var string TODO config */

    private const API_BASE_URL = 'https://stage-api-shipx-it.easypack24.net/';

    public function getShipment($shipmentId) : array
    {
        $client = $this->httpClientFactory->create();

        $client->setAuthToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJzdGFnZS1zaGlweC1hcGktaXQuZWFzeXBhY2syNC5uZXQiLCJzdWIiOiJzdGFnZS1zaGlweC1hcGktaXQuZWFzeXBhY2syNC5uZXQiLCJleHAiOjE2NzE3OTQ4OTEsImlhdCI6MTY3MTc5NDg5MSwianRpIjoiYjJmYzc4YTUtMzk2Ny00YmI4LWEyNjItMDY0NDNiMzI1MjgwIn0.-rIQG99AqNfmybLQ3qGQTJKd-gogQcV350aIz2hd7SCT6uUNUYqqxIux0kr7mAGGQdaxu5ADLlEKkDGCjyVwtg');
        $response = $client->get(
            self::API_BASE_URL . "v1/organizations/116/shipments?id=$shipmentId",
            []
        );

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }
}
