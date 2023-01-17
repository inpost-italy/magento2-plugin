define([
    'mage/utils/wrapper',
    'jquery'
], function (wrapper, $) {
    'use strict';

    return function (selectShippingMethod) {
        return wrapper.wrap(selectShippingMethod, function (_super, shippingMethod) {
            if (shippingMethod) {
                if (shippingMethod.method_code === 'inpost') {
                    $('#easypack-map-modal-toggler').show();
                } else {
                    $('#easypack-map-modal-toggler').hide();
                    $('#inpost_selected_point_id').val('');
                }
            }

            _super(shippingMethod);
        });
    };
});
