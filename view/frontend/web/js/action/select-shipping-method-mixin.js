define([
    'mage/utils/wrapper',
    'jquery'
], function (wrapper, $) {
    'use strict';

    return function (selectShippingMethod) {
        return wrapper.wrap(selectShippingMethod, function (_super, shippingMethod) {
            if (shippingMethod) {

                if (shippingMethod.method_code === 'inpost') {
                    $('#inpost-extra-info').show();
                } else {
                    $('#inpost-extra-info').hide();
                    $('#inpost_selected_point_id').val('');
                    $('#inpost-point-details').hide();
                    $('#remove-selected-point').hide();
                }
            }

            _super(shippingMethod);
        });
    };
});
