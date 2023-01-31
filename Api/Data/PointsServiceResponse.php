<?php
declare(strict_types=1);

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

        return $items[0] ?? new PointData([]);
    }

    public function isEmpty() : bool
    {
        return empty($this->getItems());
    }
}
