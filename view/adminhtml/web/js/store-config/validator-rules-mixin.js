define([
    'jquery'
], function ($) {
    'use strict';
    return function (target) {
        $.validator.addMethod(
            'validate-italian-phone-number',
            function (value) {
                return /(^(\(?(((\+)|00)39)?\)?(3)(\d{8,9}))$)/.test(value);
            },
            $.mage.__('Please enter valid Italian phone number.')
        );
        return target;
    };
});
