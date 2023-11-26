define([
        'uiComponent',
        'jquery',
        'knockout',
        'Magento_Checkout/js/model/quote'

    ],
    function (
        Component,
        $,
        ko,
        quote
    ) {
        'use strict';
        window.isInpostInstantiated = false;

        return Component.extend({
            defaults: {
                template: 'InPost_Shipment/inpost-form.html'
            },
            config: window.checkoutConfig.inpost,
            shippingMethodSelected: ko.observable(false),
            selectedPoint: ko.observable(''),
            line1: ko.observable(''),
            line2: ko.observable(''),
            openingHours: ko.observable(''),
            postCode: ko.observable(''),
            isInstantiated: false,
            initialize: function () {
                if (!window.isInpostInstantiated) {
                    this.initWidget(this.config);
                }

                window.isInpostInstantiated = true;
                this._super();
            },
            selectedPointChanged: function(value) {
                // Notify subscribers
                return value;
            },
            getSelectedPoint: function() {
                if (!this.shippingMethodSelected()) {
                    return null;
                }

                return {
                    pointid: this.selectedPoint(),
                    address1: this.line1(),
                    address2: this.line2(),
                    openingHours: this.openingHours
                }
            },
            initWidget: function (config) {
                self = this;
                this.selectedPoint.subscribe(this.selectedPointChanged);

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
                        display: {}
                    });
                }

                return this.widget;
            },
            openInPostModal: function() {
                $('#inpost_overlay').show();

                var modalWidget = easyPack.modalMap(function (point, modal) {
                    modal.closeModal();

                    self.line1(point.address.line1);
                    self.line2(point.address.line2);
                    self.openingHours(point.opening_hours);
                    self.postCode(point.address_details.post_code)
                    self.selectedPoint(point.name);

                   $('#easypack-map-modal-toggler').hide()
                   $('#inpost_overlay').hide()
                }, {});

                // Set postal code from address
                if (quote.shippingAddress().postcode) {
                    modalWidget.searchPlace(quote.shippingAddress().postcode);
                }

                $('#easypack-search').on('input', function(input) {
                    // Check if the input is empty
                    if ($(input.target).val() === '') {
                        // Trigger the alert
                        window.mapController.setCenterFromArray([41.898386, 12.516985]);
                    }
                });

                $('#widget-modal .widget-modal__close').click(function () {
                    $('#inpost_overlay').hide();
                });

                $("#widget-modal").css({"max-height":"90%"});
            }
        });
    });
