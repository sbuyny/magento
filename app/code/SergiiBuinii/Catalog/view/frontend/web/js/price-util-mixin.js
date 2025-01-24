define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    return function (target) {
        target.deepClone = function(obj) {
            Object.keys(obj).forEach(function(key) {
                if (obj[key].hasOwnProperty('amount') && $.isNumeric(obj[key]['amount'])) {
                    obj[key]['amount'] = obj[key]['amount'] * 1;
                }
            });
            return JSON.parse(JSON.stringify(obj));
        };
        return target;
    };
});
