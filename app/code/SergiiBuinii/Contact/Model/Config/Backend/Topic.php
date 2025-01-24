<?php

namespace SergiiBuinii\Contact\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized;

class Topic extends Serialized
{
    /**
     * Prepare data before save
     *
     * @return void
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        unset($value['__empty']);
        $this->setValue($value);
        parent::beforeSave();
    }
}
