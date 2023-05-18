<?php

namespace InPost\Shipment\Config\Checkout;

use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Config\WidgetConfigProvider;
use \Magento\Checkout\Model\ConfigProviderInterface;

class InpostConfigProvider implements ConfigProviderInterface
{
    /** @var WidgetConfigProvider  */
    private $widgetConfigProvider;

    /** @var ConfigProvider  */
    private $configProvider;

    /**
     * @param WidgetConfigProvider $widgetConfigProvider
     */
    public function __construct(
        WidgetConfigProvider $widgetConfigProvider,
        ConfigProvider $configProvider
    ) {
        $this->widgetConfigProvider = $widgetConfigProvider;
        $this->configProvider = $configProvider;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'inpost' => [
                'mapType' => $this->widgetConfigProvider->getMapType(),
                'gMapsApiKey' => $this->widgetConfigProvider->getGmapsApiKey(),
                'pointsApiUrl' => $this->configProvider->getApiBaseUrl() . '/v1/',
            ]
        ];
    }
}
