define(
    [
        'jquery',
        'ko',
        'inpostForm',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, ko, inpostForm, checkoutData) {
        'use strict';

        return {
            checkoutData: checkoutData,
            inpost: inpostForm,
            validate: function () {
                var method = this.checkoutData.shippingMethod();
                if (!method || method.carrier_code !== 'inpost') {
                    return true;
                }

                if (this.inpost().selectedPoint() == '') {
                    alert('Seleziona il punto di Inpost');
                    return false;
                }

                return true
            }
        };
    }
);
