<?php

namespace SergiiBuinii\Catalog\Model\LinkedProduct;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\BaseSelectProcessorInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Store\Model\StoreManagerInterface;

class HighestPriceOptionsProvider
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Key is product id and store id. Value is array of prepared linked products
     *
     * @var array
     */
    private $linkedProductMap;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    private $metadataPool;
    
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\BaseSelectProcessorInterface
     */
    private $baseSelectProcessor;

    
    /**
     * HighestPriceOptionsProvider constructor.
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Catalog\Model\ResourceModel\Product\BaseSelectProcessorInterface $baseSelectProcessor
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Session $customerSession,
        MetadataPool $metadataPool,
        BaseSelectProcessorInterface $baseSelectProcessor
    ) {
        $this->resource = $resourceConnection;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->metadataPool = $metadataPool;
        $this->baseSelectProcessor = $baseSelectProcessor;
    }
    
    /**
     * Get products
     *
     * @param ProductInterface $product
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function getProducts(ProductInterface $product)
    {
        $key = $this->storeManager->getStore()->getId() . '-' . $product->getId();
        if (!isset($this->linkedProductMap[$key])) {
            $productIds = $this->resource->getConnection()->fetchCol(
                $this->buildSelect($product->getId())
            );

            $this->linkedProductMap[$key] = $this->collectionFactory->create()
                ->addAttributeToSelect(
                    ['price', 'special_price', 'special_from_date', 'special_to_date', 'tax_class_id']
                )
                ->addIdFilter($productIds)
                ->getItems();
        }
        return $this->linkedProductMap[$key];
    }
    
    /**
     * Select builder
     *
     * Query was inherited from
     * @param int $productId
     * @return mixed
     */
    private function buildSelect($productId)
    {
        $linkField = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
        $productTable = $this->resource->getTableName('catalog_product_entity');
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customerGroupId = $this->customerSession->getCustomerGroupId();
    
        $priceSelect = $this->resource->getConnection()->select()
            ->from(['parent' => $productTable], '')
            ->joinInner(
                ['link' => $this->resource->getTableName('catalog_product_relation')],
                "link.parent_id = parent.$linkField",
                []
            )->joinInner(
                [BaseSelectProcessorInterface::PRODUCT_TABLE_ALIAS => $productTable],
                sprintf('%s.entity_id = link.child_id', BaseSelectProcessorInterface::PRODUCT_TABLE_ALIAS),
                ['entity_id']
            )->joinInner(
                [
                    't' => $this->resource->getTableName('catalog_product_index_price')
                ],
                sprintf('t.entity_id = %s.entity_id', BaseSelectProcessorInterface::PRODUCT_TABLE_ALIAS),
                []
            )->where('parent.entity_id = ?', $productId)
            ->where('t.website_id = ?', $websiteId)
            ->where('t.customer_group_id = ?', $customerGroupId)
            ->order('t.min_price ' . Select::SQL_DESC)
            ->order(BaseSelectProcessorInterface::PRODUCT_TABLE_ALIAS . '.' . $linkField . ' ' . Select::SQL_ASC)
            ->limit(1);
        return $this->baseSelectProcessor->process($priceSelect);
    }
    
}
