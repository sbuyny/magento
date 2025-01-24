<?php

namespace SergiiBuinii\Catalog\Pricing\Price;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\TierPrice;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Pricing\PriceInfoInterface;

class ExtendTierPrice extends TierPrice
{
    /**
     * Price type extended tier
     */
    const PRICE_CODE = 'extended_tier_price';
    
    /**
     * Get price value
     *
     * @return bool|float
     */
    public function getValue()
    {
        if ($this->getProduct()->getTypeId() !== Configurable::TYPE_CODE) {
            return parent::getValue();
        }
    
        if (null === $this->value) {
            $prices = $this->getChildTierPrices();
            $prevQty = PriceInfoInterface::PRODUCT_QUANTITY_DEFAULT;
            $this->value = $prevPrice = $tierPrice = false;
            $priceGroup = $this->groupManagement->getAllCustomersGroup()->getId();
        
            foreach ($prices as $price) {
                if (!$this->canApplyTierPrice($price, $priceGroup, $prevQty)) {
                    continue;
                }
                if (false === $prevPrice || $this->isFirstPriceBetter($price['website_price'], $prevPrice)) {
                    $tierPrice = $prevPrice = $price['website_price'];
                    $prevQty = $price['price_qty'];
                    $priceGroup = $price['cust_group'];
                    $this->value = (float)$tierPrice;
                }
            }
        }
        return $this->value;
    }
    
    /**
     * Retrieve child tier prices
     *
     * @return array
     */
    protected function getChildTierPrices()
    {
        $options = $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct());
        $rawPriceLists = [];
        /** @var Product $option */
        foreach ($options as $option) {
            $rawPriceList = $option->getData(parent::PRICE_CODE);
            if (null === $rawPriceList || !is_array($rawPriceList)) {
                /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute */
                $attribute = $option->getResource()->getAttribute(parent::PRICE_CODE);
                if ($attribute) {
                    $attribute->getBackend()->afterLoad($option);
                    $rawPriceList = $option->getData(parent::PRICE_CODE);
                }
            }
            if (null === $rawPriceList || !is_array($rawPriceList)) {
                $rawPriceList = [];
            }
            if (!$this->isPercentageDiscount()) {
                foreach ($rawPriceList as $index => $rawPrice) {
                    if (isset($rawPrice['price'])) {
                        $rawPriceList[$index]['price'] =
                            $this->priceCurrency->convertAndRound($rawPrice['price']);
                    }
                    if (isset($rawPrice['website_price'])) {
                        $rawPriceList[$index]['website_price'] =
                            $this->priceCurrency->convertAndRound($rawPrice['website_price']);
                    }
                    if (!empty($rawPrice)) {
                        $rawPriceLists[] = $rawPrice;
                        continue;
                    }
                }
            }
        }
        
        return $rawPriceLists;
    }
    
    /**
     * Reset data
     *
     * @return $this
     */
    public function reset()
    {
        $this->value = null;
        return $this;
    }
}
