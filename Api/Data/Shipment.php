<?php

namespace InPost\Shipment\Api\Data;

class Shipment extends \Magento\Framework\DataObject
{
    public function getTrackingNumber() : ?string
    {
        return $this->getData('tracking_number');
    }
}