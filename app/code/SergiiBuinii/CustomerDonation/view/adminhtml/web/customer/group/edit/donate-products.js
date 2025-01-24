/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {
        var selectedProducts = config.selectedProducts,
            taggedProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName];

        $('admin_tagged_products').value = Object.toJSON(taggedProducts);

        /**
         * Register Admin's Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerAdminProduct(grid, element, checked)
        {
            if (checked) {
                taggedProducts.set(element.value, '1');
            } else {
                taggedProducts.unset(element.value);
            }
            $('admin_tagged_products').value = Object.toJSON(taggedProducts);
            grid.reloadParams = {
                'selected_products[]': taggedProducts.keys()
            };
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function taggedProductRowClick(grid, event)
        {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;
                    gridJsObject.setCheckboxChecked(checkbox[0], checked);
                }
            }
        }

        gridJsObject.rowClickCallback = taggedProductRowClick;
        gridJsObject.checkboxCheckCallback = registerAdminProduct;
    };
});
