<?php

namespace InPost\Shipment\Plugin\Order\Model\Handler;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Handler\State;

class StatePlugin
{
    public function aroundCheck(State $subject, callable $proceed ,Order $order)
    {
        if(!$order->getForceInpostStatus())
        {
            return $proceed($order);
        }
    }
}
