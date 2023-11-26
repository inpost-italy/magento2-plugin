define([],function () {
    'use strict';

    var mixin = {
        defaults: {
            template: 'InPost_Shipment/shipping-information/address-renderer/default.html'
        },
        point: function() {
            var uiRegistry = require('uiRegistry')
            var inpostForm = uiRegistry.get('checkout.steps.shipping-step.shippingAddress.inpostForm')

            return inpostForm.selectedPoint() ? inpostForm.getSelectedPoint() : null;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
