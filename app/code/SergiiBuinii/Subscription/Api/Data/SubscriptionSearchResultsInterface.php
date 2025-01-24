<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface SubscriptionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get subscriptions list.
     *
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface[]
     */
    public function getItems();

    /**
     * Set subscriptions list.
     *
     * @param \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
