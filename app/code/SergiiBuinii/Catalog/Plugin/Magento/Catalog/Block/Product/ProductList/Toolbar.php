<?php

namespace SergiiBuinii\Catalog\Plugin\Magento\Catalog\Block\Product\ProductList;

use Magento\Catalog\Block\Product\ProductList\Toolbar as OriginClass;
use Magento\Framework\Data\Collection;
use Magento\Framework\DB\Select;

class Toolbar
{
    /**#@+
     * Order array indexes
     */
    const ORDER_FILED_INDEX = 0;
    const ORDER_DIRECTION_INDEX = 0;
    /**#@- */
    
    /**#@+
     * Price orders
     */
    const  MIN_PRICE_ORDER = 'price_index.min_price';
    const  MAX_PRICE_ORDER = 'price_index.max_price';
    /**#@- */
    
    /**
     * Around set collection plugin
     *
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\Data\Collection $collection
     * @return \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    public function aroundSetCollection(OriginClass $subject, \Closure $proceed, Collection $collection)
    {
        $toolbar = $proceed($collection);
        
        $this->updatePriceOrder($toolbar);
        return $toolbar;
    }
    
    /**
     * Update price sorting
     *
     * As PLP was modify to show maximum price instead of minimal,
     * need to replace "price_index.min_price" to "price_index.max_price" in order statement.
     *
     * @see \Magento\Catalog\Model\ResourceModel\Product\Collection::addAttributeToSort
     * @param \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar
     */
    private function updatePriceOrder($toolbar)
    {
        $select = $toolbar->getCollection()->getSelect();
        $orders = $select->getPart(Select::ORDER);
        foreach ($orders as &$order) {
            if (isset($order[self::ORDER_FILED_INDEX]) &&
                $order[self::ORDER_FILED_INDEX] === self::MIN_PRICE_ORDER) {
                $order[self::ORDER_FILED_INDEX] = self::MAX_PRICE_ORDER;
            }
        }
        $select->setPart(Select::ORDER, $orders);
    }
}
