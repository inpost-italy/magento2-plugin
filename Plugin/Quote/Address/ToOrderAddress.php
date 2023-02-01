<?php

namespace InPost\Shipment\Plugin\Quote\Address;

class ToOrderAddress
{
    /**
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $subject
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $orderAddress
     * @param \Magento\Quote\Model\Quote\Address $object
     * @return \Magento\Sales\Api\Data\OrderAddressInterface
     */
    public function afterConvert(
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $subject,
        \Magento\Sales\Api\Data\OrderAddressInterface $orderAddress,
        \Magento\Quote\Model\Quote\Address $object
    ) {
        $orderAddress->setInpostPointId($object->getInpostPointId());

        return $orderAddress;
    }
}