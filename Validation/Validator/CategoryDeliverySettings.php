<?php

namespace InPost\Shipment\Validation\Validator;

use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Validation\AddressRateValidator;
use InPost\Shipment\Validation\ValidationException;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Item;

class CategoryDeliverySettings implements AddressRateValidator
{
    private ScopeConfigInterface $scopeConfig;
    private CollectionFactory $categoryCollectionFactory;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->categoryCollectionFactory = $collectionFactory;
    }

    public function validate(RateRequest $rateRequest): void
    {
        $categoryIds = [];
        /** @var Item $item */
        foreach ($rateRequest->getAllItems() as $item) {
            $categoryIds = array_merge($categoryIds, $item->getProduct()->getCategoryIds());
        }

        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addFieldToFilter('entity_id', ['in' => $categoryIds])
            ->addAttributeToSelect(ConfigProvider::ALLOW_INPOST_DELIVERY_CATEGORY_ATTRIBUTE);

        foreach ($categoryCollection as $category) {
            $allowCategory = $category->getData(ConfigProvider::ALLOW_INPOST_DELIVERY_CATEGORY_ATTRIBUTE);
            if (is_null($allowCategory)) {
                continue;
            }

            if (!$allowCategory) {
                throw new ValidationException('Category validation is failed');
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
