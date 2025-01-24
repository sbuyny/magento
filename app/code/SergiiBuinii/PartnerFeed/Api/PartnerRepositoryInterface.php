<?php

namespace SergiiBuinii\PartnerFeed\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface;

/**
 * Interface PartnerRepositoryInterface
 */
interface PartnerRepositoryInterface
{
    /**
     * Save Feed
     *
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @param \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface $partner
     */
    public function save(PartnerInterface $partner);

    /**
     * Retrieve Feed
     *
     * @param string $id
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * Retrieve Feed matching the specified criteria
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Feed
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     * @param \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface $partner
     */
    public function delete(PartnerInterface $partner);

    /**
     * Delete Feed by ID
     *
     * @param string $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($id);
}
