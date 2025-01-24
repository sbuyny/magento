<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CategoryWidget\Block\Html;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use SergiiBuinii\CategoryWidget\Helper\Data;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Widget\Model\Template\Filter;
use Magento\Theme\Block\Html\Topmenu as OriginClass;
use Magento\Framework\Data\Tree\Node;
use SergiiBuinii\CategoryWidget\Model\Category\CategoryNav;

class Topmenu extends OriginClass
{
    /**
     * @var \SergiiBuinii\CategoryWidget\Helper\Data $dataHelper
     */
    protected $dataHelper;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Widget\Model\Template\Filter $filter
     */
    protected $filter;

    /**
     * Topmenu constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Tree\NodeFactory $nodeFactory
     * @param \Magento\Framework\Data\TreeFactory $treeFactory
     * @param \SergiiBuinii\CategoryWidget\Helper\Data $dataHelper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Widget\Model\Template\Filter $filter
     * @param array $data
     */
    public function __construct(
        Context $context,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        Data $dataHelper,
        CategoryFactory $categoryFactory,
        Filter $filter,
        array $data = []
    ) {

        $this->dataHelper = $dataHelper;
        parent::__construct($context, $nodeFactory, $treeFactory, $data);
        $this->categoryFactory = $categoryFactory;
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @codingStandardsIgnoreStart
     */
    protected function _getHtml(
        Node $menuTree,
        $childrenWrapClass,
        $limit,
        array $colBrakes = []
    ) {
        // @codingStandardsIgnoreEnd
        $html = '';

        $children = $menuTree->getChildren();
        $parentLevel = $menuTree->getLevel();
        $childLevel = $parentLevel === null ? 0 : $parentLevel + 1;

        $counter = 1;
        $itemPosition = 1;
        $childrenCount = $children->count();

        $parentPositionClass = $menuTree->getPositionClass();
        $itemPositionClassPrefix = $parentPositionClass ? $parentPositionClass . '-' : 'nav-';
        $getCategoryWidgetTitle = null;
        $categoryWidget = '';

        /** @var \Magento\Framework\Data\Tree\Node $child */
        foreach ($children as $child) {
            if ($childLevel === 0 && $child->getData('is_parent_active') === false) {
                continue;
            }

            $child->setLevel($childLevel);
            $child->setIsFirst($counter == 1);
            $child->setIsLast($counter == $childrenCount);
            $child->setPositionClass($itemPositionClassPrefix . $counter);
            $outermostClassCode = '';
            $outermostClass = $menuTree->getOutermostClass();

            if ($childLevel == 0 && $outermostClass) {
                $explodeName = explode(' ', $child->getName());
                $linkclass = str_replace(
                    '&',
                    'and',
                    strtolower(implode('-', $explodeName))
                );

                $outermostClassCode = ' class=" link-'
                    . preg_replace('/[^A-Za-z0-9\-]/', '', $linkclass)
                    . ' ui-corner-all '. $outermostClass .'" data-category-id="'.$child->getId().'"';
                $child->setClass($outermostClass);
            }

            if (count($colBrakes) && $colBrakes[$counter]['colbrake']) {
                $html .= '</ul></li><li class="column"><ul>';
            }

            if ($childLevel == 1 && $counter == 1) {
                $html .= '<li class="level1 nav-1-top-title"><span class="title">'
                    . $child->getParent()->getName()
                    . '</span></li>';
                $html .= '<li class="level1 nav-1-0"><a title="View All ' . $child->getParent()->getName()
                    . '" href="' . $child->getParent()->getUrl() . '">'
                    . $child->getParent()->getName() . '</a></li>';
            }

            $html .= '<li ' . $this->_getRenderedMenuItemAttributes($child) . '>';

            if ($childLevel == 1) {
                if ($counter == $childrenCount) {
                    $getCategoryWidgetTitle = $child->getParent()->getName();
                    $widgetCollection = $this->categoryFactory
                        ->create()
                        ->getCollection()
                        ->addAttributeToFilter('name', $getCategoryWidgetTitle)
                        ->setPageSize(1);
                    if ($widgetCollection->getSize()) {
                        $widgetCategoryId = (int)$widgetCollection->getFirstItem()->getId();
                        $dataCategoryWidget = $this->categoryFactory->create()->load($widgetCategoryId);
                        $categoryWidget = $dataCategoryWidget->getData('or_widget_category');
                    }
                }

                $explodeName = explode(' ', $child->getName());
                $linkclass = str_replace(
                    '&',
                    'and',
                    strtolower(implode('-', $explodeName))
                );

                $outermostClassCode = ' class=" link-'
                    . preg_replace('/[^A-Za-z0-9\-]/', '', $linkclass)
                    . ' ui-corner-all" data-category-id="'.$child->getId().'"';
            }

            $categoryName = $child->getData(CategoryNav::CATEGORY_NAV_NAME_ATTRIBUTE) ?: $child->getName();
            $html .= '<a title="'. $child->getName() .'" href="' . $child->getUrl() . '" '
                . $outermostClassCode . '><span class="category-name">' . $this->escapeHtml(
                    $categoryName
                ) . '</span></a>';

            if (($childLevel == 0 && $outermostClass) || ($childLevel == 1)) {
                $parentName = $child->getParent()->getName();
                $labelId = 'nav-' . str_replace(
                    ' ',
                    '-',
                    strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $child->getName()))
                    . '-' . $childLevel
                    . '-' . strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $parentName))
                );

                $html .= '<input id="'. $labelId
                    .'" type="checkbox" class="checkbox" name="'. $labelId .'" title="'
                    . $this->escapeHtml($child->getName())
                    .'"/><label for="'. $labelId .'"><em>'
                    . $this->escapeHtml($child->getName())
                    . '</em></label>';
            }

            $html .=  $this->_addSubMenu(
                $child,
                $childLevel,
                $childrenWrapClass,
                $limit
            ) . '</li>';

            $itemPosition++;
            $counter++;

            if ($categoryWidget) {
                $html .= $this->filter->filter($categoryWidget);
            }
        }

        if (count($colBrakes) && $limit) {
            $html = '<li class="column"><ul>' . $html . '</ul></li>';
        }

        return $html;
    }
}
