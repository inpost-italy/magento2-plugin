<?php
declare(strict_types=1);

namespace InPost\Shipment\Service\Api;

use InPost\Shipment\Api\Data\PointDataFactory;
use InPost\Shipment\Api\Data\PointsServiceRequest;
use InPost\Shipment\Api\Data\PointsServiceResponseFactory;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Http\ClientFactory;
use InPost\Shipment\Service\Http\HttpClient;
use InPost\Shipment\Service\Http\HttpClientException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PointsApiServiceTest extends TestCase
{
    /** @var MockObject|ClientFactory */
    private $httpClientFactory;

    /** @var MockObject|PointDataFactory */
    private $pointDataFactory;

    /** @var MockObject|PointsServiceResponseFactory */
    private $pointsServiceResponseFactory;

    /** @var MockObject|ConfigProvider */
    private $configProvider;

    /** @var PointsApiService */
    private $service;

    protected function setUp(): void
    {
        $this->httpClientFactory = $this->createMock(ClientFactory::class);
        $this->pointDataFactory = $this->createMock(PointDataFactory::class);
        $this->pointsServiceResponseFactory = $this->createMock(PointsServiceResponseFactory::class);
        $this->configProvider = $this->createMock(ConfigProvider::class);

        $this->service = new PointsApiService(
            $this->httpClientFactory,
            $this->pointDataFactory,
            $this->pointsServiceResponseFactory,
            $this->configProvider
        );
    }

    public function testGetPoints(): void
    {
        $params = ['param1' => 'value1'];
        $data = ['items' => [['id' => '1'], ['id' => '2']]];

        $httpClient = $this->createMock(HttpClient::class);
        $this->httpClientFactory->expects($this->once())
            ->method('create')
            ->willReturn($httpClient);

        $this->configProvider->expects($this->once())
            ->method('getApiBaseUrl')
            ->willReturn('http://example.com');

        $httpClient->expects($this->once())
            ->method('get')
            ->with('http://example.com/v1/points', $params)
            ->willReturn($this->createMock(HttpClient\Response::class));

        $this->pointsServiceResponseFactory->expects($this->once())
            ->method('create')
            ->with(['data' => ['items' => [$this->createMock(\InPost\Shipment\Api\Data\PointDataInterface::class), $this->createMock(\InPost\Shipment\Api\Data\PointDataInterface::class)]]])
            ->willReturn($this->createMock(\InPost\Shipment\Api\Data\PointsServiceResponseInterface::class));

        $this->pointDataFactory->expects($this->exactly(2))
            ->method('create')
            ->withConsecutive(
                [['data' => ['id' => '1']]],
                [['data' => ['id' => '2']]]
            )
            ->willReturn($this->createMock(\InPost\Shipment\Api\Data\PointDataInterface::class));

        $this->service->getPoints(new PointsServiceRequest($params));
    }

    public function testGetPointsWithException(): void
    {
        $params = ['param1' => 'value1'];

        $httpClient = $this->createMock(HttpClient::class);
        $this->httpClientFactory->expects($this->once());
    }
}