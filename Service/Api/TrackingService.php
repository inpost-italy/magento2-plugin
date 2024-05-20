<?php
declare(strict_types=1);

namespace InPost\Shipment\Service\Api;

use InPost\Shipment\Api\Data\TrackingData;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Http\ClientFactory;

class TrackingService
{
    /** @var ClientFactory */
    private $httpClientFactory;

    /** @var ConfigProvider */
    private $configProvider;

    /**
     * @param ClientFactory $httpClient
     * @param ConfigProvider $configProvider
     */
    public function __construct(
        ClientFactory $httpClient,
        ConfigProvider $configProvider
    ) {
        $this->httpClientFactory = $httpClient;
        $this->configProvider = $configProvider;
    }

    /**
     * @param $trackingNumber
     *
     * @return TrackingData
     * @throws \Exception
     */
    public function getTracking($trackingNumber): TrackingData
    {
        $client = $this->httpClientFactory->create();

        $response = $client->get(
            "{$this->configProvider->getShipXBaseUrl()}/v1/tracking/$trackingNumber"
        );
        $data = json_decode($response->getBody()->getContents(), true);

        if($data['status'])
        {
            $data['status'] = $this->getTrackingStatusDetails($data['status']);
        }

        return new TrackingData($data);
    }

    public function getTrackingStatuses()
    {
        $client = $this->httpClientFactory->create();

        $response = $client->get(
            "{$this->configProvider->getShipXBaseUrl()}/v1/statuses"
        );
        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    public function getTrackingStatusDetails(string $status)
    {
        $trackingStatuses = $this->getTrackingStatuses();
        foreach ($trackingStatuses['items'] as $trackingStatus)
        {
            if($trackingStatus['name'] == $status)
            {
                return $trackingStatus;
            }
        }
    }

}
