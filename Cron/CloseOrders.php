<?php

namespace InPost\Shipment\Cron;

use InPost\Shipment\Service\Management\OrderManager;

class CloseOrders
{
    /**
     * @var OrderManager
     */
    private OrderManager $orderManager;

    /**
     * @param OrderManager $orderManager
     */
    public function __construct(
        OrderManager $orderManager
    )
    {
        $this->orderManager = $orderManager;
    }

    /**
     * @return CloseOrders
     */
    public function execute(): CloseOrders
    {
        $this->orderManager->closeOrders();
        return $this;
    }
}
