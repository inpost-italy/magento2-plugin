<?php

namespace InPost\Shipment\Config\Source;

class MapSources implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'google', 'label' => __('Google Maps')],
            ['value' => 'osm', 'label' => __('OSM')]
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['google' => __('Google Maps'), 'osm' => __('OSM')];
    }
}
