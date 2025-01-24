<?php

namespace SergiiBuinii\Catalog\Model\Optimizer;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Item as StockItemResource;

class Swatches
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;

    /**
     * @var \Magento\CatalogInventory\Model\ResourceModel\Stock\Item
     */
    protected $stockItemResource;

    /**
     * @var array $observableStockItemFields
     */
    protected $observableStockItemFields = [
        StockItemInterface::QTY,
        StockItemInterface::ITEM_ID,
        StockItemInterface::PRODUCT_ID,
        StockItemInterface::IS_IN_STOCK,
        StockItemInterface::MANAGE_STOCK,
    ];

    /**
     * Swatches optimizer constructor
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\CatalogInventory\Model\ResourceModel\Stock\Item $stockItemResource
     */
    public function __construct(
        ProductResource $productResource,
        StockItemResource $stockItemResource
    ) {
        $this->productResource = $productResource;
        $this->stockItemResource = $stockItemResource;
    }

    /**
     * Retrieve stock items data arr from DB
     *
     * @param array $productIds
     * @param bool $hasFormat
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getStockItemsData($productIds, $hasFormat = true)
    {
        $connection = $this->stockItemResource->getConnection();
        $field = $connection->quoteIdentifier(sprintf(
            '%s.%s',
            $this->stockItemResource->getMainTable(),
            StockItemInterface::PRODUCT_ID
        ));
        $sql= $connection->select()
            ->from(
                $this->stockItemResource->getMainTable(),
                $this->observableStockItemFields
            )->where($field . ' IN (?)', $productIds);
        $data = $connection->fetchAll($sql);
        return $hasFormat ? $this->formatStockItemData($data) : $data;
    }

    /**
     * Retrieve product name by SKU
     *
     * @param string $sku
     * @param int $storeId
     * @return string
     */
    public function getProductNameBySku($sku, $storeId)
    {
        $productName = $this->productResource->getAttributeRawValue(
            $this->productResource->getIdBySku($sku),
            'name',
            $storeId
        );

        return $productName ? $productName : '';
    }

    /**
     * Retrieve suggest product data by SKU
     *
     * @param string $sku
     * @param int $storeId
     * @param string $baseUrl
     * @return array
     */
    public function getSuggestData($sku, $storeId, $baseUrl)
    {
        $prodId = $this->productResource->getIdBySku($sku);
        $productData = [
            'name'      => $this->productResource->getAttributeRawValue($prodId, 'name', $storeId),
            'url_key'   => $this->productResource->getAttributeRawValue($prodId, 'url_key', $storeId),
        ];

        return [
            'product_name'  => $productData['name'] ? $productData['name'] : '',
            'product_url'   => $productData['url_key'] ? $baseUrl . $productData['url_key'] : '',
        ];
    }

    /**
     * Retrieve product data (attribute list)
     *
     * @param int $productId
     * @param array $attributeList
     * @param int $storeId
     * @return array
     */
    public function getProductData($productId, $attributeList, $storeId)
    {
        return $this->productResource->getAttributeRawValue($productId, $attributeList, $storeId);
    }

    /**
     * Format stock item data array
     * (uses product ID like keys)
     *
     * @param array $stockItemData
     * @return array [ProductId => [$stockItemData]]
     */
    protected function formatStockItemData($stockItemData)
    {
        $output = [];

        foreach ($stockItemData as $fetchData) {
            $output[$fetchData[StockItemInterface::PRODUCT_ID]] = $fetchData;
        }

        return $output;
    }
}
