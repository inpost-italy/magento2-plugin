<?php
declare(strict_types=1);

namespace InPost\Shipment\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider
{
    const ALLOW_INPOST_DELIVERY_CATEGORY_ATTRIBUTE = 'allow_inpost_delivery22';
    const XML_PATH_INPOST_GENERAL_ACTIVE = 'carriers/inpost/general/active';
    const XML_PATH_INPOST_GENERAL_COMPANY_NAME = 'carriers/inpost/general/company_name';
    const XML_PATH_INPOST_GENERAL_EMAIL = 'carriers/inpost/general/email';
    const XML_PATH_INPOST_GENERAL_MOBILE_PHONE_NUMBER = 'carriers/inpost/general/mobile_phone_number';

    const XML_PATH_INPOST_FLOW_TYPE = 'carriers/inpost/flow/type';
    const XML_PATH_INPOST_FLOW_STREET = 'carriers/inpost/flow/street';
    const XML_PATH_INPOST_FLOW_BUILDING = 'carriers/inpost/flow/building';
    const XML_PATH_INPOST_FLOW_CITY = 'carriers/inpost/flow/city';
    const XML_PATH_INPOST_FLOW_POSTCODE = 'carriers/inpost/flow/postcode';

    const XML_PATH_INPOST_CREDENTIALS_DEBUG = 'carriers/inpost/credentials/debug';
    const XML_PATH_INPOST_CREDENTIALS_MERCHANT_ID = 'carriers/inpost/credentials/merchant_id';
    const XML_PATH_INPOST_CREDENTIALS_API_KEY = 'carriers/inpost/credentials/api_key';

    const URL_SHIPX_STAGING = 'https://stage-api-shipx-it.easypack24.net';
    const URL_SHIPX_PRODUCTION = 'https://api-shipx-it.easypack24.net';

    /** @var ScopeConfigInterface */
    protected $scopeConfig;
    protected $sandboxApiUrl = "https://stage-api-it-points.easypack24.net";
    protected $productionApiUrl = "https://api-it-local-points.easypack24.net";

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
    public function isActive(): bool
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
     * @return string|null
     */
    public function getCompanyName(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_INPOST_GENERAL_COMPANY_NAME);
    }

    public function getService()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_INPOST_FLOW_TYPE);
    }

    /**
     * @return array
     */
    public function getSenderAddress() : array
    {
        return [
            'city'              => $this->scopeConfig->getValue(self::XML_PATH_INPOST_FLOW_CITY),
            'post_code'         => $this->scopeConfig->getValue(self::XML_PATH_INPOST_FLOW_POSTCODE),
            'street'            => $this->scopeConfig->getValue(self::XML_PATH_INPOST_FLOW_STREET),
            'building_number'   => $this->scopeConfig->getValue(self::XML_PATH_INPOST_FLOW_BUILDING),
        ];
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_INPOST_GENERAL_EMAIL);
    }

    /**
     * @return string|null
     */
    public function getMobilePhoneNumber(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_INPOST_GENERAL_MOBILE_PHONE_NUMBER);
    }

    /**
     * @return bool
     */
    public function isDebugModeEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(self::XML_PATH_INPOST_CREDENTIALS_DEBUG);
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

    public function getReturnUrl(): ?string
    {
        return $this->scopeConfig->getValue('carriers/inpost/return/return_url');
    }
}
