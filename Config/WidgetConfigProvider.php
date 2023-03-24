<?php

namespace InPost\Shipment\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class WidgetConfigProvider
{
    const WIDGET_SEARCH_TYPE = 'carriers/inpost/widget/search_type';

    const WIDGET_GMAP_API_KEY = 'carriers/inpost/widget/gmaps_api_key';

    const WIDGET_MAP_TYPE = 'carriers/inpost/widget/map_type';


    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getMapType()
    {
        return $this->scopeConfig->getValue(self::WIDGET_MAP_TYPE);
    }

    public function getGmapsApiKey()
    {
        return $this->scopeConfig->getValue(self::WIDGET_GMAP_API_KEY);
    }

    public function getSearchType()
    {
        return $this->scopeConfig->getValue(self::WIDGET_SEARCH_TYPE);
    }
}
