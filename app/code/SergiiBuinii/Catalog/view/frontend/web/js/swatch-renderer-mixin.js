define([
    'jquery',
    'underscore',
    'jquery/ui'
], function ($, _) {
    'use strict';

    return function(widget){
        $.widget(
            'mage.SwatchRenderer', $['mage']['SwatchRenderer'], {

                /**
                 * Determine product id and related data
                 *
                 * @returns {{productId: *, isInProductView: bool}}
                 * @private
                 */
                _determineProductData: function () {
                    // Check if product is in a list of products.
                    var productId,
                        isInProductView = false,
                        $finalPrice = this.element.parents('.product-item-details').find('.price-box.price-final_price');


                    productId = $finalPrice.length ? $finalPrice.attr('data-product-id') : this.element.attr('data-product-id');


                    if (!productId) {
                        // Check individual product.
                        productId = $('[name=product]').val();
                        isInProductView = productId > 0;
                    }


                    return {
                        productId: productId,
                        isInProductView: isInProductView
                    };
                },

				/**
				 * Input for submit form.
				 * This control shouldn't have "type=hidden", "display: none" for validation work :(
				 *
				 * @param {Object} config
				 * @private
				 */
				_RenderFormInput: function (config) {
				    var msgTxt = "Please select " + config.label.toLowerCase();
					return '<input class="' + this.options.classes.attributeInput + ' super-attribute-select" ' +
						'name="super_attribute[' + config.id + ']" ' +
						'type="text" ' +
						'value="" ' +
						'data-selector="super_attribute[' + config.id + ']" ' +
						'data-validate="{required: true}" ' +
						'data-msg-required="' + msgTxt + '" ' +
						'aria-required="true" ' +
						'aria-invalid="false">';
				},

                /**
                 * Modify on click method
                 *
                 * @param {Object} $this
                 * @param {Object} $widget
                 * @private
                 */
                _OnClick: function ($this, $widget) {
                    var result = this._super($this, $widget);
                    this.savePreselectedSwatches($this, $widget);
                    return result;
                },

                /**
                 * Pre select attributes from cookie if it not set in URL
                 *
                 * @param {Object} [selectedAttributes]
                 * @private
                 */
                _EmulateSelected: function (selectedAttributes) {
                    if (!this._determineProductData().isInProductView) {
                        return this._super(selectedAttributes);
                    }
                    if (window.swatchRenderer.isPreselectEnabled
                        && Object.entries(selectedAttributes).length <= 1
                    ) {
                        var $this = this,
                            products = $.cookieStorage.get('preselected-swatches'),
                            attributes = this.options.jsonConfig.attributes,
                            productId = this.element.parents('.product-item-details').find('.price-box.price-final_price').attr('data-product-id');
                        if (!productId) {
                            productId = this.element.parents().find('input[name="product"]').val();
                        }
                        if (productId && products && typeof products[productId] !== 'undefined') {
                            selectedAttributes = products[productId]['attributes'];
                        }
                         if (attributes.length > Object.keys(selectedAttributes).length) {
                            var indexes = this.options.jsonConfig.index;
                            $.each(attributes, function (index, attribute) {
                                var isExists = typeof selectedAttributes[attribute['code']] !== 'undefined' || attribute['code'] === 'or_color';
                                if (!isExists) {
                                    var firstSwatchProductId = attribute['options'][0]['products'][0];
                                    if(firstSwatchProductId !== undefined) {
                                        selectedAttributes[attribute['code']] = indexes[firstSwatchProductId][attribute['id']];
                                    }
                                }
                            });
                            if (typeof selectedAttributes === "undefined") {
                                $.each(attributes, function (index, attribute) {
                                    selectedAttributes[attribute['code']] = $this._getCurrentAttributeOptionId(attribute, selectedAttributes);
                                });
                            }

                        }
                    }
                    return this._super(selectedAttributes);
                },

                /**
                 * Decide of out-of-stock product information what option need to choose
                 *
                 * @param {Object} [attribute]
                 * @param {Object} [selectedAttributes]
                 * @private
                 */
                _getCurrentAttributeOptionId: function (attribute, selectedAttributes) {
                    var $this = this,
                        currentAttributeOptionId = '',
                        mappedAttributes = $this.options.jsonConfig.mappedAttributes,
                        selectedAttributeProducts = {};
                    $.each(selectedAttributes, function (attributeCode, optionId) {
                        if (typeof selectedAttributeProducts[attributeCode] != 'undefined') {
                            return;
                        }
                        $.each(mappedAttributes, function (mappedAttributeId, mappedAttribute) {
                            if (mappedAttribute['code'] !== attributeCode) {
                                return;
                            }
                            $.each(mappedAttribute['options'], function (index, selectedOption) {
                                if (selectedOption['id'] !== optionId) {
                                    return;
                                }
                                selectedAttributeProducts[attributeCode] = selectedOption['products'];
                            });
                        });
                    });
                    for (var i = 0; attribute['options'].length > i; i++) {
                        var currentOptionProducts = attribute['options'][i]['products'];
                        if (_.isEmpty(selectedAttributeProducts)) {
                            if (!currentOptionProducts.length) {
                                continue;
                            }
                        } else {
                            var productsToCompare = Object.values(selectedAttributeProducts);
                            productsToCompare.push(currentOptionProducts);
                            var availableProducts = _.intersection.apply(_, productsToCompare);
                            if (!availableProducts.length) {
                                continue;
                            }
                        }
                        currentAttributeOptionId = attribute['options'][i]['id'];
                        break;
                    }
                    return currentAttributeOptionId;
                },

                /**
                 * Write to cookies data on click to swatch
                 *
                 * @param {Object} $this
                 * @param {Object} $widget
                 */
                savePreselectedSwatches: function ($this, $widget) {
                    if (window.swatchRenderer.isPreselectEnabled) {
                        var products = $.cookieStorage.get('preselected-swatches') ? $.cookieStorage.get('preselected-swatches') : {},
                            $finalPrice = this.element.parents('.product-item-details').find('.price-box.price-final_price'),
                            productId = $finalPrice.length ? $finalPrice.attr('data-product-id') : this.element.attr('data-product-id'),
                            $parent = $this.parents('.' + $widget.options.classes.attributeClass),
                            attributeId = $parent.attr('attribute-id'),
                            attributeCode = this._getAttributeCodeById(attributeId);
                        if (!productId) {
                            productId = this.element.parents().find('input[name="product"]').val();
                        }
                        if (productId) {
                            var product = products[productId];
                            if (!$this.hasClass('selected') && !$this.hasClass('disable_selected')) {
                                if (typeof product != 'undefined' && product.attributes) {
                                    delete products[productId]['attributes'][attributeCode];
                                }
                            } else {
                                var currentAttributes = typeof product != 'undefined' && product.attributes ? products[productId].attributes : {};
                                currentAttributes[attributeCode] = $this.attr('option-id');
                                products[productId] = {
                                    product_id: productId,
                                    attributes: currentAttributes
                                };
                            }
                            $.cookieStorage.set('preselected-swatches', products);
                        }
                    }
                },

                //start fix Magento bug - https://github.com/magento/magento2/issues/3149
                /**
                 * Start update base image process based on event name
                 * @param {Array} images
                 * @param {jQuery} context
                 * @param {Boolean} isInProductView
                 * @param {String|undefined} eventName
                 */
                updateBaseImage: function (images, context, isInProductView, eventName) {
                    if (!this._determineProductData().isInProductView) {
                        return this._super(images, context, isInProductView);
                    }
                    var gallery = context.find(this.options.mediaGallerySelector).data('gallery');

                    if (eventName === undefined && gallery !== undefined) {
                        this.processUpdateBaseImage(images, context, isInProductView, gallery);
                    } else {
                        context.find(this.options.mediaGallerySelector).on('gallery:loaded', function (loadedGallery) {
                            loadedGallery = context.find(this.options.mediaGallerySelector).data('gallery');
                            this.processUpdateBaseImage(images, context, isInProductView, loadedGallery);
                        }.bind(this));
                    }
                },

                /**
                 * Update [gallery-placeholder] or [product-image-photo]
                 * @param {Array} images
                 * @param {jQuery} context
                 * @param {Boolean} isInProductView
                 * @param {Object} gallery
                 */
                processUpdateBaseImage: function (images, context, isInProductView, gallery) {
                    var justAnImage = images[0],
                        initialImages = this.options.mediaGalleryInitial,
                        imagesToUpdate,
                        isInitial;

                    if (isInProductView) {
                        imagesToUpdate = images.length ? this._setImageType($.extend(true, [], images)) : [];
                        isInitial = _.isEqual(imagesToUpdate, initialImages);

                        if (this.options.gallerySwitchStrategy === 'prepend' && !isInitial) {
                            imagesToUpdate = imagesToUpdate.concat(initialImages);
                        }

                        imagesToUpdate = this._setImageIndex(imagesToUpdate);
                        gallery.updateData(imagesToUpdate);

                        if (isInitial) {
                            $(this.options.mediaGallerySelector).AddFotoramaVideoEvents();
                        } else {
                            $(this.options.mediaGallerySelector).AddFotoramaVideoEvents({
                                selectedOption: this.getProductImage(),
                                dataMergeStrategy: this.options.gallerySwitchStrategy
                            });
                        }

                        gallery.first();

                    } else if (justAnImage && justAnImage.img) {
                        context.find('.product-image-photo').attr('src', justAnImage.img);
                    }
                }
                //end fix Magento bug - https://github.com/magento/magento2/issues/3149
            }
        );
        return $['mage']['SwatchRenderer'];
    };
});
