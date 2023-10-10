define([
    'mage/utils/wrapper',
    'jquery',
    'uiRegistry'
], function (wrapper, $, uiRegistry) {
    'use strict';

    return function (selectShippingMethod) {

        return wrapper.wrap(selectShippingMethod, function (_super, shippingMethod) {
            var inpostForm = uiRegistry.get('checkout.steps.shipping-step.shippingAddress.inpostForm');
            if (shippingMethod && inpostForm) {
                inpostForm.paymentMethodSelected(shippingMethod.method_code === 'inpost');
            }

            _super(shippingMethod);
        });
    };
});
