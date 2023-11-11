<?php
declare(strict_types=1);

namespace InPost\Shipment\Carrier;

use InPost\Shipment\Api\Data\PointsServiceRequestFactory;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Api\PointsApiService;
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
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param PointsApiService $pointsApiService
     * @param Session $checkoutSession
     * @param ConfigProvider $configProvider
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

        $pointInfo = $this->getPointInfo($request);

        $method = $this->rateMethodFactory->create();
        $method->setCarrier(self::CARRIER_CODE);
        $method->setMethod(self::ALLOWED_METHODS);

        $method->setCarrierTitle(self::CARRIER_TITLE );
        $method->setMethodTitle($pointInfo ?: '');
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
    private function getPointInfo(RateRequest $request): ?string
    {
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = current($request->getAllItems());
        if (!$quoteItem) {
            return null;
        }

        return $this->pointsExtractor->getInpostPoint($quoteItem->getQuoteId());
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
