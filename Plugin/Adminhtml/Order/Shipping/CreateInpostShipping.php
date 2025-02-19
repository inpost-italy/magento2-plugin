<?php

namespace InPost\Shipment\Plugin\Adminhtml\Order\Shipping;

use InPost\Shipment\Carrier\Inpost;
use InPost\Shipment\Service\Management\ShipmentManager;
use InPost\Shipment\Service\Order\ShippingStatusAction;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Reports\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment as OrderShipment;
use Magento\Sales\Model\OrderRepository;
use Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save;

class CreateInpostShipping
{

    /** @var ShipmentManager */
    private $createShipmentService;


    private CollectionFactory $orderCollectionFactory;
    private OrderRepository $orderRepository;
    private \Magento\Framework\Message\ManagerInterface $messageManager;
    private Shipment\TrackFactory $trackFactory;
    private Order\ShipmentRepository $shipmentRepository;
    private $shippingStatusAction;

    public function __construct(
        OrderRepository $orderRepository,
        CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        ShipmentManager $createShipmentService,
        Order\ShipmentRepository $shipmentRepository,
        ShippingStatusAction $shippingStatusAction,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->createShipmentService = $createShipmentService;
        $this->shippingStatusAction = $shippingStatusAction;
        $this->orderRepository = $orderRepository;
        $this->messageManager = $messageManager;
        $this->trackFactory = $trackFactory;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @param Save $saveShipmentAction
     * @param callable $proceed
     * @return mixed
     * @throws \Exception
     */
    public function aroundExecute(
        Save $saveShipmentAction,
        callable $proceed
    ) {

        $request = $saveShipmentAction->getRequest();
        $orderId = $request->getParam('order_id');
        if (empty($orderId)) {
            throw new \Exception("Unable to create a shipment. Order ID is missing");
        }

        if (! $this->isInpostOrder($orderId)) {
            return $proceed();
        }

        $packageOption = $request->getParam('inpost')['package_type'] ?? null;
        if (empty($packageOption)) {
            throw new \Exception("Unable to create a shipment. An package options is missing");
        }

        $order = $this->getOrder($orderId);
        // If point was provided from the form, use it, otherwise use the one from the order
        $pointId = $request->getParam('inpost')['point_id'] ?? $order->getShippingAddress()->getInpostPointId();;


        try {
            $inpostShipment = $this->createShipmentService->createShipment(
                $order,
                $pointId,
                $packageOption
            );

            // Create a Magento shipment here
            $result = $proceed();

            $shipment = $this->getCreatedShipment($orderId);
            $this->addTrack($shipment, $inpostShipment);
            $this->addTrackingNumberToOrderShipment($shipment, (string) $inpostShipment->getId());

        } catch (\InPost\Shipment\Service\Http\HttpClientException $e) {
            return $this->redirectWithError($saveShipmentAction, $e->getReason());
        } catch (\Exception $e) {
            return $this->redirectWithError($saveShipmentAction, $e->getMessage());
        }


        return $result;
    }

    private function redirectWithError(Save $saveAction, string $error) : \Magento\Framework\App\ResponseInterface
    {
        $response = $saveAction->getResponse();
        $request = $saveAction->getRequest();
        $response->setRedirect($request->getHeader('referer'));

        $this->messageManager->addErrorMessage("Unable to create InPost shipment: $error");

        return $response;
    }

    /**
     * @param Shipment $shipment
     * @param string $trackingNumber
     *
     * @return void
     * @throws CouldNotSaveException
     */
    private function addTrackingNumberToOrderShipment(Shipment $shipment, string $trackingNumber)
    {
        $shipment->setData('inpost_shipment_id', $trackingNumber);
        $this->shipmentRepository->save($shipment);
    }


    private function getOrder($orderId): Order
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFieldToFilter('entity_id', $orderId);

        return $collection->getFirstItem();
    }

    /**
     * @param OrderShipment $shipment
     * @param \InPost\Shipment\Api\Data\Shipment $inpostShipment
     * @return void
     */
    private function addTrack(\Magento\Sales\Model\Order\Shipment $shipment, \InPost\Shipment\Api\Data\Shipment $inpostShipment)
    {
        $trackData = [
            'carrier_code' => Inpost::CARRIER_CODE,
            'title' => $inpostShipment->getService(),
            'number' => $inpostShipment->getTrackingNumber()
        ];

        $track = $this->trackFactory->create()->addData($trackData);
        $shipment->addTrack($track);
    }

    private function getCreatedShipment($orderId): Shipment
    {
        $order = $this->orderRepository->get($orderId);

        return $order->getShipmentsCollection()->getLastItem();
    }

    /**
     * Check if current order has inpost shipment
     *
     * @param $orderId
     * @return bool
     * @throws InputException
     * @throws NoSuchEntityException
     */
    private function isInpostOrder($orderId) : bool
    {
        $order = $this->orderRepository->get($orderId);

        return strpos($order->getShippingMethod(), Inpost::CARRIER_CODE) !== false;
    }

}
