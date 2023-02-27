<?php

namespace InPost\Shipment\Config\Checkout;

use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Config\WidgetConfigProvider;
use \Magento\Checkout\Model\ConfigProviderInterface;

class InpostConfigProvider implements ConfigProviderInterface
{
    private WidgetConfigProvider $widgetConfigProvider;

    private ConfigProvider $configProvider;

    /**
     * @param WidgetConfigProvider $widgetConfigProvider
     */
    public function __construct(
        WidgetConfigProvider $widgetConfigProvider,
        ConfigProvider $configProvider
    )
    {
        $this->widgetConfigProvider = $widgetConfigProvider;
        $this->configProvider = $configProvider;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $additionalVariables['inpost'] = [
            'mapType' => $this->widgetConfigProvider->getMapType(),
            'searchType' => $this->widgetConfigProvider->getSearchType(),
            'pointsApiUrl' => $this->configProvider->getApiBaseUrl() . '/v1/',
        ];

        return $additionalVariables;
    }
}