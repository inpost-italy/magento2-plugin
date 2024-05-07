<?php

/**
 * InPost Locker Size source model
 */
namespace InPost\Shipment\Model\Config\Source\Product;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class LockerSize extends AbstractSource implements OptionSourceInterface, SourceInterface
{
    const UNDEFINED_OPTION_LABEL = '-- Please Select --';
    const LOCKER_SIZE_S = 'S';
    const LOCKER_SIZE_M = 'M';
    const LOCKER_SIZE_L = 'L';
    /**
     * @return array
     */
    public function getAllOptions(): array
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => '', 'label' => __(self::UNDEFINED_OPTION_LABEL)],
                ['value' => self::LOCKER_SIZE_S, 'label' => __('Small')],
                ['value' => self::LOCKER_SIZE_M, 'label' => __('Medium')],
                ['value' => self::LOCKER_SIZE_L, 'label' => __('Large')],
            ];
        }
        return $this->_options;
    }
}
