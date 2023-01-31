<?php
declare(strict_types=1);

namespace InPost\Shipment\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Tests\NamingConvention\true\string;

class ConfigProvider
{
    const XML_PATH_INPOST_GENERAL_DEBUG = 'carriers/inpost/general/debug';

    /** @var ScopeConfigInterface */
    protected $scopeConfig;
    protected $sandboxApiUrl = "https://stage-api-it-points-new.easypack24.net";
    protected $productionApiUrl = "https://api-it-points-new.easypack24.net";

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getApiBaseUrl(): string
    {
        return $this->isDebugModeEnabled() ? $this->sandboxApiUrl : $this->productionApiUrl;
    }

    /**
     * @return bool
     */
    public function isDebugModeEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_INPOST_GENERAL_DEBUG);
    }
}
