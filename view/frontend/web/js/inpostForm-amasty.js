// app/code/Amasty/Checkout/view/frontend/web/js/model/shipping-registry.js:114
define([
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/checkout-data',
        'jquery',
        'knockout',
        'Amasty_Checkout/js/model/shipping-registry' // Custom Amasty dependency
    ],
    function (
        Component,
        quote,
        rateReg,
        checkoutData,
        $,
        ko,
        shippingRegistry
    ) {
        'use strict';

        return Component.extend({
            shippingRegistry: shippingRegistry,
            isTriggerPosted: false,
            defaults: {
                template: 'InPost_Shipment/form'
            },
            config: window.checkoutConfig.inpost,
            initialize: function () {
                this.initWidget(this.config);
                this._super();


            },
            initWidget: function (config) {
                var self = this;

                window.easyPackAsyncInit = function () {
                    easyPack.init({
                        defaultLocale: 'it',
                        apiEndpoint: config.pointsApiUrl,
                        mapType: config.mapType,
                        searchType: "google",
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



                window.openInPostModal = function (param) {
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
                        $('#inpost_selected_point_id').trigger('change');

                        $('#inpost_overlay').hide();
                        $('#easypack-map-modal-toggler').hide();
                    }, {});


                    // Add event listener to the input
                    document.getElementById('easypack-search').addEventListener('input', (input) => {
                        // Check if the input is empty
                        if ($(input.target).val() === '') {
                            // Trigger the alert
                            window.mapController.setCenterFromArray([41.898386, 12.516985]);
                        }
                    });

                    if (quote.shippingAddress().postcode) {
                        modalWidget.searchPlace(quote.shippingAddress().postcode);
                    }

                    $('#widget-modal .widget-modal__close').click(function () {
                        $('#inpost_overlay').hide();
                    });

                    $("#widget-modal").css({"max-height":"90%"});
                }

                window.removeSelectedPoint = function () {
                    $('#inpost_selected_point_id').val('');
                    $('#inpost_selected_point_id').trigger('change');
                    $('#inpost-point-details').find('.point-name').html('');
                    $('#inpost-point-details').find('.point-address').html('');
                    $('#inpost-point-details').find('.point-postcode').html('');
                    $('#inpost-point-details').find('.point-opening_hours').html('');
                    $('#inpost-point-details').hide();
                    $('#remove-selected-point').hide();
                    $('#easypack-map-modal-toggler').show();
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


                setTimeout(function() {
                    if (checkoutData.getSelectedShippingRate() === 'inpost_inpost') {
                        $('#inpost-extra-info').show();
                    }

                }, 3000);

                setTimeout(function() {
                    debugger;
                    if (self.isTriggerPosted == false) {
                        $('#inpost_selected_point_id').change($.proxy(function() {
                            console.log('InPost point id change')
                            // Mark registry change to make re-submission of same shipping method possible
                            this.shippingRegistry.shippingMethod = 'inpost_reload';
                            $('#checkout-shipping-method-load > table > tbody > tr.row.amcheckout-method.-selected').click()
                        }, self));
                        self.isTriggerPosted = true;
                    }
                }, 5000);

                setTimeout(function() {
                    debugger;
                    if (self.isTriggerPosted == false) {
                        $('#inpost_selected_point_id').change($.proxy(function() {
                            console.log('InPost point id change')
                            // Mark registry change to make re-submission of same shipping method possible
                            this.shippingRegistry.shippingMethod = 'inpost_reload';
                            $('#checkout-shipping-method-load > table > tbody > tr.row.amcheckout-method.-selected').click()
                        }, self));
                        self.isTriggerPosted = true;
                    }
                }, 10000);



                return this.widget;
            }
        });
    });
