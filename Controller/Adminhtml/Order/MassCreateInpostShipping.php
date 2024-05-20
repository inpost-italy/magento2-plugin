<?php
namespace InPost\Shipment\Controller\Adminhtml\Order;

use InPost\Shipment\Api\Data\Shipment as InpostShipment;
use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Logger\Logger;
use InPost\Shipment\Model\Config\Source\Product\LockerSize;
use InPost\Shipment\Service\Api\GetLabelService;
use InPost\Shipment\Service\Http\HttpClientException;
use InPost\Shipment\Service\Management\ShipmentManager;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Helper\Data as SalesData;
use Magento\Sales\Model\Convert\Order as OrderConverter;
use Magento\Sales\Model\Order\Email\Sender\ShipmentSender;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\Order\Shipment\TrackFactory;

class MassCreateInpostShipping extends \Magento\Backend\App\Action
{
    /**
     * @var OrderRepositoryInterface
     */
    protected OrderRepositoryInterface $orderRepository;

    /**
     * @var OrderConverter
     */
    protected OrderConverter $orderConverter;

    /**
     * @var GetLabelService
     */
    protected ShipmentRepository $shipmentRepository;

    /**
     * @var ShipmentManager
     */
    protected ShipmentManager $shipmentManager;

    /**
     * @var SalesData
     */
    protected SalesData $salesData;

    /**
     * @var ShipmentSender
     */
    protected $shipmentSender;

    /**
     * @var ConfigProvider
     */
    protected ConfigProvider $configProvider;

    /**
     * @var LockerSize
     */
    protected LockerSize $lockerSize;

    /**
     * @var TrackFactory
     */
    protected $trackFactory;

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderConverter $orderConverter
     * @param ShipmentRepository $shipmentRepository
     * @param ShipmentManager $shipmentManager
     * @param SalesData $salesData
     * @param ShipmentSender $shipmentSender
     * @param ConfigProvider $configProvider
     * @param LockerSize $lockerSize
     * @param TrackFactory $trackFactory
     * @param Logger $logger
     * @param Context $context
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderConverter $orderConverter,
        ShipmentRepository $shipmentRepository,
        ShipmentManager $shipmentManager,
        SalesData $salesData,
        ShipmentSender $shipmentSender,
        ConfigProvider $configProvider,
        LockerSize $lockerSize,
        TrackFactory $trackFactory,
        Logger $logger,
        Context $context
    )
    {
        $this->orderRepository = $orderRepository;
        $this->orderConverter = $orderConverter;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentManager = $shipmentManager;
        $this->salesData = $salesData;
        $this->shipmentSender = $shipmentSender;
        $this->configProvider = $configProvider;
        $this->lockerSize = $lockerSize;
        $this->trackFactory = $trackFactory;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute action based on request and return result
     *
     * @return ResultInterface
     * @throws NotFoundException
     */
    public function execute(): ResultInterface
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $this->logger->info('START Mass Ship Order');
        try {
            $orderIds = $this->getRequest()->getParam('selected');
            $inPostOrderCounter = 0;
            foreach ($orderIds as $orderId)
            {
                $order = $this->orderRepository->get($orderId);
                $this->logger->info('Order: ' . $order->getIncrementId());
                if ($this->isInpostOrder($order))
                {
                    $inPostOrderCounter++;
                    if(count($order->getShipmentsCollection()) == 0)
                    {
                        try {
                            $trackingNumber = $this->shipOrder($order);
                            $this->logger->info('    Tracking Number: ' . $trackingNumber);
                        }
                        catch (HttpClientException $e) {
                            $this->messageManager->addErrorMessage($e->getMessage());
                            $this->logger->error('    ' . trim($e->getMessage()));
                        }
                        catch (\Exception $exception)
                        {
                            $this->messageManager->addErrorMessage($exception->getMessage());
                            $this->logger->error('    ' . trim($exception->getMessage()));
                        }
                    }
                    else
                    {
                        $this->logger->info('    has already been shipped');
                    }
                }
                else
                {
                    $this->logger->info('    is not an InPost order');
                }
            }
            if ($inPostOrderCounter == 0)
            {
                $message = __('No InPost Orders was selected.');
                throw new \Exception($message->__toString());
            }
        }
        catch (\Exception $exception)
        {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        $this->logger->info('END Mass Ship Order');
        return $resultRedirect->setPath('sales/order');
    }

    /**
     * Check if current order has inpost shipment
     *
     * @param $order
     * @return bool
     * @throws InputException
     * @throws NoSuchEntityException
     */
    private function isInpostOrder($order) : bool
    {
        return strpos($order->getShippingMethod(), Inpost::CARRIER_CODE) !== false;
    }


    /**
     * @param OrderInterface $order
     * @return void
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    protected function shipOrder(OrderInterface $order): string
    {
        $trackingNumber = '';
        // Check if order has already shipped or can be shipped
        if ($order->canShip())
        {
            // Initialize the order shipment object
            $shipment = $this->orderConverter->toShipment($order);
            $usedLockerSize = $this->configProvider->getDefaultInpostLockerSize();
            foreach ($order->getAllItems() as $orderItem)
            {
                if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual())
                {
                    continue;
                }
                $lockerSize = $this->getLockerSize($orderItem->getProduct());
                if($lockerSize > $usedLockerSize)
                {
                    $usedLockerSize = $lockerSize;
                }
                $qtyShipped = $orderItem->getQtyToShip();
                $shipmentItem = $this->orderConverter->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                $shipment->addItem($shipmentItem);
            }
            // Register shipment
            $shipment->register();

            $pointId = $order->getShippingAddress()->getInpostPointId();
            $inpostShipment = $this->shipmentManager->createShipment(
                $order,
                $pointId,
                strtolower($this->lockerSize->getOptionText($usedLockerSize))
            );
            $this->addTrack($shipment, $inpostShipment);
            $this->addTrackingNumberToOrderShipment($shipment, (string) $inpostShipment->getId());

            $this->shipmentRepository->save($shipment);

            $trackingNumber = $inpostShipment->getTrackingNumber();

            if ($this->salesData->canSendNewShipmentEmail())
            {
                $this->shipmentSender->send($shipment);
            }
        }
        else
        {
            $this->logger->warning('    can\'t be shipped');
        }
        return $trackingNumber;
    }

    /**
     * @param Shipment $shipment
     * @param string $trackingNumber
     * @return void
     * @throws CouldNotSaveException
     */
    protected function addTrackingNumberToOrderShipment(Shipment $shipment, string $trackingNumber)
    {
        $shipment->setData('inpost_shipment_id', $trackingNumber);
        $this->shipmentRepository->save($shipment);
    }

    /**
     * @param Shipment $shipment
     * @param InPostShipment $inpostShipment
     * @return void
     */
    protected function addTrack(Shipment $shipment, InpostShipment $inpostShipment)
    {
        $trackData = [
            'carrier_code' => Inpost::CARRIER_CODE,
            'title' => $inpostShipment->getService(),
            'number' => $inpostShipment->getTrackingNumber()
        ];

        $track = $this->trackFactory->create()->addData($trackData);
        $shipment->addTrack($track);
    }


    protected function getLockerSize($product)
    {
        if(!$lockerSize = $product->getInpostLockerSize())
        {
            $lockerSize = $this->configProvider->getDefaultInpostLockerSize();
        }
        return $lockerSize;
    }

}
