<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CategoryWidget\Plugin\Block;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Plugin\Block\Topmenu as OriginClass;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Tree\Node;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Model\ResourceModel\Category\StateDependentCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Theme\Block\Html\Topmenu as MenuBlock;
use SergiiBuinii\CategoryWidget\Model\Category\CategoryNav;

class Topmenu extends OriginClass
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private $storeManager;

    /**
     * @var \Magento\Catalog\Model\Layer\Resolver $layerResolver
     */
    private $layerResolver;

    /**
     * @var \SergiiBuinii\CategoryWidget\Model\Category\CategoryNav $categoryNav
     */
    private $categoryNav;

    /**
     * Topmenu constructor
     *
     * @param \Magento\Catalog\Helper\Category $catalogCategory
     * @param \Magento\Catalog\Model\ResourceModel\Category\StateDependentCollectionFactory $categoryCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \SergiiBuinii\CategoryWidget\Model\Category\CategoryNav $categoryNav
     */
    public function __construct(
        CategoryHelper $catalogCategory,
        StateDependentCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        Resolver $layerResolver,
        CategoryNav $categoryNav
    ) {
        $this->collectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->layerResolver = $layerResolver;
        $this->categoryNav = $categoryNav;
        parent::__construct($catalogCategory, $categoryCollectionFactory, $storeManager, $layerResolver);
    }

    /**
     * Build category tree for menu block
     *
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return void
     * @SuppressWarnings("PMD.UnusedFormalParameter")
     */
    public function beforeGetHtml(
        MenuBlock $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    ) {
        $rootId = $this->storeManager->getStore()->getRootCategoryId();
        $storeId = $this->storeManager->getStore()->getId();
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->getCategoryTree($storeId, $rootId);
        $currentCategory = $this->getCurrentCategory();
        $mapping = [$rootId => $subject->getMenu()];  // use nodes stack to avoid recursion
        foreach ($collection as $category) {
            $categoryParentId = $category->getParentId();
            if (!isset($mapping[$categoryParentId])) {
                $parentIds = $category->getParentIds();
                foreach ($parentIds as $parentId) {
                    if (isset($mapping[$parentId])) {
                        $categoryParentId = $parentId;
                    }
                }
            }

            /** @var Node $parentCategoryNode */
            $parentCategoryNode = $mapping[$categoryParentId];

            $categoryNode = new Node(
                $this->getCategoryAsArray(
                    $category,
                    $currentCategory,
                    $category->getParentId() == $categoryParentId
                ),
                'id',
                $parentCategoryNode->getTree(),
                $parentCategoryNode
            );
            $parentCategoryNode->addChild($categoryNode);

            $mapping[$category->getId()] = $categoryNode; //add node in stack
        }
    }

    /**
     * Get current Category from catalog layer
     *
     * @return \Magento\Catalog\Model\Category
     */
    private function getCurrentCategory()
    {
        $catalogLayer = $this->layerResolver->get();

        if (!$catalogLayer) {
            return null;
        }

        return $catalogLayer->getCurrentCategory();
    }

    /**
     * Convert category to array
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Catalog\Model\Category $currentCategory
     * @param bool $isParentActive
     * @return array
     */
    private function getCategoryAsArray($category, $currentCategory, $isParentActive)
    {
        return [
            'name' => $category->getName(),
            'id' => 'category-node-' . $category->getId(),
            'url' => $this->catalogCategory->getCategoryUrl($category),
            'has_active' => in_array((string)$category->getId(), explode('/', $currentCategory->getPath()), true),
            'is_active' => $category->getId() == $currentCategory->getId(),
            'is_parent_active' => $isParentActive,
            CategoryNav::CATEGORY_NAV_NAME_ATTRIBUTE => $this->categoryNav->getCategoryNavName($category)
        ];
    }

    /**
     * Get Category Tree
     *
     * @param int $storeId
     * @param int $rootId
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCategoryTree($storeId, $rootId)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = parent::getCategoryTree($storeId, $rootId);
        $collection->addAttributeToSelect(CategoryNav::CATEGORY_NAV_NAME_ATTRIBUTE);
        return $collection;
    }
}
