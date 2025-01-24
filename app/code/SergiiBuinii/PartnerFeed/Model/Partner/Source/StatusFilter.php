<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model\Partner\Source;

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
