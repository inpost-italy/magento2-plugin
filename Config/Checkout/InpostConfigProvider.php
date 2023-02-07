<?php

namespace InPost\Shipment\Config\Checkout;

use InPost\Shipment\Config\WidgetConfigProvider;
use \Magento\Checkout\Model\ConfigProviderInterface;

class InpostConfigProvider implements ConfigProviderInterface
{
    private WidgetConfigProvider $widgetConfigProvider;

    /**
     * @param WidgetConfigProvider $widgetConfigProvider
     */
    public function __construct(WidgetConfigProvider $widgetConfigProvider)
    {
        $this->widgetConfigProvider = $widgetConfigProvider;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $additionalVariables['inpost'] = [
            'map_type' => $this->widgetConfigProvider->getMapType(),
            'search_type' => $this->widgetConfigProvider->getSearchType(),
        ];

        return $additionalVariables;
    }
}