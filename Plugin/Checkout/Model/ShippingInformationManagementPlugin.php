<?php

namespace InPost\Shipment\Plugin\Checkout\Model;

use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Framework\Exception\InputException;

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

            if (!$inpostPointId && $addressInformation->getShippingCarrierCode() === \InPost\Shipment\Carrier\Inpost::CARRIER_CODE) {
                throw new InputException(
                    __('Cannot proceed checkout without selected Inpost Point.')
                );
            }

            $addressInformation->getShippingAddress()->setInpostPointId($inpostPointId);
        }

        return [$cartId, $addressInformation];
    }
}
