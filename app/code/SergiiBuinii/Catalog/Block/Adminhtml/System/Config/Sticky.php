<?php

namespace SergiiBuinii\Catalog\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use SergiiBuinii\Catalog\Helper\Config;
use Magento\Framework\DataObject;

class Sticky extends AbstractFieldArray
{
    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @codingStandardsIgnoreStart
     */
    protected function _prepareToRender()
    {
        // @codingStandardsIgnoreEnd
        $this->addColumn(Config::STICKY_LINK, ['label' => __('Link'), 'renderer' => false]);
        $this->addColumn(Config::STICKY_CLASS, ['label' => __('Class'), 'renderer' => false]);
        $this->addColumn(Config::STICKY_LABEL, ['label' => __('Label'), 'renderer' => false]);
        $this->addColumn(Config::STICKY_POSITION, ['label' => __('Position'), 'renderer' => false]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Sticky Element');
    }
}
