define([
    'uiComponent',
    'jquery',
    'knockout',
    ],
function(
    Component,
    $,
    ko,
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'InPost_Shipment/form'
        },
        config:     window.checkoutConfig,
        initialize: function() {
            this.initWidget(this.config);
            this._super();
        },
        initWidget: function (config) {
            console.log('init.widget')

            window.easyPackAsyncInit = function () {
                easyPack.init({});  // Configuration object
                var map = easyPack.mapWidget('easypack-map', function(point){
                    console.log(point);
                });
            };

            return this.widget;
        }
    });
});
