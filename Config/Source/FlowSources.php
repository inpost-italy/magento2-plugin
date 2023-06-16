<?php

namespace InPost\Shipment\Config\Source;

class FlowSources implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array[]
     */
    private $values = [
        [
            'value' => 'inpost_locker_standard',
            'label' => 'Locker2Locker'
        ],
        [
            'value' => 'inpost_courier_c2c',
            'label' => 'Locker2Hub'
        ]
    ];

    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return $this->values;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'inpost_locker_standard' => 'Locker2Locker',
            'inpost_courier_c2c' => 'Locker2Hub',
        ];
    }
}
