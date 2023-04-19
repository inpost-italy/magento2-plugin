<?php

namespace InPost\Shipment\Test\Unit\Config\Checkout;

use InPost\Shipment\Config\Checkout\InpostConfigProvider;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Config\WidgetConfigProvider;
use PHPUnit\Framework\TestCase;

class InpostConfigProviderTest extends TestCase
{
    /**
     * @var WidgetConfigProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $widgetConfigProviderMock;

    /**
     * @var ConfigProvider|\PHPUnit\Framework\MockObject\MockObject
     */
    private $configProviderMock;

    /**
     * @var InpostConfigProvider
     */
    private $inpostConfigProvider;

    protected function setUp(): void
    {
        $this->widgetConfigProviderMock = $this->createMock(WidgetConfigProvider::class);
        $this->configProviderMock = $this->createMock(ConfigProvider::class);

        $this->inpostConfigProvider = new InpostConfigProvider(
            $this->widgetConfigProviderMock,
            $this->configProviderMock
        );
    }

    public function testGetConfigReturnsExpectedData()
    {
        $this->widgetConfigProviderMock->expects($this->once())
            ->method('getMapType')
            ->willReturn('customMapType');

        $this->widgetConfigProviderMock->expects($this->once())
            ->method('getSearchType')
            ->willReturn('customSearchType');

        $this->widgetConfigProviderMock->expects($this->once())
            ->method('getGmapsApiKey')
            ->willReturn('customGmapsApiKey');

        $this->configProviderMock->expects($this->once())
            ->method('getApiBaseUrl')
            ->willReturn('https://custom-api-url.com');

        $expectedConfig = [
            'inpost' => [
                'mapType' => 'customMapType',
                'searchType' => 'customSearchType',
                'gMapsApiKey' => 'customGmapsApiKey',
                'pointsApiUrl' => 'https://custom-api-url.com/v1/'
            ]
        ];

        $this->assertEquals($expectedConfig, $this->inpostConfigProvider->getConfig());
    }
}
