<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Api\Data;

interface FeedSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Feed list
     *
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface[]
     */
    public function getItems();

    /**
     * Set Feed list
     *
     * @param \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
