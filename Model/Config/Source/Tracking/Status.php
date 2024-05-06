<?php

/**
 * InPost Tracking Statuses source model
 */
namespace InPost\Shipment\Model\Config\Source\Tracking;

use InPost\Shipment\Service\Api\TrackingService;
use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    const UNDEFINED_OPTION_LABEL = '-- Please Select --';

    /**
     * @var TrackingService
     */
    protected TrackingService $trackingService;

    /**
     * @var string[]
     */
    protected $statuses;


    /**
     * @param TrackingService $trackingService
     */
    public function __construct
    (
        TrackingService $trackingService
    )
    {
        $this->trackingService = $trackingService;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $statuses = $this->trackingService->getTrackingStatuses();

        $options = [['value' => '', 'label' => __('-- Please Select --')]];
        if(array_key_exists('items', $statuses))
        {
            foreach ($statuses['items'] as $status)
            {
                $options[] = ['value' => $status['name'], 'label' => $status['title']];
            }
        }
        return $options;
    }
}
