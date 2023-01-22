define([
    'uiComponent',
    'jquery',
    'knockout'
    ],
function(
    Component,
    $,
    ko
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'InPost_Shipment/form'
        },
        config:     window.checkoutConfig,
        initialize: function() {
            this.initWidget(this.config);
            this.positionInpostButton;
            this._super();
        },
        initWidget: function (config) {
            window.easyPackAsyncInit = function () {
                easyPack.init({
                    defaultLocale: 'it',
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
            }

            window.openInPostModal = function () {
                $('#inpost_overlay').show();

                let postCode =  $('input[name="postcode"]').val();
                let modalWidget = easyPack.modalMap(function(point, modal) {
                    modal.closeModal();
                    $('#inpost_selected_point_id').val(point.name);
                    $('#inpost_overlay').hide();
                }, { width: 1200, height: 600 });

                if (postCode) {
                    modalWidget.searchPlace(postCode);
                }

                $('#widget-modal .widget-modal__close').click(function () {
                    $('#inpost_overlay').hide();
                });
            }

            $( document ).ready(function() {
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

            return this.widget;
        }
    });
});
