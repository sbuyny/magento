<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model;

use Magento\Framework\Api\SortOrder;
use SergiiBuinii\PartnerFeed\Model\FeedFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Model\StoreManagerInterface;
use SergiiBuinii\PartnerFeed\Api\Data\FeedInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use SergiiBuinii\PartnerFeed\Api\FeedRepositoryInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SergiiBuinii\PartnerFeed\Api\Data\FeedInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed as ResourceFeed;
use SergiiBuinii\PartnerFeed\Api\Data\FeedSearchResultsInterfaceFactory;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\CollectionFactory as FeedCollectionFactory;

/**
 * Class FeedRepository
 *
 * @SuppressWarnings("CouplingBetweenObjects")
 */
class FeedRepository implements FeedRepositoryInterface
{
    /**
     * @var \SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed
     */
    protected $resource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\FeedFactory
     */
    protected $feedFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \SergiiBuinii\PartnerFeed\Api\Data\FeedInterfaceFactory
     */
    protected $dataFeedFactory;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \SergiiBuinii\PartnerFeed\Api\Data\FeedSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\CollectionFactory
     */
    protected $feedCollectionFactory;

    /**
     * FeedRepository constructor.
     * @param \SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed $resource
     * @param \SergiiBuinii\PartnerFeed\Model\FeedFactory $feedFactory
     * @param \SergiiBuinii\PartnerFeed\Api\Data\FeedInterfaceFactory $dataFeedFactory
     * @param \SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\CollectionFactory $feedCollectionFactory
     * @param \SergiiBuinii\PartnerFeed\Api\Data\FeedSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceFeed $resource,
        FeedFactory $feedFactory,
        FeedInterfaceFactory $dataFeedFactory,
        FeedCollectionFactory $feedCollectionFactory,
        FeedSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->feedFactory = $feedFactory;
        $this->feedCollectionFactory = $feedCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFeedFactory = $dataFeedFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Feed item
     *
     * @param \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface $feed
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface
     * @throws CouldNotSaveException
     */
    public function save(FeedInterface $feed)
    {
        try {
            $feed->getResource()->save($feed);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the feed: %1',
                $exception->getMessage()
            ));
        }
        return $feed;
    }

    /**
     * Retrieve Feed item
     *
     * @return mixed
     * @param string $id
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $feed = $this->feedFactory->create();
        $feed->getResource()->load($feed, $id);
        if (!$feed->getId()) {
            throw new NoSuchEntityException(__('Feed with id "%1" does not exist.', $id));
        }
        return $feed;
    }

    /**
     * Retrieve Feed item by upc code
     *
     * @param string $upc
     * @return \SergiiBuinii\PartnerFeed\Model\Feed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByUpc($upc)
    {
        $feed = $this->feedFactory->create();
        $this->resource->load($feed, $upc, FeedInterface::UPC);
        if (!$feed->getId()) {
            throw new NoSuchEntityException(__('Feed with upc code "%1" does not exist.', $upc));
        }
        return $feed;
    }
    /**
     * Retrieve feed collection
     *
     * @return mixed
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->feedCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * Delete feed item
     *
     * @param \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface $feed
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(FeedInterface $feed)
    {
        try {
            $feed->getResource()->delete($feed);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Feed: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * Delete feed item by id
     *
     * @param string $id
     * @return bool
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
