var isAmastyEnabled = window.checkoutConfig && window.checkoutConfig.hasOwnProperty('amastyOscGdprConsent');

var config = {
    map: {
        '*': {
            inpostForm: 'InPost_Shipment/js/inpostForm',
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender':'InPost_Shipment/js/model/shipping-save-processor/payload-extender'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/select-shipping-method': {
                'InPost_Shipment/js/action/select-shipping-method-mixin': true
            },
            'InPost_Shipment/js/inpostForm': {
                'InPost_Shipment/js/mixin/amasty/update-shipping-rates-mixin': isAmastyEnabled
            }
        }
    }
};

