define([
    'uiComponent',
    'jquery',
    'ko',
    'underscore',
    'mage/translate'
], function (Component, $, ko, _) {
    'use strict';

    return Component.extend({
        defaults: {
            productId: ko.observable(''),
            image: ko.observable(''),
            productName: ko.observable(''),
            description: ko.observable(''),
            price: ko.observable(''),
            addToCartUrl: ko.observable(''),
            customPrice: ko.observable('')
        },

        isVisible: function () {
           return this.has_donation;
        },

        getItems: function () {
            return Object.values(this.items);
        },

        changeDonation: function (obj, event) {
            var productId = event.currentTarget.value;
            var productData = this.items[productId];
            this.customPrice('');
            if (typeof productData == 'undefined') {
                this.productId('');
                this.image('');
                this.productName('');
                this.description('');
                this.price('');
                this.addToCartUrl('');
                return;
            }
            this.productId(productData['id']);
            this.image(productData['image']);
            this.productName(productData['name']);
            this.description(productData['description']);
            this.price(productData['price']);
            this.addToCartUrl(productData['add_to_cart_url']);
        },

        setCustomPrice: function (price) {
            this.customPrice(price);
        },

        setCustomPriceEvent: function (obj, event) {
            var price = event.currentTarget.value;
            this.customPrice(price);
        },

        getDynamicCustomPriceValue: function () {
            return $('#dynamic-custom-price').val();
        }
    });
});
