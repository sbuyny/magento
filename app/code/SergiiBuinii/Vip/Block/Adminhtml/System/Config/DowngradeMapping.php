<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Vip\Block\Adminhtml\System\Config;

use SergiiBuinii\Vip\Block\Adminhtml\System\Config\Renderer\CustomerGroupSelect;
use SergiiBuinii\Vip\Helper\Config;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class DowngradeMapping extends AbstractFieldArray
{
    /**
     * @var \SergiiBuinii\Vip\Block\Adminhtml\System\Config\Renderer\CustomerGroupSelect
     */
    protected $customerGroupRenderer;
    
    /**
     * @var \SergiiBuinii\Vip\Block\Adminhtml\System\Config\Renderer\CustomerGroupSelect
     */
    protected $vipGroupRenderer;
    
    /**
     * DowngradeMapping constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
    
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @codingStandardsIgnoreStart
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            Config::VIP_GROUP,
            [
                'label' => __('Vip Group'),
                'renderer' => $this->getVipGroupRenderer()
            ]
        );
        $this->addColumn(
            Config::CUSTOMER_GROUP,
            [
                'label'    => __('Customer Group'),
                'renderer' => $this->getCustomerGroupRenderer()
            ]
        );
        
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Mapping');
    }
    
    /**
     * Get Customer Group select block
     *
     * @return \SergiiBuinii\Vip\Block\Adminhtml\System\Config\Renderer\CustomerGroupSelect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getCustomerGroupRenderer()
    {
        if (!$this->customerGroupRenderer) {
            $this->customerGroupRenderer = $this->getRenderer();
        }
        return $this->customerGroupRenderer;
    }
    
    /**
     * Get Vip Group select block
     *
     * @return \SergiiBuinii\Vip\Block\Adminhtml\System\Config\Renderer\CustomerGroupSelect
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getVipGroupRenderer()
    {
        if (!$this->vipGroupRenderer) {
            $this->vipGroupRenderer = $this->getRenderer();
        }
        return $this->vipGroupRenderer;
    }
    
    
    
    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @codingStandardsIgnoreStart
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $optionExtraAttr = [];
        $optionExtraAttr[
        'option_'.$this->getVipGroupRenderer()
            ->calcOptionHash(
                $row->getData(Config::VIP_GROUP)
            )
        ]
            = 'selected="selected"';
        $optionExtraAttr[
        'option_'.$this->getCustomerGroupRenderer()
            ->calcOptionHash(
                $row->getData(Config::CUSTOMER_GROUP)
            )
        ] = 'selected="selected"';
        $row->setData(
            'option_extra_attrs',
            $optionExtraAttr
        );
    }
    
    /**
     * Get renderer
     *
     * @return \SergiiBuinii\Vip\Block\Adminhtml\System\Config\Renderer\CustomerGroupSelect
     */
    protected function getRenderer()
    {
         return $this->getLayout()->createBlock(
            CustomerGroupSelect::class,
            '',
            ['data' => ['is_render_to_js_template' => true]]
        );
    }
}
