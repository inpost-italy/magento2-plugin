<?php

namespace InPost\Shipment\Plugin\Adminhtml\Order\Shipping;

use InPost\Shipment\Service\Builder\ShipmentRequestBuilder;
use Magento\Sales\Model\OrderRepository;

class CreateInpostShipping
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    private \InPost\Shipment\Service\Management\ShipmentManager $createShipmentService;

    public function __construct(
        OrderRepository $orderRepository,
        \InPost\Shipment\Service\Management\ShipmentManager $createShipmentService
    ) {
        $this->orderRepository = $orderRepository;
        $this->createShipmentService = $createShipmentService;
    }

    public function aroundExecute(
        \Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save $saveShipment,
        callable $proceed
    ) {
        $request = $saveShipment->getRequest();
        $packageOption = $request->getParam('inpost')['package_type'] ?? null;

        $result = $proceed();
        if (! $packageOption) {
            return $result;
        }

        $order = $this->orderRepository->get($request->getParam('order_id'));
        $shipment = $order->getShipmentsCollection()->getLastItem();
        $this->createShipmentService->createShipment($shipment, $packageOption);

        return $result;
    }
}