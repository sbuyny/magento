<?php

namespace SergiiBuinii\Catalog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    /**#@+
     * System config xml path constants and other constant variables
     * @type string
     */
    const XML_PATH_STICKY_MENU = 'SergiiBuinii_catalog/sticky/elements';
    const XML_PATH_PRODUCT_LISTING_ENABLED_PRE_SELECT = 'SergiiBuinii_catalog/product_listing/enabled_pre_select';
    const XML_PATH_CONFIGURABLE_PRODUCT_IMAGE = 'SergiiBuinii_catalog/product_image/configurable_product_image';
    const XML_PATH_DISCONTINUED_ENABLED = 'SergiiBuinii_catalog/discontinued/enabled';
    const XML_PATH_DISCONTINUED_ATTRIBUTE_CODE = 'SergiiBuinii_catalog/discontinued/discontinued_attribute_code';
    const XML_PATH_DISCONTINUED_TEXT = 'SergiiBuinii_catalog/discontinued/discounted_text';
    const XML_PATH_DISCONTINUED_PRODUCT_SUGGESTION_ENABLED = 'SergiiBuinii_catalog/discontinued/product_suggestion_enabled';
    const XML_PATH_DISCONTINUED_PRODUCT_SUGGESTION_SKU_ATTRIBUTE_CODE = 'SergiiBuinii_catalog/discontinued/product_suggestion_sku_attribute_code';

    const STICKY_LINK       = 'link';
    const STICKY_CLASS      = 'element_class';
    const STICKY_LABEL      = 'label';
    const STICKY_POSITION   = 'position';
    /**#@- */

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serialize;

    /**
     * Config constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     */
    public function __construct(
        Context $context,
        Json $json
    ) {
        $this->serialize = $json;
        parent::__construct($context);
    }

    /**
     * Get sticky menu
     *
     * @return array
     */
    public function getStickyMenu()
    {
        $position = [];
        $counter = 0;
        $preselectMap = $this->scopeConfig->getValue(self::XML_PATH_STICKY_MENU);
        if (empty($preselectMap)) {
            return [];
        }
        $sticky = $this->serialize->unserialize($preselectMap);
        $stickyMenu = [];
        foreach ($sticky as $value) {
            $value = (array) $value;
            $stickyMenu[$counter] = [
                self::STICKY_LINK    => $value[self::STICKY_LINK],
                self::STICKY_LABEL   => $value[self::STICKY_LABEL],
                self::STICKY_CLASS   => $value[self::STICKY_CLASS]
            ];
            $position[$counter] = $value[self::STICKY_POSITION] ?
                $value[self::STICKY_POSITION] : 0;
            $counter++;
        }
        array_multisort($position, $stickyMenu);
        return $stickyMenu;
    }

    /**
     * Check if pre selected feature enabled for product listing
     *
     * @return bool
     */
    public function isEnabledPreSelect()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_PRODUCT_LISTING_ENABLED_PRE_SELECT,
            ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get Configurable Product Image Mode
     *
     * @return string
     */
    public function getConfigurableProductImageMode()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CONFIGURABLE_PRODUCT_IMAGE);
    }

    /**
     * Check if discontinued feature enabled
     *
     * @return bool
     */
    public function isEnabledDiscontinued()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISCONTINUED_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve discontinued attribute code
     *
     * @return string
     */
    public function getDiscontinuedAttributeCode()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DISCONTINUED_ATTRIBUTE_CODE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve discontinued text
     *
     * @return string
     */
    public function getDiscontinuedText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DISCONTINUED_TEXT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if discontinued product suggestion feature enabled
     *
     * @return bool
     */
    public function isEnabledDiscountedProductSuggestion()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISCONTINUED_PRODUCT_SUGGESTION_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve discontinued product suggestion attribute code
     *
     * @return string
     */
    public function getDiscontinuedProductSuggestionAttributeCode()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DISCONTINUED_PRODUCT_SUGGESTION_SKU_ATTRIBUTE_CODE,
            ScopeInterface::SCOPE_STORE
        );
    }
}
