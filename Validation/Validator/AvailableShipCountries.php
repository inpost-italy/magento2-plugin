<?php

namespace InPost\Shipment\Validation\Validator;

use InPost\Shipment\Validation\AddressRateValidator;
use InPost\Shipment\Validation\ValidationException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;

class AvailableShipCountries implements AddressRateValidator
{
    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function validate(RateRequest $rateRequest): void
    {
        $speCountriesAllow = $this->getConfigData('general/sallowspecific', $rateRequest->getStoreId());
        /*
         * for specific countries, the flag will be 1
         */
        if ($speCountriesAllow && $speCountriesAllow == 1) {
            $showMethod = $this->getConfigData('general/showmethod');
            $availableCountries = [];
            if ($this->getConfigData('general/specificcountry')) {
                $availableCountries = explode(',', $this->getConfigData('general/specificcountry'));
            }
            if ($availableCountries && in_array($rateRequest->getDestCountryId(), $availableCountries)) {
                return;
            } elseif ($showMethod && (!$availableCountries || $availableCountries && !in_array(
                        $rateRequest->getDestCountryId(),
                        $availableCountries
                    ))
            ) {
                $errorMsg = $this->getConfigData('general/specificerrmsg');
                throw new ValidationException($errorMsg ?: __(
                    'Sorry, but we can\'t deliver to the destination country with this shipping module.'
                ));
            } else {
                 throw new ValidationException();
            }
        }
    }

    private function getConfigData($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
