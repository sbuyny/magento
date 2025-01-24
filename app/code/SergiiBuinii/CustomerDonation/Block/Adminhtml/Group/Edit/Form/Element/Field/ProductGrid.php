<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */
namespace SergiiBuinii\CustomerDonation\Block\Adminhtml\Group\Edit\Form\Element\Field;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Registry;
use Magento\Backend\Helper\Data;
use Magento\Customer\Model\GroupRegistry;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Json\EncoderInterface;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use SergiiBuinii\CustomerDonation\Model\ResourceModel\CustomerGroup\Donation;
use Magento\Directory\Model\Currency;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\Product\Type;

class ProductGrid extends Extended
{
    /**
     * @var array product ids
     */
    protected $productIds = [];

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Customer\Model\GroupRegistry
     */
    protected $groupRegistry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * ProductGrid constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\GroupRegistry $groupRegistry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        Registry $coreRegistry,
        GroupRegistry $groupRegistry,
        EncoderInterface $jsonEncoder,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->jsonEncoder = $jsonEncoder;
        $this->coreRegistry = $coreRegistry;
        $this->groupRegistry = $groupRegistry;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    // @codingStandardsIgnoreStart
    /**
     * Init GRID
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('ba_customer_group_products');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * Select product for current tag
     *
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return $this|Extended
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'is_tagged') {
            $productIds = $this->getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare data collection for grid
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _prepareCollection()
    {
        if (!empty($this->getSelectedProducts())) {
            $this->setDefaultFilter(['is_tagged' => 1]);

        }
        $collection = $this->collectionFactory->create()
            ->addAttributeToFilter(ProductInterface::TYPE_ID, array('in' => [Type::TYPE_SIMPLE, Type::TYPE_VIRTUAL]))
            ->addAttributeToSelect(
                'name'
            )->addAttributeToSelect(
                'sku'
            )->addAttributeToSelect(
                'price'
            );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Build columns for grid
     *
     * @return Extended
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'is_tagged',
            [
                'type' => 'checkbox',
                'name' => 'is_tagged',
                'values' => $this->getSelectedProducts(),
                'index' => 'entity_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('sku', ['header' => __('SKU'), 'index' => 'sku']);
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'currency_code' => (string)$this->_scopeConfig->getValue(
                    Currency::XML_PATH_CURRENCY_BASE,
                    ScopeInterface::SCOPE_STORE
                ),
                'index' => 'price'
            ]
        );

        return parent::_prepareColumns();
    }
    // @codingStandardsIgnoreEnd

    /**
     * Retrieve selected products ids
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');

        if ($products === null) {
            $groupId = $this->coreRegistry->registry(RegistryConstants::CURRENT_GROUP_ID);

            if ($groupId === null) {
                $groupId = $this->getRequest()->getParam('id');
            }

            if ($groupId !== null) {
                $ids = $this->groupRegistry->retrieve($groupId)->getData(Donation::CUSTOMER_GROUP_DONATION_PRODUCT_IDS);
                if ($ids) {
                    $this->productIds = explode(Donation::PRODUCT_IDS_SEPARATOR, $ids);
                }
            }

            return $this->productIds;
        }

        return $products;
    }

    /**
     * URL for AJAX grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('customer_donation/grid/index', ['_current' => true]);
    }

    /**
     * Flag (is needed customers or admin)
     *
     * @return bool
     */
    public function isCustomerNeeded()
    {
        return false;
    }

    /**
     * Retrieve JSON object with product ids
     *
     * @return string
     */
    public function getProductsJson()
    {
        if (!empty($this->productIds)) {
            return $this->jsonEncoder->encode(array_flip($this->productIds));
        }
        return '{}';
    }
}
