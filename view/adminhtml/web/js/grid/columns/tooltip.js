/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/grid/columns/column',
    'underscore'
], function (Column, _) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'InPost_Shipment/ui/grid/cells/tooltip',
            tooltipTmpl: 'InPost_Shipment/ui/grid/cells/tooltip/content',
            visibeItemsLimit: 1,
            tooltipTitle: ''
        },

        /**
         * Extracts array of labels associated with provided values and sort it alphabetically.
         *
         * @param {Object} record - Record object.
         * @returns {Array}
         */
        getValuesArray: function (record) {
            var values = [];
            if(this.getLabel(record)) {
                values = JSON.parse(this.getLabel(record));
            }
            return values;
        },

        /**
         * Gets value of key from values array.
         *
         * @param {Object} record - Record object.
         * @param {String} key - Record object.
         * @returns {String}
         */
        getTooltipValue: function (record, key) {
            var val = '';
            var values = this.getValuesArray(record);
            if(key in values)
            {
                val = values[key];
            }
            return val;
        },

        /**
         * Checks if amount of options is more than limit value.
         *
         * @param {Object} record - Data to be preprocessed.
         * @returns {Boolean}
         */
        isExpandable: function (record) {
            var values = this.getValuesArray(record);
            return ("description" in values);
        }
    });
});
