<?php

namespace InPost\Shipment\Plugin\Adminhtml\Order\Shipping;

use InPost\Shipment\Service\Builder\ShipmentRequestBuilder;
use InPost\Shipment\Service\Management\ShipmentManager;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\OrderRepository;

class CreateInpostShipping
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    private ShipmentManager $createShipmentService;

    public function __construct(
        OrderRepository $orderRepository,
        ShipmentManager $createShipmentService
    ) {
        $this->orderRepository = $orderRepository;
        $this->createShipmentService = $createShipmentService;
    }

    public function aroundExecute(
        \Magento\Shipping\Controller\Adminhtml\Order\Shipment\Save $saveShipmentAction,
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