<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CategoryWidget\Model\Category;

class CategoryNav
{
    /**#@+
     * Category attribute
     * @type string
     */
    const CATEGORY_NAV_NAME_ATTRIBUTE = 'or_category_nav_name';
    /**#@- */

    /**
     * Get Alternate category name
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    public function getCategoryNavName($category)
    {
        $alternateName = $category->getData(self::CATEGORY_NAV_NAME_ATTRIBUTE);
        return $alternateName ? $alternateName : $category->getName();
    }
}
