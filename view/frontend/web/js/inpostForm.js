define([
        'uiComponent',
        'jquery',
        'knockout'
    ],
    function (
        Component,
        $,
        ko
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'InPost_Shipment/form'
            },
            config: window.checkoutConfig.inpost,
            initialize: function () {
                this.initWidget(this.config);
                this._super();
            },
            initWidget: function (config) {
                window.easyPackAsyncInit = function () {
                    easyPack.init({
                        defaultLocale: 'it',
                        apiEndpoint: 'https://api-it-points.easypack24.net/v1/',
                        mapType: config.map_type,
                        searchType: config.search_type,
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

                    let modalWidget = easyPack.modalMap(function (point, modal) {
                        console.log(point);
                        modal.closeModal();
                        $('#inpost-point-details').show();

                        $('#inpost-point-details').find('.point-name').html(point.name);
                        $('#inpost-point-details').find('.point-address').html(point.address.line1);
                        $('#inpost-point-details').find('.point-postcode').html(point.address.line2);
                        $('#inpost-point-details').find('.point-opening-hours').html(point.opening_hours);

                        $('#remove-selected-point').show();
                        $('#inpost_selected_point_id').val(point.name);
                        $('#inpost_overlay').hide();
                    }, {width: 1200, height: 600});

                    if ($('input[name="postcode"]').val()) {
                        modalWidget.searchPlace($('input[name="postcode"]').val());
                    }

                    $('#widget-modal .widget-modal__close').click(function () {
                        $('#inpost_overlay').hide();
                    });
                }

                window.removeSelectedPoint = function () {
                    $('#inpost_selected_point_id').val('');
                    $('#inpost-point-details').find('.point-name').html('');
                    $('#inpost-point-details').find('.point-address').html('');
                    $('#inpost-point-details').find('.point-postcode').html('');
                    $('#inpost-point-details').find('.point-opening_hours').html('');
                    $('#inpost-point-details').hide();
                    $('#remove-selected-point').hide();
                }

                return this.widget;
            }
        });
    });
