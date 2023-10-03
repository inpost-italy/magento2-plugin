define(
    [
        'jquery',
        'ko',
        'uiRegistry',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, ko, registry, b) {
        'use strict';

        return {
            validate: function () {
                if (! window.is_inpost_selected) {
                    return true;
                }

                if (!$('#inpost_selected_point_id').val()) {
                    alert('Please select inpost point')
                    return false;
                }

                return true;
            }
        };
    }
);
