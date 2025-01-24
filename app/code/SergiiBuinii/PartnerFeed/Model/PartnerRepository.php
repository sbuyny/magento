<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model;

use Magento\Framework\Api\SortOrder;
use SergiiBuinii\PartnerFeed\Model\PartnerFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Model\StoreManagerInterface;
use SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use SergiiBuinii\PartnerFeed\Api\PartnerRepositoryInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use SergiiBuinii\PartnerFeed\Api\Data\PartnerInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner as Resource;
use SergiiBuinii\PartnerFeed\Api\Data\PartnerSearchResultsInterfaceFactory;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner\CollectionFactory as PartnerCollectionFactory;

/**
 * Class PartnerRepository
 *
 * @SuppressWarnings("CouplingBetweenObjects")
 */
class PartnerRepository implements PartnerRepositoryInterface
{
    /**
     * @var \SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner
     */
    protected $resource;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\PartnerFactory
     */
    protected $partnerFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterfaceFactory
     */
    protected $dataFeedFactory;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \SergiiBuinii\PartnerFeed\Api\Data\PartnerSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * PartnerRepository constructor.
     * @param Resource $resource
     * @param \SergiiBuinii\PartnerFeed\Model\PartnerFactory $partnerFactory
     * @param \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterfaceFactory $dataPartnerFactory
     * @param \SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner\CollectionFactory $collectionFactory
     * @param \SergiiBuinii\PartnerFeed\Api\Data\PartnerSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Resource $resource,
        PartnerFactory $partnerFactory,
        PartnerInterfaceFactory $dataPartnerFactory,
        PartnerCollectionFactory $collectionFactory,
        PartnerSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->partnerFactory = $partnerFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFeedFactory = $dataPartnerFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Partner item
     *
     * @param \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface $model
     * @return \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface
     * @throws CouldNotSaveException
     */
    public function save(PartnerInterface $model)
    {
        try {
            $model->getResource()->save($model);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the Partner: %1',
                $exception->getMessage()
            ));
        }
        return $model;
    }

    /**
     * Retrieve Partner item
     *
     * @return mixed
     * @param string $id
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        $model = $this->partnerFactory->create();
        $model->getResource()->load($model, $id);
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('Partner with id "%1" does not exist.', $id));
        }
        return $model;
    }

    /**
     * Retrieve Partner collection
     *
     * @return mixed
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->collectionFactory->create();
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
     * Delete Partner item
     *
     * @param \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface $model
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(PartnerInterface $model)
    {
        try {
            $model->getResource()->delete($model);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Partner: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * Delete Partner item by id
     *
     * @param string $id
     * @return bool
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
