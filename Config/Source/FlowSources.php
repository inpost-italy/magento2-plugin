<?php

namespace InPost\Shipment\Config\Source;

class FlowSources implements \Magento\Framework\Option\ArrayInterface
{
    const FLOW_SERVICE_TYPE_LOCKER  = 'L2L';

    const FLOW_SERVICE_TYPE_HUB     = 'H2L';

    /**
     * @var array[]
     */
    private $values = [
        [
            'value' => self::FLOW_SERVICE_TYPE_LOCKER,
            'label' => 'Locker2Locker'
        ],
        [
            'value' => self::FLOW_SERVICE_TYPE_HUB,
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
            FlowSources::FLOW_SERVICE_TYPE_LOCKER => 'Locker2Locker',
            FlowSources::FLOW_SERVICE_TYPE_HUB => 'Locker2Hub',
        ];
    }
}
