define([
    'uiComponent',
    'jquery',
    'knockout',
    'Magento_Customer/js/customer-data',
    'domReady'
    ],
function(
    Component,
    $,
    ko,
    storage,
    domReady
) {
    'use strict';

    domReady(function () {
        let modalToggler = $('#easypack-map-modal-toggler'),
            inpostLabelCell = $('#label_carrier_inpost_inpost');

        console.log(modalToggler);

        modalToggler.css({
            'position': 'absolute',
            'top': 0,
            'right': 0
        });
        inpostLabelCell.css({'position': 'absolute'});
        modalToggler.appendTo(inpostLabelCell);
    });

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

            window.localStorage.removeItem('inpost_point_id');
            window.easyPackAsyncInit = function () {
                easyPack.init({
                    defaultLocale: 'pl',
                    mapType: 'osm',
                    searchType: 'osm',
                    points: {
                        types: ['pop', 'parcel_locker'],
                        allowedToolTips: ['pok', 'pop'],
                        functions: []
                    },
                    map: {
                        initialTypes: ['parcel_locker']
                    }
                });
            };

            window.openInPostModal = function () {
                easyPack.modalMap(function(point, modal) {
                    modal.closeModal();
                    console.log(point);
                    $('#inpost_selected_point_id').val(point.name);
                    //window.localStorage.setItem('inpost_point_id', point.name);
                }, { width: 1200, height: 600 });
            }

            return this.widget;
        }
    });
});
