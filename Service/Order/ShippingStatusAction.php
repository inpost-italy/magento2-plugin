<?php

namespace InPost\Shipment\Service\Order;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InvalidTransitionException;
use Magento\Sales\Api\OrderRepositoryInterface;

class ShippingStatusAction
{

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws InvalidTransitionException
     */
    public function processOrder($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        $order->setState('shipping');
        $order->setStatus('Shipping');
        $this->orderRepository->save($order);
    }
}
