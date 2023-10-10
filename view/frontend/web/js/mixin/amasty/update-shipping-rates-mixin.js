define([
    'jquery',
    'Amasty_Checkout/js/model/shipping-registry'
], function ($, shippingRegistry) {
    'use strict';


    var inpostMixin = {
        selectedPointChanged: function (point) {
            // it needs to recollect shipping totals for inpost shipping
            // supper important to have
            shippingRegistry.shippingMethod = 'inpost_reload';
            $('#label_carrier_inpost_inpost').click();

            return point;
        }
    };

    return function (target) {
        return target.extend(inpostMixin);
    };
});
