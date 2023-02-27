<?php
declare(strict_types=1);

namespace InPost\Shipment\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider
{
    const ALLOW_INPOST_DELIVERY_CATEGORY_ATTRIBUTE = 'allow_inpost_delivery';
    const XML_PATH_INPOST_GENERAL_ACTIVE = 'carriers/inpost/general/active';
    const XML_PATH_INPOST_GENERAL_DEBUG = 'carriers/inpost/general/debug';
    const XML_PATH_INPOST_CREDENTIALS_MERCHANT_ID = 'carriers/inpost/credentials/merchant_id';
    const XML_PATH_INPOST_CREDENTIALS_API_KEY = 'carriers/inpost/credentials/api_key';

    const URL_SHIPX_STAGING = 'https://stage-api-shipx-it.easypack24.net';

    const URL_SHIPX_PRODUCTION = 'https://api-shipx-it.easypack24.net';


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
     * @return bool
     */
    public function isActive():bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_INPOST_GENERAL_ACTIVE);
    }

    /**
     * @return string
     */
    public function getApiBaseUrl(): string
    {
        return $this->isDebugModeEnabled() ? $this->sandboxApiUrl : $this->productionApiUrl;
    }

    public function getShipXBaseUrl(): string
    {
        return $this->isDebugModeEnabled() ? self::URL_SHIPX_STAGING : self::URL_SHIPX_PRODUCTION;
    }

    /**
     * @return bool
     */
    public function isDebugModeEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_INPOST_GENERAL_DEBUG);
    }

    /**
     * @return string|null
     */
    public function getMerchantId(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_INPOST_CREDENTIALS_MERCHANT_ID);
    }

    /**
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_INPOST_CREDENTIALS_API_KEY);
    }
}
