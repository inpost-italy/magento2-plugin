define(
    [
        'jquery',
        'underscore',
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'mage/translate'

    ],function (
        $,
        _,
        uiRegistry,
        quote,
        $t
    ) {
        'use strict';

        return function (target) {
            return target.extend({
                q: quote,
                validateShippingInformation: function () {
                    var shippingMethod = this.q.shippingMethod();
                    if (shippingMethod.carrier_code !== 'inpost') {
                        return this._super()
                    }

                    var inpostForm = uiRegistry.get('checkout.steps.shipping-step.shippingAddress.inpostForm');
                    if (! inpostForm.selectedPoint()) {
                        this.errorValidationMessage(
                            $t('Manca il punto di InPost')
                        );
                        return false;
                    }

                    return this._super()
                }
            });
        };
    });
