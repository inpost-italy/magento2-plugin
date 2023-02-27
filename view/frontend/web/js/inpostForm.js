define([
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'jquery',
        'knockout'
    ],
    function (
        Component,
        quote,
        rateReg,
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
                        apiEndpoint: config.pointsApiUrl,
                        mapType: config.mapType,
                        searchType: config.searchType,
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
                    $('#inpost-required-error-message').hide();

                    let modalWidget = easyPack.modalMap(function (point, modal) {
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

                $(document).on('submit','#co-shipping-method-form',function(event) {
                    if ((quote.shippingMethod()['method_code']) === 'inpost' && $('#inpost_selected_point_id').val() === '') {
                        $('#inpost-required-error-message').show();
                        event.preventDefault();
                        return false;
                    }
                });

                $(document).on('change', '#co-shipping-form input[name="telephone"]', function() {
                    let address = quote.shippingAddress();
                    address.telephone = $(this).val();
                    rateReg.set(address.getKey(), null);
                    rateReg.set(address.getCacheKey(), null);
                    quote.shippingAddress(address);
                });

                return this.widget;
            }
        });
    });
