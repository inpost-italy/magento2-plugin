<?php

namespace InPost\Shipment\Service\Api;

use InPost\Shipment\Api\Data\CreateShipmentRequest;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Http\ClientFactory;
use InPost\Shipment\Service\Http\HttpClientException;

class CreateShipmentService
{
    private ClientFactory $httpClientFactory;
    private ConfigProvider $configProvider;

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
     * @param CreateShipmentRequest $request
     * @return mixed
     * @throws \InPost\Shipment\Service\Http\HttpClientException
     */
    public function execute(CreateShipmentRequest $request)
    {
        try {
            $client = $this->httpClientFactory->create();

            $apiKey = $this->configProvider->getApiKey();
            $merchantId = $this->configProvider->getMerchantId();

            $data = $this->generateRequestData($request);
            $client->setAuthToken($apiKey);

            $response = $client->post(
                "https://stage-api-shipx-it.easypack24.net/v1/organizations/{$merchantId}/shipments",
                json_encode($data)
            );
        } catch (HttpClientException $exception) {
            // TODO: Add logging and error handling in case of response not 200
            throw $exception;
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param CreateShipmentRequest $request
     * @return array
     */
    private function generateRequestData(CreateShipmentRequest $request) : array
    {
        $requestData = $request->getData();
        $template = $requestData['parcels']['template'];
        unset($requestData['parcels']);
        $requestData['parcels'] = new \stdClass();
        $requestData['parcels']->template = $template;

        return $requestData;
    }
}
