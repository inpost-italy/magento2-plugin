var config = {
    map: {
        '*': {
            inpostForm: 'InPost_Shipment/js/inpostForm',
            'Magento_Checkout/template/shipping-address/shipping-method-item.html': 'InPost_Shipment/template/shipping-address/shipping-method-item.html',
            'Magento_Checkout/js/model/shipping-save-processor/default':'InPost_Shipment/js/model/shipping-save-processor/default'
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
