<?php

namespace InPost\Shipment\Api\Data;

use InPost\Shipment\Carrier\Inpost;

class TrackingData extends \Magento\Framework\DataObject
{
    public function getTracking()
    {
        return $this->getData('tracking_number');
    }

    public function getCarrierTitle()
    {
        return Inpost::CARRIER_TITLE;
    }

    public function getTrackSummary()
    {
        return $this->arrayToHtmlTableRecursive($this->getData('custom_attributes')['target_machine_detail'] ?? []);
    }

    public function getUrl()
    {
        return "https://inpost.it/trova-il-tuo-pacco?number={$this->getTracking()}";
    }

    private function arrayToHtmlTableRecursive($arr)
    {
        $str = "";
        foreach ($arr as $key => $val) {
            $str .= "$key: ";
            if (is_array($val)) {
                if (!empty($val)) {
                    $str .= $this->arrayToHtmlTableRecursive($val);
                }
            } else {
                $str .= " $val, ";
            }
        }

        return $str;
    }
}
