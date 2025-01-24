<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CartAbandonmentEmail\Block\Adminhtml\System\Config\Element;

use Magento\Framework\View\Element\AbstractBlock;

class Checkbox extends AbstractBlock
{
    /**
     * Checkbox to html
     *
     * @inheritdoc
     * @codingStandardsIgnoreStart
     * @return string
     */
    protected function _toHtml()
    {
        //@codingStandardsIgnoreEnd
        $elId = $this->getInputId();
        $elName = $this->getInputName();
        $column = $this->getColumn();
        return '<input type="checkbox"  value="1" id="' . $elId . '"' .
            ' name="' . $elName . '"' .
            ' class="' .
            (isset($column['class']) ? $column['class'] : 'input-text') . '"' .
            (isset($column['style']) ? ' style="' . $column['style'] . '"' : '') . '/>';
    }
}