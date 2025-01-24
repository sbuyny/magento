<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner;

use SergiiBuinii\PartnerFeed\Model\Partner;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner as PartnerResource;
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
            Partner::class,
            PartnerResource::class
        );
    }
}
