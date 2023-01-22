<?php

namespace InPost\Shipment\Api\Data;

use Magento\Framework\DataObject;

class PointsServiceResponse extends DataObject
{
    public function getItemsCount() : int
    {
        return $this->getCount();
    }

    public function getFirstItem() : PointData
    {
        $items = $this->getItems();

        return $items[0];
    }
}
