<?php
declare(strict_types=1);

namespace InPost\Shipment\Carrier;

use InPost\Shipment\Api\Data\PointsServiceRequestFactory;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Api\PointsApiService;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Item;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Checkout\Model\Session;
use Psr\Log\LoggerInterface;

class Inpost extends AbstractCarrier implements CarrierInterface
{
    const ALLOWED_METHODS = 'inpost';

    const CARRIER_TITLE = 'InPost';

    const CARRIER_CODE = 'inpost';

    const METHOD_LOCKER = 'inpost_loceker';

    const METHOD_COURIER = 'inpost_courier';

    protected $_code = 'inpost';

    /** @var ResultFactory */
    private $rateResultFactory;

    /** @var MethodFactory */
    private $rateMethodFactory;

    /** @var Session */
    private $checkoutSession;

    private PointsApiService $pointsApiService;

    private PointsServiceRequestFactory $pointsServiceRequestFactory;

    /** @var CollectionFactory */
    private $categoryCollectionFactory;

    /** @var ConfigProvider */
    private $configProvider;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param PointsApiService $pointsApiService
     * @param PointsServiceRequestFactory $pointsServiceRequestFactory
     * @param Session $checkoutSession
     * @param CollectionFactory $categoryCollectionFactory
     * @param ConfigProvider $configProvider
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        PointsApiService $pointsApiService,
        PointsServiceRequestFactory $pointsServiceRequestFactory,
        Session $checkoutSession,
        CollectionFactory $categoryCollectionFactory,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->checkoutSession = $checkoutSession;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->configProvider = $configProvider;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->pointsApiService = $pointsApiService;
        $this->pointsServiceRequestFactory = $pointsServiceRequestFactory;
    }

    public function isShippingLabelsAvailable()
    {
        return true;
    }

    public function requestToShipment($request)
    {
        return new \Magento\Framework\DataObject([
            'info' => [
                'label_content' => 'label',
                'tracking_number' => 'TRACK'
            ]
        ]);
    }

    public function isTrackingAvailable()
    {
        return true;
    }

    public function getCustomizableContainerTypes()
    {
        return [];
    }

    public function getContainerTypes($params = null, $storeId = null)
    {
        /* @codingStandardsIgnoreEnd */
        return [
            'looker_S' => __('Inpost locker S'),
            'looker_M' => __('Inpost locker M'),
            'looker_L' => __('Inpost locker L'),
        ];
    }


    /**
     * Collect rates
     *
     * @param RateRequest $request
     * @return Result
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function collectRates(RateRequest $request)
    {
        /** @var Result $result */
        $result = $this->rateResultFactory->create();
        if (!$this->configProvider->isActive()) {
            return $result;
        }

        // Allowed countries validation
        if (!$this->checkAvailableShipCountries($request)) {
            return $result;
        }

        // Weight limitations check
        if (!$this->validateWeightLimits()) {
            return $result;
        }

        // Validate category delivery settings
        if (!$this->validateCategoryDeliverySettings()) {
            return $result;
        }

        $pointId = $this->fetchOption($request);
        $methodTitle = $pointId ? $this->getPointInfo($pointId) : self::CARRIER_TITLE;

        $method = $this->rateMethodFactory->create();
        $method->setCarrier(self::CARRIER_CODE);
        $method->setMethod(self::ALLOWED_METHODS);

        $method->setCarrierTitle(self::CARRIER_TITLE);
        $method->setMethodTitle($methodTitle);
        $method->setPrice($this->getConfigData('general/price'));
        $method->setCost($this->getConfigData('general/price'));
        $result->append($method);

        return $result;
    }

    /**
     * @param RateRequest $request
     *
     * @return array|null
     */
    private function fetchOption(RateRequest $request): ?string
    {
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = current($request->getAllItems());
        if (!$quoteItem) {
            return null;
        }
        $address = $quoteItem->getQuote()->getShippingAddress();

        return $address->getInpostPointId();
    }


    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [self::METHOD_LOCKER, self::METHOD_COURIER];
    }

    private function getPointInfo(?string $pointId): ?string
    {
        $apiPointsRequest = $this->pointsServiceRequestFactory->create();
        $apiPointsRequest->setName($pointId);
        $points = $this->pointsApiService->getPoints($apiPointsRequest);

        return !$points->isEmpty() ? $points->getFirstItem()->getAddressInfo() : null;
    }

    /**
     * Validate request for available ship countries.
     *
     * @param \Magento\Framework\DataObject $request
     * @return $this|bool|false|\Magento\Framework\Model\AbstractModel
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function checkAvailableShipCountries(\Magento\Framework\DataObject $request)
    {
        $speCountriesAllow = $this->getConfigData('general/sallowspecific');
        /*
         * for specific countries, the flag will be 1
         */
        if ($speCountriesAllow && $speCountriesAllow == 1) {
            $showMethod = $this->getConfigData('general/showmethod');
            $availableCountries = [];
            if ($this->getConfigData('general/specificcountry')) {
                $availableCountries = explode(',', $this->getConfigData('general/specificcountry'));
            }
            if ($availableCountries && in_array($request->getDestCountryId(), $availableCountries)) {
                return $this;
            } elseif ($showMethod && (!$availableCountries || $availableCountries && !in_array(
                        $request->getDestCountryId(),
                        $availableCountries
                    ))
            ) {
                $error = $this->_rateErrorFactory->create();
                $error->setCarrier($this->_code);
                $error->setCarrierTitle($this->getConfigData('title'));
                $errorMsg = $this->getConfigData('general/specificerrmsg');
                $error->setErrorMessage(
                    $errorMsg ? $errorMsg : __(
                        'Sorry, but we can\'t deliver to the destination country with this shipping module.'
                    )
                );

                return $error;
            } else {
                /*
                 * The admin set not to show the shipping module if the delivery country
                 * is not within specific countries
                 */
                return false;
            }
        }

        return $this;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function validateWeightLimits(): bool
    {
        $totalWeight = 0;
        $itemWeightLimit = (float)$this->getConfigData('delivery_options/max_item_weight');
        $totalCartWeightLimit = (float)$this->getConfigData('delivery_options/max_total_cart_weight');

        foreach ($this->checkoutSession->getQuote()->getAllItems() as $item) {
            if ($itemWeightLimit > 0 && $item->getWeight() > $itemWeightLimit) {
                return false;
            }
            $totalWeight += ($item->getWeight() * $item->getQty());
        }

        if ($totalCartWeightLimit > 0 && $totalWeight > $totalCartWeightLimit) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validateCategoryDeliverySettings(): bool
    {
        $categoryIds = [];

        /** @var Item $item */
        foreach ($this->checkoutSession->getQuote()->getAllItems() as $item) {
            $categoryIds = array_merge($categoryIds, $item->getProduct()->getCategoryIds());
        }

        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addFieldToFilter('entity_id', ['in' => $categoryIds])
            ->addAttributeToSelect(ConfigProvider::ALLOW_INPOST_DELIVERY_CATEGORY_ATTRIBUTE);

        foreach ($categoryCollection as $category) {
            if (!$category->getData(ConfigProvider::ALLOW_INPOST_DELIVERY_CATEGORY_ATTRIBUTE)) {
                return false;
            }
        }

        return true;
    }
}
