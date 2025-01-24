<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Model;

use SergiiBuinii\Subscription\Api\SubscriptionRepositoryInterface;
use SergiiBuinii\Subscription\Api\Data;
use SergiiBuinii\Subscription\Model\ResourceModel\Subscription as ResourceModel;
use SergiiBuinii\Subscription\Model\ResourceModel\Subscription\CollectionFactory as SubscriptionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    /**
     * @var \SergiiBuinii\Subscription\Model\ResourceModel\Subscription
     */
    protected $resource;

    /**
     * @var \SergiiBuinii\Subscription\Model\SubscriptionFactory
     */
    protected $subscriptionFactory;

    /**
     * @var \SergiiBuinii\Subscription\Model\ResourceModel\Subscription\CollectionFactory
     */
    protected $subscriptionCollectionFactory;

    /**
     * @var \SergiiBuinii\Subscription\Api\Data\SubscriptionSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * SubscriptionRepository constructor
     *
     * @param \SergiiBuinii\Subscription\Model\ResourceModel\Subscription $resource
     * @param \SergiiBuinii\Subscription\Model\SubscriptionFactory $subscriptionFactory
     * @param \SergiiBuinii\Subscription\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollection
     * @param \SergiiBuinii\Subscription\Api\Data\SubscriptionSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        ResourceModel $resource,
        SubscriptionFactory $subscriptionFactory,
        SubscriptionCollectionFactory $subscriptionCollection,
        Data\SubscriptionSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->resource = $resource;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->subscriptionCollectionFactory = $subscriptionCollection;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * Save subscription data
     *
     * @param \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface $subscription
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(Data\SubscriptionInterface $subscription)
    {
        try {
            $this->resource->save($subscription);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $subscription;
    }

    /**
     * Load subscription data by given subscription identity
     *
     * @param int $subscriptionId
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($subscriptionId)
    {
        $subscription = $this->subscriptionFactory->create();
        $this->resource->load($subscription, $subscriptionId);
        if (!$subscription->getId()) {
            throw new NoSuchEntityException(__('Subscription with id "%1" does not exist.', $subscriptionId));
        }
        return $subscription;
    }

    /**
     * Load subscription data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \SergiiBuinii\Subscription\Api\Data\SubscriptionSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        /** @var \SergiiBuinii\Subscription\Model\ResourceModel\Subscription\Collection $collection */
        $collection = $this->subscriptionCollectionFactory->create();
        $this->extensionAttributesJoinProcessor->process($collection, Data\SubscriptionInterface::class);
        $this->collectionProcessor->process($criteria, $collection);

        /** @var \SergiiBuinii\Subscription\Api\Data\SubscriptionSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete subscription
     *
     * @param \SergiiBuinii\Subscription\Api\Data\SubscriptionInterface $subscription
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(Data\SubscriptionInterface $subscription)
    {
        try {
            $this->resource->delete($subscription);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete subscription by given subscription identity
     *
     * @param string $subscriptionId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($subscriptionId)
    {
        return $this->delete($this->getById($subscriptionId));
    }
}
