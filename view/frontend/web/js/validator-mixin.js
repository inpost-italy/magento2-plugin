define([
    'jquery',
    'jquery/validate'
], function ($) {
    "use strict";

    return function (validator) {
        validator.addRule('validate-it-telephone', function (value, event) {
            let rule = /(^(\(?(((\+)|00)39)?\)?(3)(\d{8,9}))$)/;

            if((value.match(rule))){
                return true;
            }
        }, $.mage.__("Please enter valid Italian phone number."));

        return validator;
    };
});
