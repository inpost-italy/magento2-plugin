<?php
namespace InPost\Shipment\Service\Management;

use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Config\ConfigProvider;
use InPost\Shipment\Logger\Logger;
use InPost\Shipment\Service\Api\TrackingService;
use Magento\Framework\Exception\AlreadyExistsException as AlreadyExistsExceptionAlias;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Track;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class OrderManager
{
    /**
     * @var CollectionFactory
     */
    protected CollectionFactory $orderCollectionFactory;

    /**
     * @var TrackingService
     */
    protected TrackingService $trackingService;

    /**
     * @var ConfigProvider
     */
    protected ConfigProvider $configProvider;

    /**
     * @var OrderRepository
     */
    protected OrderRepository $orderRepository;

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * @param CollectionFactory $orderCollectionFactory
     * @param TrackingService $trackingService
     * @param ConfigProvider $configProvider
     * @param OrderRepository $orderRepository
     * @param Logger $logger
     */
    public function __construct
    (
        CollectionFactory $orderCollectionFactory,
        TrackingService $trackingService,
        ConfigProvider $configProvider,
        OrderRepository $orderRepository,
        Logger $logger
    )
    {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->trackingService = $trackingService;
        $this->configProvider = $configProvider;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    public function closeOrders(): void
    {
        $this->logger->info('START Automation Close Order');
        if($this->configProvider->canCloseOrder())
        {
            if(count($this->getCollection()) > 0)
            {
                $trackingStatusToCloseOrder = $this->configProvider->getTrackingStatusToCloseOrder();
                foreach($this->getCollection() as $order)
                {
                    /**
                     * @var Order $order
                     */
                    if(count($order->getShipmentsCollection()) > 0)
                    {
                        $this->logger->info('Order: ' . $order->getIncrementId());
                        foreach($order->getShipmentsCollection() as $shipment)
                        {
                            /**
                             * @var Shipment $shipment
                             */
                            if(count($shipment->getTracks() ) > 0)
                            {
                                foreach ($shipment->getTracks() as $track)
                                {
                                    /**
                                     * @var Track $track
                                     */
                                    $trackingNumber = $track->getTrackNumber();
                                    $this->logger->info('    Tracking Number: ' . $trackingNumber);
                                    try {
                                        $trackingStatus = $this->trackingService->getTracking($trackingNumber);
                                        if($trackingStatus['status'])
                                        {
                                            $this->logger->info('    Tracking Status: ' . $trackingStatus['status']['title']);
                                            if($trackingStatus['status']['name'] == $trackingStatusToCloseOrder)
                                            {
                                                $this->setOrderToClose($order);
                                            }
                                            else
                                            {
                                                $this->logger->info('    Nothing To Do');
                                            }
                                        }
                                    }
                                    catch (\Exception $e)
                                    {
                                        $this->logger->error('    ' . trim($e->getMessage()));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->logger->info('END Automation Close Order');
    }

    public function getCollection(): Collection
    {
        $collection = $this->orderCollectionFactory->create();
        $status = $this->configProvider->getStatusToCloseOrder();
        if($status)
        {
            $collection->addFieldToFilter('status',
                ['nin' => [Order::STATE_CANCELED, Order::STATE_HOLDED, $status]]
            )
            ->addFieldToFilter('shipping_method', ['like' => '%' . Inpost::CARRIER_CODE . '%']);
        }
        else
        {
            $collection->addFieldToFilter('status', 'null');
        }

        return $collection;
    }

    /**
     * @param Order $order
     * @return Order
     * @throws AlreadyExistsExceptionAlias
     * @throws InputException
     * @throws NoSuchEntityException
     */
    protected function setOrderToClose(Order $order): Order
    {
        if($status = $this->configProvider->getStatusToCloseOrder())
        {
            $order->setStatus($status);
            $order->setState($status);
            $order->setForceInpostStatus(true);
            $this->orderRepository->save($order);
            $this->logger->info('    Set Order Status: ' . $status);
        }
        return $order;
    }
}
