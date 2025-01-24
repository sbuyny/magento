<?php

namespace SergiiBuinii\Vip\Block\Adminhtml\System\Config\Renderer;

use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;
use Magento\Customer\Model\Customer\Attribute\Source\GroupSourceLoggedInOnlyInterface;

class CustomerGroupSelect extends Select
{
    /**
     * @var \Magento\Customer\Model\Customer\Attribute\Source\GroupSourceLoggedInOnlyInterface
     */
    protected $sources;

    /**
     * CustomerGroupSelect constructor
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Customer\Model\Customer\Attribute\Source\GroupSourceLoggedInOnlyInterface $sources
     * @param array $data
     */
    public function __construct(
        Context $context,
        GroupSourceLoggedInOnlyInterface $sources,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sources = $sources;
    }

    /**
     * Render block HTML
     *
     * @return string
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @codingStandardsIgnoreStart
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            $options = $this->sources->toOptionArray();
            $this->setOptions($options);
        }
        $this->setClass('cc-type-select');
        return parent::_toHtml();
    }

    /**
     * Set name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
