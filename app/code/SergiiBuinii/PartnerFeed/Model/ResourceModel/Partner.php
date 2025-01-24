<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model\ResourceModel;

use SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Partner
 */
class Partner extends AbstractDb
{
    /**
     * Table name for Feed entity
     *
     * @type string
     */
    const DB_SCHEMA_MAIN_TABLE = 'ba_partner_entity';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::DB_SCHEMA_MAIN_TABLE, PartnerInterface::ENTITY_ID);
    }
}
