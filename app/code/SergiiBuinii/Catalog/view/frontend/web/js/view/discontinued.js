define([
    'uiComponent',
    'jquery',
    'ko',
    'underscore',
    'mage/translate'
], function (Component, $, ko, _) {
    'use strict';

    var mainInfoProductBlock = $('.product-info-main');

    return Component.extend({
        defaults: {
            isVisible: ko.observable(false),
            isSuggestionVisible: ko.observable(false),
            discontinuedText: ko.observable(''),
            suggestionProductName: ko.observable(''),
            suggestionProductUrl: ko.observable(''),
            listens: {
                rates: 'rateHasChanged'
            }
        },

        setIsVisible: function (isVisible) {
            if (isVisible) {
                mainInfoProductBlock.addClass('discontinued');
            } else {
                mainInfoProductBlock.removeClass('discontinued');
            }
            this.isVisible(isVisible);
        },

        getSuggestionProductName: function () {
            return this.suggestionProductName;
        },

        getSuggestionProductUrl: function () {
            return this.suggestionProductUrl;
        },

        getDiscontinuedText: function () {
            return this.discontinuedText;
        },

        setSuggestionProductName: function (name) {
            this.suggestionProductName(name);
        },

        setSuggestionProductUrl: function (url) {
            this.suggestionProductUrl(url);
        },

        setDiscontinuedText: function (text) {
            this.discontinuedText(text);
        }
    });
});
