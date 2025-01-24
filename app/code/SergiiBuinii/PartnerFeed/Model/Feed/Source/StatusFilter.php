<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model\Feed\Source;

/**
 * Class StatusFilter
 */
class StatusFilter extends Status
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return array_merge([['label' => '', 'value' => '']], parent::toOptionArray());
    }
}
