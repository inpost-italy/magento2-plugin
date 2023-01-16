<?php

namespace InPost\Shipment\Plugin\Checkout\Model;

use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Checkout\Api\Data\ShippingInformationInterface;

class ShippingInformationManagementPlugin
{
    /**
     * Set Inpost Attribute Values
     *
     * @param ShippingInformationManagement $subject
     * @param                                $cartId
     * @param ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        if ($extAttributes = $addressInformation->getExtensionAttributes()) {
            $inpostPointId = $extAttributes->getInpostPointId();
            $addressInformation->getShippingAddress()->setInpostPointId($inpostPointId);
        }

        return [$cartId, $addressInformation];
    }
}
