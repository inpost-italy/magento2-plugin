<?php

namespace Inpost\Shipment\Carrier;

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


    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
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
        $method = $this->rateMethodFactory->create();

        if (!$this->getConfigData('general/active')) {
            return $result;
        }

        $method->setCarrier(self::CARRIER_CODE);
        $method->setMethod(self::ALLOWED_METHODS);
        $method->setCarrierTitle('InPost');
        $method->setMethodTitle('InPost');
        $method->setPrice($this->getConfigData('general/price'));
        $method->setCost($this->getConfigData('general/price'));
        $result->append($method);

        return $result;
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
