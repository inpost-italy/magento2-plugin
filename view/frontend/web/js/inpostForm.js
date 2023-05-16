define([
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/checkout-data',
        'jquery',
        'knockout'
    ],
    function (
        Component,
        quote,
        rateReg,
        checkoutData,
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
                            initialTypes: ['pop', 'parcel_locker'],
                            useGeolocation: false
                        },
                        display: {
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
                    }, {});


                    // Add event listener to the input
                    document.getElementById('easypack-search').addEventListener('input', (input) => {
                        // Check if the input is empty
                        if ($(input.target).val() === '') {
                            // Trigger the alert
                            window.mapController.setCenterFromArray([41.898386, 12.516985]);
                        }
                    });


                    if ($('input[name="postcode"]').val()) {
                        modalWidget.searchPlace($('input[name="postcode"]').val());
                    }

                    $('#widget-modal .widget-modal__close').click(function () {
                        $('#inpost_overlay').hide();
                    });

                    $("#widget-modal").css({"max-height":"90%"});
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

                $('document').ready(function () {
                    setTimeout(function() {
                        if (checkoutData.getSelectedShippingRate() === 'inpost_inpost') {
                            $('#inpost-extra-info').show();
                        }
                    }, 3000);
                });

                return this.widget;
            }
        });
    });
