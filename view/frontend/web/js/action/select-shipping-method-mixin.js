define([
    'mage/utils/wrapper',
    'Magento_Customer/js/customer-data',
    'jquery'
], function (wrapper, customerStorage, $) {
    'use strict';

    return function (selectShippingMethod) {
        return wrapper.wrap(selectShippingMethod, function (_super, shippingMethod) {
            if (shippingMethod) {
                if (shippingMethod.method_code === 'inpost') {
                    $('#easypack-map-modal-toggler').show();
                } else {
                    $('#easypack-map-modal-toggler').hide();
                }
            }

            _super(shippingMethod);
            if (shippingMethod) {
                // your logic after shippingMethod set to quote
            }
        });
    };
});
