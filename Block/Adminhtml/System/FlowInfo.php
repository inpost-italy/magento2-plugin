<?php

namespace InPost\Shipment\Block\Adminhtml\System;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\RuntimeException;

class FlowInfo extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     * @throws FileSystemException
     * @throws RuntimeException
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return '<block style="display: grid;justify-content: center;">
                    <span>' . __('Pick up address for the courier') . '</span>
                </block>';
    }
}
