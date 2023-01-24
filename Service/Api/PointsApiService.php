<?php

namespace InPost\Shipment\Service\Api;

use InPost\Shipment\Api\Data\PointsServiceRequest;
use InPost\Shipment\Api\Data\PointsServiceResponse;
use InPost\Shipment\Service\Http\HttpClientException;
use Magento\Framework\DataObject;

class PointsApiService
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
    private const API_BASE_URL = 'https://api-it-points.easypack24.net';

    public function getPoints(PointsServiceRequest $pointsServiceRequest) : PointsServiceResponse
    {
        $client = $this->httpClientFactory->create();
        $params = $pointsServiceRequest->getData();


        try {
            $response = $client->get(
                self::API_BASE_URL . '/v1/points',
                $params
            );
        } catch (HttpClientException $e) {
            // @todo handle exception here
            return $this->pointsServiceResponseFactory->create(['data' => []]);
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (!empty($data['items']) && count($data['items'])) {
            $items = [];
            foreach ($data['items'] as $itemData) {
                $items[] = $this->pointDataFactory->create(['data' => $itemData]);
            }
            $data['items'] = $items;
        }

        return $this->pointsServiceResponseFactory->create(['data' => $data]);
    }
}
