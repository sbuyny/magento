<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Catalog\Plugin\Magento\Swatches\Block\Product\Renderer;

use Magento\Catalog\Pricing\Price;
use SergiiBuinii\Catalog\Helper\Data;
use SergiiBuinii\Catalog\Helper\Config;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use SergiiBuinii\ExtendCustomer\Helper\Data as ExtendCustomerHelper;
use SergiiBuinii\Catalog\Model\Optimizer\Swatches as SwatchesOptimizer;
use Magento\Swatches\Block\Product\Renderer\Configurable as OriginalClass;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;

class ConfigurableOpt
{
    /**
     * @var \SergiiBuinii\Catalog\Helper\Config
     */
    private $config;

    /**
     * @var \SergiiBuinii\Catalog\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @var \SergiiBuinii\Catalog\Model\Optimizer\Swatches
     */
    protected $swatchesOptimizer;

    /**
     * @var \SergiiBuinii\ExtendCustomer\Helper\Data
     */
    protected $extendCustomerHelper;

    /**
     * Configurable constructor
     *
     * @param \SergiiBuinii\Catalog\Helper\Data $helper
     * @param \SergiiBuinii\Catalog\Helper\Config $config
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \SergiiBuinii\ExtendCustomer\Helper\Data $extendCustomerHelper
     * @param \SergiiBuinii\Catalog\Model\Optimizer\Swatches $swatchesOptimizer
     */
    public function __construct(
        Data $helper,
        Config $config,
        SerializerInterface $serializer,
        SwatchesOptimizer $swatchesOptimizer,
        ExtendCustomerHelper $extendCustomerHelper
    ) {
        $this->config = $config;
        $this->helper = $helper;
        $this->serializer = $serializer;
        $this->swatchesOptimizer = $swatchesOptimizer;
        $this->extendCustomerHelper = $extendCustomerHelper;
    }

    /**
     * Add discontinued config values to result
     *
     * @param \Magento\Swatches\Block\Product\Renderer\Configurable $subject
     * @param $result
     * @return bool|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function afterGetJsonConfig(OriginalClass $subject, $result)
    {
        $currentProduct = $subject->getProduct();
        $jsonConfig = $this->serializer->unserialize($result);
        $this->addAdditionalPrices($subject, $jsonConfig);
        $isEnabled = $this->config->isEnabledDiscontinued();
        $baseUrl = $subject->getBaseUrl();

        $discontinuedData = [
            'is_enabled'                => $isEnabled,
            'discontinued_text'         => $this->config->getDiscontinuedText(),
            'is_enabled_suggestions'    => $this->config->isEnabledDiscountedProductSuggestion(),
        ];

        $suggestionAttr = $this->config->getDiscontinuedProductSuggestionAttributeCode();
        $discontinuedAttr = $this->config->getDiscontinuedAttributeCode();
        if ($isEnabled && $currentProduct && $discontinuedAttr) {
            $discontinuedProducts = [];
            if ($currentProduct->getTypeId() == ConfigurableType::TYPE_CODE) {
                $storeId = $currentProduct->getStoreId();
                $productSearcAttr = [$discontinuedAttr];
                if ($suggestionAttr) {
                    $productSearcAttr[] = $suggestionAttr;
                }
                $childrenProductIds = array_keys($jsonConfig['index']);
                $stockItemsData = $this->swatchesOptimizer->getStockItemsData($childrenProductIds);

                foreach ($childrenProductIds as $productId) {
                    $suggestData = [
                        'product_name'  => '',
                        'product_url'   => ''
                    ];
                    $stockItemData = $stockItemsData[$productId];
                    $productDataArr = $this->swatchesOptimizer->getProductData($productId, $productSearcAttr, $storeId);
                    $isDiscontinued = isset($productDataArr[$discontinuedAttr]) && $productDataArr[$discontinuedAttr] == 1 ? true : false;

                    if ($isDiscontinued && isset($productDataArr[$suggestionAttr]) && $productDataArr[$suggestionAttr]) {
                        $suggestData = $this->swatchesOptimizer->getSuggestData(
                            $productDataArr[$suggestionAttr],
                            $storeId,
                            $baseUrl
                        );
                    }

                    $discontinuedProducts[$productId] = [
                        'is_discontinued_enabled'   => $isDiscontinued,
                        'attributes'                => $jsonConfig['index'][$productId],
                        'suggest_data'              => $suggestData,
                        'is_out_of_stock'           => $this->getProductStockStatus($stockItemData),
                        'stock_status'              => $stockItemData[StockItemInterface::IS_IN_STOCK] == 1 ? true : false,
                    ];
                }
                $discontinuedData['products'] = $discontinuedProducts;
            }
        }
        $jsonConfig = array_merge($jsonConfig, ['discontinued_data' => $discontinuedData]);
        $result = $this->serializer->serialize($jsonConfig);
        return $result;
    }

    /**
     * Get product stock status
     *
     * @param [] $stockItemData
     * @return string
     */
    public function getProductStockStatus($stockItemData)
    {
        $stockQty = (float) $stockItemData[StockItemInterface::QTY];

        if ($stockQty < 0 || !$stockItemData[StockItemInterface::MANAGE_STOCK]
            || !$stockItemData[StockItemInterface::IS_IN_STOCK]
        ) {
            $stockQty = (float) 0;
        }

        return $stockQty > 0 ? '0' : '1';
    }

