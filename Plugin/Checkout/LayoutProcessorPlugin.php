<?php
declare(strict_types=1);

namespace InPost\Shipment\Plugin\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessor;

class LayoutProcessorPlugin
{
    /**
     * @param LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(LayoutProcessor $subject, array $jsLayout): array
    {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['validation']['validate-it-telephone'] = true;

        foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                 ['payment']['children']['payments-list']['children'] as $key => $payment) {
            /* Telephone Billing Address */
            if (isset($payment['children']['form-fields']['children']['telephone'])) {
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children']
                ['telephone']['validation'] = ['required-entry' => true, 'validate-it-telephone' => true];
            }
        }

        return $jsLayout;
    }

}
