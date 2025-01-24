<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Model\ResourceModel\Subscription;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SergiiBuinii\Subscription\Model\Subscription;
use SergiiBuinii\Subscription\Model\ResourceModel\Subscription as SubscriptionResource;

class Collection extends AbstractCollection
{
    // @codingStandardsIgnoreStart
    /**
     * @var string
     */
    protected $_idFieldName = 'subscription_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'SergiiBuinii_subscription_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'SergiiBuinii_subscription_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Subscription::class, SubscriptionResource::class);
    }
    // @codingStandardsIgnoreEnd
}
