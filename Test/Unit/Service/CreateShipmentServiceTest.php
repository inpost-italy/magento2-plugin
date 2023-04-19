<?php

namespace InPost\Shipment\Service\Api;

use PHPUnit\Framework\TestCase;
use InPost\Shipment\Api\Data\CreateShipmentRequest;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Http\ClientFactory;
use InPost\Shipment\Service\Http\HttpClientInterface;
use InPost\Shipment\Service\Http\ResponseInterface;

class CreateShipmentServiceTest extends TestCase
{
    private $httpClientFactoryMock;

    private $httpClientMock;

    private $configProviderMock;

    private $responseMock;

    private $responseBodyMock;

    private $responseContentsMock;

    private $requestMock;

    private $requestDataMock;

    public function setUp(): void
    {
        $this->httpClientFactoryMock = $this->createMock(ClientFactory::class);
        $this->httpClientMock = $this->createMock(HttpClientInterface::class);
        $this->configProviderMock = $this->createMock(ConfigProvider::class);
        $this->responseMock = $this->createMock(ResponseInterface::class);
        $this->responseBodyMock = $this->getMockBuilder(\Psr\Http\Message\StreamInterface::class)->getMock();
        $this->responseContentsMock = '{"key": "value"}';
        $this->requestMock = $this->createMock(CreateShipmentRequest::class);
        $this->requestDataMock = [
            'parcels' => [
                'template' => 'template',
            ],
            'other_field' => 'value',
        ];
    }

    public function testExecute()
    {
        $apiKey = 'api_key';
        $merchantId = 'merchant_id';
        $shipXBaseUrl = 'https://shipx-base-url.com';
        $url = "{$shipXBaseUrl}/v1/organizations/{$merchantId}/shipments";
        $requestJson = '{"parcels":{"template":"template"},"other_field":"value"}';
        $responseArray = ['key' => 'value'];

        $this->configProviderMock->expects($this->once())
            ->method('getApiKey')
            ->willReturn($apiKey);

        $this->configProviderMock->expects($this->once())
            ->method('getMerchantId')
            ->willReturn($merchantId);

        $this->configProviderMock->expects($this->once())
            ->method('getShipXBaseUrl')
            ->willReturn($shipXBaseUrl);

        $this->httpClientFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($this->httpClientMock);

        $this->httpClientMock->expects($this->once())
            ->method('setAuthToken')
            ->with($apiKey);

        $this->requestMock->expects($this->once())
            ->method('getData')
            ->willReturn($this->requestDataMock);

        $this->httpClientMock->expects($this->once())
            ->method('post')
            ->with($url, $requestJson)
            ->willReturn($this->responseMock);

        $this->responseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($this->responseBodyMock);

        $this->responseBodyMock->expects($this->once())
            ->method('getContents')
            ->willReturn($this->responseContentsMock);

        $service = new CreateShipmentService($this->httpClientFactoryMock, $this->configProviderMock);
        $result = $service->execute($this->requestMock);

        $this->assertEquals($responseArray, $result);
    }
}
