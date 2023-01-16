<?php

namespace InPost\Shipment\Plugin\Checkout\Model;

use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Checkout\Api\Data\ShippingInformationInterface;

class ShippingInformationManagementPlugin
{
    /**
     * Set Inpost Attribute Values
     *
     * @param  ShippingInformationManagement $subject
     * @param                                $cartId
     * @param  ShippingInformationInterface  $addressInformation
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
                                      $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $shippingAddress = $addressInformation->getShippingAddress();
        if ($shippingAddressExtensionAttributes = $shippingAddress->getExtensionAttributes()) {
            $pointId = $shippingAddressExtensionAttributes->getInpostPointId();
            $shippingAddress->setInpostPointId($pointId);
        }

        return [$cartId, $addressInformation];
    }
}
