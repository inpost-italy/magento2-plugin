<?php
declare(strict_types=1);

namespace InPost\Shipment\Api\Data;

use Magento\Framework\DataObject;

class ShipmentResponse extends DataObject
{
    public function __construct(array $data = [])
    {
        foreach ($data['items'] ?? [] as $i => $item) {
            $data['items'][$i] = new Shipment($item);
        }
        parent::__construct($data);
    }

    public function getShipment(): Shipment
    {
        return $this->getData('shipment');
    }

    public function getFirstItem(): Shipment
    {
        return $this->getItems()[0];
    }

    public function isEmpty(): bool
    {
        return empty($this->getItems());
    }
}
