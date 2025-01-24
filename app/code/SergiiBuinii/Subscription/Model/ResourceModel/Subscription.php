<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use SergiiBuinii\Subscription\Api\Data\SubscriptionInterface;

class Subscription extends AbstractDb
{
    /**
     * Subscription table
     */
    const DB_SCHEMA_TABLE_SUBSCRIPTION = 'SergiiBuinii_subscription';

    // @codingStandardsIgnoreStart
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::DB_SCHEMA_TABLE_SUBSCRIPTION, SubscriptionInterface::SUBSCRIPTION_ID);
    }
    // @codingStandardsIgnoreEnd
}
