<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Frequency
 */
class Frequency implements ArrayInterface
{
    /**
     * Return Option Array
     *
     * @return array|null
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('1 min')],
            ['value' => '2', 'label' => __('2 min')],
            ['value' => '5', 'label' => __('5 min')],
            ['value' => '10', 'label' => __('10 min')],
            ['value' => '20', 'label' => __('20 min')],
            ['value' => '30', 'label' => __('30 min')],
            ['value' => 'hour', 'label' => __('Hour')]
        ];
    }
}
