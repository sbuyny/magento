<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CartAbandonmentEmail\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\DataObject;
use SergiiBuinii\CartAbandonmentEmail\Block\Adminhtml\System\Config\Element\Checkbox;

class Abandonment extends AbstractFieldArray
{
    /**
     * System Configuration field name constants
     */
    const SYSTEM_ELEMENT_OPTION_FIELD  = 'abandonment_option';
    const SYSTEM_ELEMENT_TITLE_FIELD   = 'abandonment_fieldname';
    const SYSTEM_ELEMENT_VALUE_FIELD   = 'abandonment_fieldvalue';
    const SYSTEM_ELEMENT_ENABLE_FIELD = 'abandonment_enabled';

    /**
     * @var \SergiiBuinii\CartAbandonmentEmail\Block\Adminhtml\System\Config\Element\Select
     */
    protected $abandonmentRenderer;

    /**
     * @var \SergiiBuinii\CartAbandonmentEmail\Block\Adminhtml\System\Config\Element\Checkbox
     */
    protected $checkboxRenderer;

    /**
     * Attribute constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \SergiiBuinii\CartAbandonmentEmail\Block\Adminhtml\System\Config\Element\Checkbox $checkboxRender
     * @param array $data
     */
    public function __construct(
        Context $context,
        Checkbox $checkboxRender,
        array $data = []
    ) {
        $this->checkboxRenderer = $checkboxRender;
        parent::__construct($context, $data);
    }

    /**
     * Return the filter options
     *
     * @return \SergiiBuinii\CartAbandonmentEmail\Block\Adminhtml\System\Config\Element\Select
     */
    public function getAbandonmentFilterField()
    {

        if (!$this->abandonmentRenderer) {

            $filterData = [
                [
                    'label' => '=',
                    'value' => 'eq'
                ],
                [
                    'label' => '>',
                    'value' => 'gt'
                ],
                [
                    'label' => '≥',
                    'value' => 'gteq'
                ],
                [
                    'label' => '≤',
                    'value' => 'lteq'
                ],
                [
                    'label' => '≠',
                    'value' => 'neq'
                ],
                [
                    'label' => 'like',
                    'value' => 'like'
                ],
                [
                    'label' => 'not like',
                    'value' => 'nlike'
                ],
                [
                    'label' => 'is null',
                    'value' => 'null'
                ],
                [
                    'label' => 'is not null',
                    'value' => 'not null'
                ],
                [
                    'label' => 'in',
                    'value' => 'in'
                ],
                [
                    'label' => 'not in',
                    'value' => 'nin'
                ],
                [
                    'label' => 'finset',
                    'value' => 'finset'
                ]
            ];
            $this->abandonmentRenderer = $this->getLayout()->createBlock(
                Element\Select::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            )->setOptions(
                $filterData
            );
        }
        return $this->abandonmentRenderer;
    }

    /**
     * Prepare to Render
     *
     * {@inheritdoc}
     * @return void
     * @codingStandardsIgnoreStart
     */
    protected function _prepareToRender()
    {
        //@codingStandardsIgnoreEnd
        $this->addColumn(
            self::SYSTEM_ELEMENT_TITLE_FIELD,
            [
                'label'     => __('Filter Name'),
                'renderer' => false
            ]
        );
        $this->addColumn(
            self::SYSTEM_ELEMENT_OPTION_FIELD,
            [
                'label'     => __('Filter Condition'),
                'renderer'  => $this->getAbandonmentFilterField()
            ]
        );
        $this->addColumn(
            self::SYSTEM_ELEMENT_VALUE_FIELD,
            [
                'label'     => __('Filter Value'),
                'renderer' => false
            ]
        );
        $this->addColumn(
            self::SYSTEM_ELEMENT_ENABLE_FIELD,
            [
                'label' => __('Enabled'),
                'renderer' => $this->checkboxRenderer,
                'class' => 'checkbox'
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Filter');
    }

    /**
     * Prepare existing row data object
     *
     * @param \Magento\Framework\DataObject $row
     * @return void
     * @codingStandardsIgnoreStart
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        //@codingStandardsIgnoreEnd
        $subdivision = $row->getData(self::SYSTEM_ELEMENT_OPTION_FIELD);
        $options = [];
        if ($subdivision) {
            $options['option_' . $this->getAbandonmentFilterField()->calcOptionHash($subdivision)]
                = 'selected="selected"';
        }
        $row->setData('option_extra_attrs', $options);
    }
}