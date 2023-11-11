<?php

namespace InPost\Shipment\Validation\Validator;

use InPost\Shipment\Validation\AddressRateValidator;
use InPost\Shipment\Validation\ValidationException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;

class WeightLimits implements AddressRateValidator
{
    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function validate(RateRequest $rateRequest): void
    {
        $totalWeight = 0;
        $itemWeightLimit = (float)$this->getConfigData('delivery_options/max_item_weight', $rateRequest->getStoreId());
        $totalCartWeightLimit = (float)$this->getConfigData('delivery_options/max_total_cart_weight', $rateRequest->getStoreId());

        foreach ($rateRequest->getAllItems() as $item) {
            if ($itemWeightLimit > 0 && $item->getWeight() > $itemWeightLimit) {
                throw new ValidationException('Weight validation failed');
            }
            $totalWeight += ($item->getWeight() * $item->getQty());
        }

        if ($totalCartWeightLimit > 0 && $totalWeight > $totalCartWeightLimit) {
            throw new ValidationException('Weight validation failed');
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
