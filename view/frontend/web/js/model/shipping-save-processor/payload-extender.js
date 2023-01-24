define(['jquery',], function ($) {
    'use strict';

    return function (payload) {
        payload.addressInformation['extension_attributes'] = {};
        payload.addressInformation['extension_attributes'].inpost_point_id = $('#inpost_selected_point_id').val();

        return payload;
    };
});
