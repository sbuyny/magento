<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Subscription\Model\ResourceModel\Subscription\Grid;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use SergiiBuinii\Subscription\Model\ResourceModel\Subscription as SubscriptionResourceModel;
use SergiiBuinii\Subscription\Model\ResourceModel\Subscription\Collection as SubscriptionCollection;

class Collection extends SubscriptionCollection implements SearchResultInterface
{
    /**
     * @var \Magento\Framework\Api\Search\AggregationInterface $aggregations
     */
    protected $aggregations;

    // @codingStandardsIgnoreStart
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Document::class, SubscriptionResourceModel::class);
    }
    // @codingStandardsIgnoreEnd

    /**
     * @inheritdoc
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @inheritdoc
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * @inheritdoc
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * @inheritdoc
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * @inheritdoc
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setItems(array $items = null)
    {
        return $this;
    }
}
