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
                    $('#easypack-map-modal-toggler').show();
                    window.is_inpost_selected = true;
                } else {
                    $('#inpost-extra-info').hide();
                    $('#inpost_selected_point_id').val('');
                    $('#inpost-point-details').hide();
                    $('#remove-selected-point').hide();
                    window.is_inpost_selected = false;
                }
            }

            _super(shippingMethod);
        });
    };
});
