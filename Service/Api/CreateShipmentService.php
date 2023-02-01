<?php

namespace InPost\Shipment\Service\Api;

use InPost\Shipment\Api\Data\CreateShipmentRequest;
use InPost\Shipment\Service\Http\ClientFactory;

class CreateShipmentService
{
    private ClientFactory $httpClientFactory;

    /**
     * @param ClientFactory $httpClient
     */
    public function __construct(
        \InPost\Shipment\Service\Http\ClientFactory $httpClient
    ) {
        $this->httpClientFactory = $httpClient;
    }
    public function execute(CreateShipmentRequest $request)
    {
        $client = $this->httpClientFactory->create();

        $data = $this->generateRequestData($request);

        $client->setAuthToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpc3MiOiJzdGFnZS1zaGlweC1hcGktaXQuZWFzeXBhY2syNC5uZXQiLCJzdWIiOiJzdGFnZS1zaGlweC1hcGktaXQuZWFzeXBhY2syNC5uZXQiLCJleHAiOjE2NzE3OTQ4OTEsImlhdCI6MTY3MTc5NDg5MSwianRpIjoiYjJmYzc4YTUtMzk2Ny00YmI4LWEyNjItMDY0NDNiMzI1MjgwIn0.-rIQG99AqNfmybLQ3qGQTJKd-gogQcV350aIz2hd7SCT6uUNUYqqxIux0kr7mAGGQdaxu5ADLlEKkDGCjyVwtg');

        $response = $client->post(
            'https://stage-api-shipx-it.easypack24.net/v1/organizations/116/shipments',
            json_encode($data)
        );

        return json_decode($response->getBody()->getContents(), true);
    }

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