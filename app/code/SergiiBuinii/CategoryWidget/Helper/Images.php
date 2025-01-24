<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CategoryWidget\Helper;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Helper\Output;
use Magento\Framework\App\Helper\Context;

class Images extends AbstractHelper
{
    /**#@+
     * Custom image attributes for category
     */
    const MOBILE_CATEGORY_IMAGE_ATTRIBUTE      = 'category_mobile_image';
    const TABLET_CATEGORY_IMAGE_ATTRIBUTE      = 'category_tablet_image';
    /**#@- */

    /**
     * @var \Magento\Catalog\Helper\Output $output
     */
    public $output;

    /**
     * Images constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Helper\Output $output
     */
    public function __construct(
        Context $context,
        Output $output
    ) {
        $this->output = $output;
        parent::__construct($context);
    }

    /**
     * Get media image url from category
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return string | null
     */
    public function getMediaCategoryImage($category)
    {
        return $category->getImageUrl(self::MOBILE_CATEGORY_IMAGE_ATTRIBUTE);
    }

    /**
     * Get tablet image url from category
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return string | null
     */
    public function getTabletCategoryImage($category)
    {
        return $category->getImageUrl(self::TABLET_CATEGORY_IMAGE_ATTRIBUTE);
    }

    /**
     * Get output for category media image
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param $imgHtml
     * @return string
     */
    public function getMediaOutput($category, $imgHtml)
    {
        return $this->output->categoryAttribute($category, $imgHtml, self::MOBILE_CATEGORY_IMAGE_ATTRIBUTE);
    }

    /**
     * Get output for category tablet image
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param $imgHtml
     * @return string
     */
    public function getTabletOutput($category, $imgHtml)
    {
        return $this->output->categoryAttribute($category, $imgHtml, self::TABLET_CATEGORY_IMAGE_ATTRIBUTE);
    }
}