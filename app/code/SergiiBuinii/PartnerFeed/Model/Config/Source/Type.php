<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model\Config\Source;

use SergiiBuinii\PartnerFeed\Helper\Config;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Type
 */
class Type implements ArrayInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => Config::TYPE_PASSWORD, 'label' => 'Password'],
            ['value' => Config::TYPE_PRIVATE_KEY, 'label' => 'Private Key']
        ];
    }
}
