<?php
declare(strict_types=1);

namespace InPost\Shipment\Service\Api;

use InPost\Shipment\Api\Data\PointDataFactory;
use InPost\Shipment\Api\Data\PointsServiceRequest;
use InPost\Shipment\Api\Data\PointsServiceResponse;
use InPost\Shipment\Api\Data\PointsServiceResponseFactory;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Http\ClientFactory;
use InPost\Shipment\Service\Http\HttpClientException;

class PointsApiService
{
    /** @var ClientFactory */
    private $httpClientFactory;

    /** @var PointDataFactory */
    private $pointDataFactory;

    /** @var PointsServiceResponseFactory */
    private $pointsServiceResponseFactory;

    /** @var ConfigProvider */
    private $configProvider;

    /**
     * @param ClientFactory $httpClient
     * @param PointDataFactory $pointDataFactory
     * @param PointsServiceResponseFactory $pointsServiceResponseFactory
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        ClientFactory $httpClient,
        PointDataFactory $pointDataFactory,
        PointsServiceResponseFactory $pointsServiceResponseFactory,
        ConfigProvider $configProvider
    ) {
        $this->httpClientFactory = $httpClient;
        $this->pointDataFactory = $pointDataFactory;
        $this->pointsServiceResponseFactory = $pointsServiceResponseFactory;
        $this->configProvider = $configProvider;
    }

    public function getPoints(PointsServiceRequest $pointsServiceRequest): PointsServiceResponse
    {
        $client = $this->httpClientFactory->create();
        $params = $pointsServiceRequest->getData();

        try {
            $response = $client->get(
                $this->configProvider->getApiBaseUrl() . '/v1/points',
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
