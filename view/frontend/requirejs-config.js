var isAmastyEnabled = window.checkoutConfig && window.checkoutConfig.hasOwnProperty('amastyOscGdprConsent');

var config = {
    map: {
        '*': {
            inpostForm: isAmastyEnabled ? 'InPost_Shipment/js/inpostForm' : 'InPost_Shipment/js/inpostForm-amasty',
            'Magento_Checkout/template/shipping-address/shipping-method-item.html': 'InPost_Shipment/template/shipping-address/shipping-method-item.html',
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender':'InPost_Shipment/js/model/shipping-save-processor/payload-extender',
            'Amasty_Checkout/template/onepage/shipping/methods.html': 'InPost_Shipment/template/amasty/onepage/shipping/methods.html'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/select-shipping-method': {
                'InPost_Shipment/js/action/select-shipping-method-mixin': true
            }
        }
    }
};

