<?php

namespace Inpost\Shipment\Carrier;

use InPost\Shipment\Api\Data\PointsServiceRequestFactory;
use InPost\Shipment\Service\Api\PointsApiService;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

class Inpost  extends AbstractCarrier implements CarrierInterface
{
    const ALLOWED_METHODS = 'inpost';

    private const PRICE = 10;

    const CARRIER_CODE = 'inpost';

    const METHOD_LOCKER = 'inpost_loceker';

    const METHOD_COURIER = 'inpost_courier';

    protected $_rateMethodFactory;

    protected $_code = 'inpost';

    /**
     * @var ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var MethodFactory
     */
    private $rateMethodFactory;

    private \InPost\Shipment\Service\Api\PointsApiService $pointsApiService;

    private PointsServiceRequestFactory $pointsServiceRequestFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param PointsApiService $pointsApiService
     * @param PointsServiceRequestFactory $pointsServiceRequestFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        \InPost\Shipment\Service\Api\PointsApiService $pointsApiService,
        \InPost\Shipment\Api\Data\PointsServiceRequestFactory $pointsServiceRequestFactory,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
        $this->pointsApiService = $pointsApiService;
        $this->pointsServiceRequestFactory = $pointsServiceRequestFactory;
    }

    /**
     * Collect rates
     *
     * @param RateRequest $request
     * @return Result
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectRates(RateRequest $request)
    {
        /** @var Result $result */
        $result = $this->rateResultFactory->create();
        if (!$this->getConfigData('general/active')) {
            return $result;
        }

        $pointId = $this->fetchOption($request);
        $pointInfo = 'InPost';
        if ($pointId) {
            $apiPointsRequest = $this->pointsServiceRequestFactory->create();
            $apiPointsRequest->setName($pointId);
            $points = $this->pointsApiService->getPoints($apiPointsRequest);
            $point = $points->getFirstItem();
            $pointInfo = $point->getAddressInfo();
        }


        $method = $this->rateMethodFactory->create();
        $method->setCarrier(self::CARRIER_CODE);
        $method->setMethod(self::ALLOWED_METHODS);

        $method->setCarrierTitle('InPost');
        $method->setMethodTitle($pointInfo);
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
    private function fetchOption(RateRequest $request) : ?string
    {
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = current($request->getAllItems());
        if (! $quoteItem) {
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
}