    /**
     * Add additional prices
     *
     * @param \Magento\Swatches\Block\Product\Renderer\Configurable $subject
     * @param array $jsonConfig
     * @return $this
     */
    protected function addAdditionalPrices($subject, &$jsonConfig)
    {
        if ($subject->getProduct()->getTypeId() !== ConfigurableType::TYPE_CODE) {
            return $this;
        }
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($subject->getAllowProducts() as $product) {
            if (empty($jsonConfig['optionPrices'][$product->getId()])) {
                continue;
            }
            $this->extendPrice($jsonConfig['optionPrices'][$product->getId()], $product);
        }
        $this->extendPrice($jsonConfig['prices'], $subject->getProduct());
        return $this;
    }

    /**
     * Extend prices
     *
     * @param array $prices
     * @param \Magento\Catalog\Model\Product $product
     */
    protected function extendPrice(&$prices, $product)
    {
        $priceInfo = $product->getPriceInfo();
        /** @var \Magento\Catalog\Pricing\Price\RegularPrice $regularPriceModel */
        $regularPriceModel = $priceInfo->getPrice(Price\RegularPrice::PRICE_CODE);
        /** @var \Magento\Catalog\Pricing\Price\FinalPrice $finalPriceModel */
        $finalPriceModel = $priceInfo->getPrice(Price\FinalPrice::PRICE_CODE);
        $prices['isGroupPrice'] = [
            'amount' => $this->extendCustomerHelper->isGroupDiscount($regularPriceModel, $finalPriceModel) ? 1 : 0,
        ];
        $prices['extraDiscount'] = [
            'amount' => $this->extendCustomerHelper->getExtraDiscountPercentage($regularPriceModel, $finalPriceModel),
        ];
    }

    /**
     * After get prices json
     *
     * @param \Magento\Swatches\Block\Product\Renderer\Configurable $subject
     * @param string $result
     * @return bool|string
     */
    public function afterGetPricesJson(OriginalClass $subject, $result)
    {
        if ($subject->getProduct()->getTypeId() !== ConfigurableType::TYPE_CODE) {
            return $result;
        }
        $jsonConfig = $this->serializer->unserialize($result);
        $currentProduct = $subject->getProduct();
        $priceInfo = $currentProduct->getPriceInfo();
        /** @var \Magento\Catalog\Pricing\Price\FinalPrice $finalPriceModel */
        $finalPriceModel = $priceInfo->getPrice(Price\FinalPrice::PRICE_CODE);
        $jsonConfig['finalPrice']['amount'] = $this->helper->getMaxPriceAmount($finalPriceModel)->getValue();
        return $this->serializer->serialize($jsonConfig);
    }
}
