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
    const LOCKER_SIZE_S_VALUE = 1;
    const LOCKER_SIZE_M_VALUE = 2;
    const LOCKER_SIZE_L_VALUE = 3;
    const LOCKER_SIZE_S_LABEL = 'small';
    const LOCKER_SIZE_M_LABEL = 'medium';
    const LOCKER_SIZE_L_LABEL = 'large';
    /**
     * @return array
     */
    public function getAllOptions(): array
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => '', 'label' => __(self::UNDEFINED_OPTION_LABEL)],
                ['value' => self::LOCKER_SIZE_S_VALUE, 'label' => __(ucfirst(self::LOCKER_SIZE_S_LABEL))],
                ['value' => self::LOCKER_SIZE_M_VALUE, 'label' => __(ucfirst(self::LOCKER_SIZE_M_LABEL))],
                ['value' => self::LOCKER_SIZE_L_VALUE, 'label' => __(ucfirst(self::LOCKER_SIZE_L_LABEL))],
            ];
        }
        return $this->_options;
    }
}
