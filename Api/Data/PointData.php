<?php
declare(strict_types=1);

namespace InPost\Shipment\Api\Data;

use Magento\Framework\DataObject;

class PointData extends DataObject
{
    public function getAddressInfo()
    {
        $addressDetails = $this->getAddressDetails();

        return "{$this->getName()}, {$addressDetails['street']}, {$addressDetails['building_number']} ({$this->getLocationDescription()},{$this->getLocationDescription1()})";
    }
}
