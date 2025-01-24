<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface SubscriptionRepositoryInterface
{
    /**
     * Save subscription
     *
     * @param \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface $subscription
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\SubscriptionInterface $subscription);

    /**
     * Retrieve subscription
     *
     * @param int $subscriptionId
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($subscriptionId);

    /**
     * Retrieve subscriptions matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete subscription
     *
     * @param \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface $subscription
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\SubscriptionInterface $subscription);

    /**
     * Delete subscription by ID.
     *
     * @param int $subscriptionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($subscriptionId);
}
