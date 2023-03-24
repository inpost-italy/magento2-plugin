<?php

namespace InPost\Shipment\Plugin\Adminhtml\Order\Shipping;

use InPost\Shipment\Service\Builder\ShipmentRequestBuilder;
use InPost\Shipment\Service\Management\ShipmentManager;
use InPost\Shipment\Service\Order\ShippingStatusAction;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\OrderRepository;
use Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save;

class CreateInpostShipping
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /** @var ShipmentManager */
    private $createShipmentService;

    /** @var ShippingStatusAction */
    private $shippingStatusAction;

    /**
     * @param OrderRepository $orderRepository
     * @param ShipmentManager $createShipmentService
     * @param ShippingStatusAction $shippingStatusAction
     */
    public function __construct(
        OrderRepository $orderRepository,
        ShipmentManager $createShipmentService,
        ShippingStatusAction $shippingStatusAction
    ) {
        $this->orderRepository = $orderRepository;
        $this->createShipmentService = $createShipmentService;
        $this->shippingStatusAction = $shippingStatusAction;
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
            throw new \Exception("Unable to create a shiopment. Order ID is missing");
        }

        $packageOption = $request->getParam('inpost')['package_type'] ?? null;
        if (empty($packageOption)) {
            throw new \Exception("Unable to create a shipment. An package options is missing");
        }

        $result = $proceed();
        try {
            $this->createShipmentService->createShipment(
                $this->getCreatedShipment($orderId),
                $packageOption
            );
            $this->shippingStatusAction->processOrder($orderId);
        } catch (\Exception $e) {
            throw new \Exception("Unable to create InPost shipment: {$e->getMessage()}");
        }

        return $result;
    }

    /**
     * @param $orderId
     *
     * @return Shipment
     * @throws InputException
     * @throws NoSuchEntityException
     */
    private function getCreatedShipment($orderId): Shipment
    {
        $order = $this->orderRepository->get($orderId);

        return $order->getShipmentsCollection()->getLastItem();
    }
}
