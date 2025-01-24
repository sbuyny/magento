<?php

namespace SergiiBuinii\Catalog\Pricing\Render;

use Magento\Catalog\Pricing\Render\FinalPriceBox as OriginFinalPriceBox;
use Magento\ConfigurableProduct\Pricing\Render\FinalPriceBox as ConfigurableFinalPriceBox;

class FinalPriceBox extends ConfigurableFinalPriceBox
{
    /**
     * Override the toHtml to remove price rendering if product isn't saleable.
     *
     * @return string
     */
    protected function _toHtml()
    {
        $result = OriginFinalPriceBox::_toHtml();
        if ($result && !$this->getSaleableItem()->getIsSalable()) {
            $result = '';
        }

        return $result;
    }
}
