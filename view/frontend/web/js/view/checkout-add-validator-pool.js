define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/additional-validators',
        'InPost_Shipment/js/model/checkout-validator'
    ],
    function (Component, additionalValidators, inpostValidator) {
        'use strict';

        additionalValidators.registerValidator(inpostValidator);
        return Component.extend({});
    }
);
