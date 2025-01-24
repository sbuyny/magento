<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CategoryWidget\Helper;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use \Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Store Manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Registry
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->registry = $registry;
    }

    /**
     * Get the current catalog category
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }
}
