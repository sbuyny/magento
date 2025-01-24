<?php

namespace SergiiBuinii\Contact\Block\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class Topic extends AbstractFieldArray
{
    /**
     * Field code
     *
     * @type string
     */
    const TOPIC_FIELD_NAME  = 'contact_type';

    // @codingStandardsIgnoreStart
    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn(self::TOPIC_FIELD_NAME, ['label' => __('Topic')]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Topic');
    }
    // @codingStandardsIgnoreEnd
}
