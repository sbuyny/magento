<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model\Partner\Source;

use SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Partner
 */
class Partner implements OptionSourceInterface
{
    /**
     * @var \SergiiBuinii\PartnerFeed\Model\Feed
     */
    protected $collection;

    /**
     * Partner constructor.
     * @param \SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner\CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collection = $collectionFactory->create();
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->collection->toOptionArray();
    }
}
