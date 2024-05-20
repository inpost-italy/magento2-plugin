<?php

/**
 * InPost Tracking Statuses source model
 */
namespace InPost\Shipment\Model\Config\Source\Labels;

use InPost\Shipment\Service\Api\TrackingService;
use Magento\Framework\Data\OptionSourceInterface;

class FormatType implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => 'pdf_A6', 'label' => __('PDF A6')],
            ['value' => 'pdf_normal', 'label' => __('PDF A4')],
            ['value' => 'zpl_A6', 'label' => __('ZPL 203dpi')],
            ['value' => 'zpl_dpi300', 'label' => __('ZPL 300dpi')],
        ];

        return $options;
    }
}
