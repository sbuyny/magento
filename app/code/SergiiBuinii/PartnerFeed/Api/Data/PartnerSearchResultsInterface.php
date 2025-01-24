<?php

namespace SergiiBuinii\PartnerFeed\Api\Data;

interface PartnerSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get Feed list
     *
     * @return \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface[]
     */
    public function getItems();

    /**
     * Set Feed list
     *
     * @param \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
