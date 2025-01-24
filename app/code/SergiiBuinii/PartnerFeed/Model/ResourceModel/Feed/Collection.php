<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed;

use SergiiBuinii\PartnerFeed\Model\Feed;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed as FeedResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 */
class Collection extends AbstractCollection
{
    /**
     * Main table primary key field name
     *
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            Feed::class,
            FeedResource::class
        );
    }
}
