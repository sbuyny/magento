<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CustomerDonation\Block\Adminhtml\Group\Edit\Form;

use Magento\Framework\Escaper;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use SergiiBuinii\CustomerDonation\Block\Adminhtml\Group\Edit\Form\Element\ProductGridField;

/**
 * Class ProductGridRenderer
 *
 * Renderer for product grid field
 */
class ProductGridRenderer extends AbstractElement
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * ProductGridRenderer constructor
     *
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param array $data
     */
    public function __construct(
        CollectionFactory $factoryCollection,
        LayoutInterface $layout,
        Factory $factoryElement,
        Escaper $escaper,
        $data = []
    ) {
        $this->layout = $layout;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * Render product grid field
     *
     * @return string
     */
    public function getElementHtml()
    {
        return $this->layout->createBlock(ProductGridField::class)->toHtml();
    }
}
