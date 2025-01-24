<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CartAbandonmentEmail\Block\Adminhtml\System\Config\Element;

use Magento\Framework\View\Element\Html\Select as OriginalSelect;

class Select extends OriginalSelect
{
    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}