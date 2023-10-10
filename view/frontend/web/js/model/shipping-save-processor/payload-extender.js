define([
    'jquery'
], function ($) {
    'use strict';

    return function (payload) {
        payload.addressInformation['extension_attributes'] = {};

        var inpostComponent = require('uiRegistry').get('checkout.steps.shipping-step.shippingAddress.inpostForm');
        if (inpostComponent && inpostComponent.selectedPoint() !== '') {
            payload.addressInformation['extension_attributes'].inpost_point_id = inpostComponent.selectedPoint();
        }

        return payload;
    };
});
