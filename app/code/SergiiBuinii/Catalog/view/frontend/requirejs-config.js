var config = {
    map: {
        '*': {
            'Magento_Catalog/js/product/breadcrumbs':
                'SergiiBuinii_Catalog/js/product/extend-breadcrumbs'
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'SergiiBuinii_Catalog/js/swatch-renderer-mixin': true
            },
            'Magento_Catalog/js/price-utils': {
                'SergiiBuinii_Catalog/js/price-util-mixin': true
            }
        }
    }
};
