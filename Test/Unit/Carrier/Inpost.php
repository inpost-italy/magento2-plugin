<?php

namespace InPost\Shipment\Test\Unit\Carrier;


use InPost\Shipment\Api\Data\PointsServiceRequestFactory;
use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Service\Api\ApiServiceProvider;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Logger\Monolog;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use PHPUnit\Framework\TestCase;

class InpostTest extends TestCase
{
    /**
     * @var Inpost
     */
    private $model;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $scopeConfig = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $rateErrorFactory = $this->getMockBuilder(ErrorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $logger = $this->getMockBuilder(Monolog::class)
            ->disableOriginalConstructor()
            ->getMock();
        $rateResultFactory = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $rateMethodFactory = $this->getMockBuilder(MethodFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $apiServiceProvider = $this->getMockBuilder(ApiServiceProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pointsServiceRequestFactory = $this->getMockBuilder(PointsServiceRequestFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $checkoutSession = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
        $categoryCollectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configProvider = $this->getMockBuilder(ConfigProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $this->objectManager->getObject(Inpost::class, [
            $scopeConfig,
            $rateErrorFactory,
            $logger,
            $rateResultFactory,
            $rateMethodFactory,
            $apiServiceProvider,
            $pointsServiceRequestFactory,
            $checkoutSession,
            $categoryCollectionFactory,
            $configProvider,
        ]);
    }

    public function testCollectRates(): void
    {
        $request = $this->getMockBuilder(RateRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resultFactory = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $result = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->getMock();
        $methodFactory = $this->getMockBuilder(MethodFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $method = $this->getMockBuilder(Method::class)
            ->disableOriginalConstructor()
            ->getMock();

        $rateResultFactory = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}