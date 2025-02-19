<?php
declare(strict_types=1);

namespace InPost\Shipment\Carrier;

use InPost\Shipment\Api\Data\PointsServiceRequestFactory;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Api\PointsApiService;
use InPost\Shipment\Service\Quote\PointsExtractor;
use InPost\Shipment\Validation\ValidationException;
use InPost\Shipment\Validation\ValidatorPool;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
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
    const CARRIER_TITLE_FREESHIPPING = 'InPost Freeshipping';

    const CARRIER_CODE = 'inpost';

    const METHOD_LOCKER = 'inpost_locker';

    const METHOD_COURIER = 'inpost_courier';

    protected $_code = 'inpost';

    /** @var ResultFactory */
    private $rateResultFactory;

    /** @var MethodFactory */
    private $rateMethodFactory;

    /** @var ConfigProvider */
    private $configProvider;

    private ValidatorPool $validationPool;

    private \InPost\Shipment\Service\Quote\PointsExtractor $pointsExtractor;

    /**
     * @var PointsServiceRequestFactory
     */
    private $pointsServiceRequestFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param PointsServiceRequestFactory $pointsServiceRequestFactory
     * @param ValidatorPool $validationPool
     * @param ConfigProvider $configProvider
     * @param PointsExtractor $pointsExtractor
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        PointsServiceRequestFactory $pointsServiceRequestFactory,
        ValidatorPool $validationPool,
        ConfigProvider $configProvider,
        \InPost\Shipment\Service\Quote\PointsExtractor $pointsExtractor,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->configProvider = $configProvider;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->pointsServiceRequestFactory = $pointsServiceRequestFactory;
        $this->validationPool = $validationPool;
        $this->pointsExtractor = $pointsExtractor;
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

        try {
            $this->validationPool->validate($request);
        } catch (ValidationException $e) {
            $error = $this->_rateErrorFactory->create();
            $error->setCarrier($this->_code);
            $error->setCarrierTitle($this->getConfigData('title'));
            $errorMsg = $this->getConfigData('general/specificerrmsg');
            $error->setErrorMessage(
                $errorMsg ? $errorMsg : __(
                    'Sorry, but we can\'t deliver to the destination country with this shipping module.'
                )
            );

            // We haven't passed validation
            return $result;
        }


        $this->_updateFreeMethodQuote($request);

        if ($request->getFreeShipping() || $this->isFreeShippingRequired($request)) {
            /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
            $method = $this->rateMethodFactory->create();

            $method->setCarrier(self::CARRIER_CODE);
            $currierName = $this->getConfigData('general/free_shipping_name_courier');
            if(empty($currierName)) {
                $currierName = __('Pick-up Points');
            }
            $method->setCarrierTitle($currierName);

            $method->setMethod(self::ALLOWED_METHODS);
            $method->setMethodTitle(self::CARRIER_TITLE);

            $method->setPrice('0.00');
            $method->setCost('0.00');

            $result->append($method);
        } else {
            $method = $this->rateMethodFactory->create();
            $method->setCarrier(self::CARRIER_CODE);
            $method->setMethod(self::ALLOWED_METHODS);
    
            $method->setCarrierTitle(self::CARRIER_TITLE );
            $method->setMethodTitle(self::CARRIER_TITLE);
            $method->setPrice($this->getConfigData('general/price'));
            $method->setCost($this->getConfigData('general/price'));
            $result->append($method);
        }


        return $result;
    }


    protected function _updateFreeMethodQuote($request)
    {
        $freeShipping = false;
        $items = $request->getAllItems();
        $c = count($items);
        for ($i = 0; $i < $c; $i++) {
            if ($items[$i]->getProduct() instanceof \Magento\Catalog\Model\Product) {
                if ($items[$i]->getFreeShipping()) {
                    $freeShipping = true;
                } else {
                    return;
                }
            }
        }
        if ($freeShipping) {
            $request->setFreeShipping(true);
        }
    }


        /**
     * Check subtotal for allowed free shipping
     *
     * @param RateRequest $request
     *
     * @return bool
     */
    private function isFreeShippingRequired(RateRequest $request): bool
    {
        if(!$this->getConfigData('general/free_shipping_enable')) {
            return false;
        }

        $minSubtotal = $request->getPackageValueWithDiscount();
        if ($request->getBaseSubtotalWithDiscountInclTax()
            && $this->getConfigFlag('tax_including')) {
            $minSubtotal = $request->getBaseSubtotalWithDiscountInclTax();
        }
        return $minSubtotal >= $this->getConfigData('general/free_shipping_subtotal');
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
}
