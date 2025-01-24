<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\Catalog\Plugin\Magento\Swatches\Block\Product\Renderer;

use SergiiBuinii\Catalog\Helper\Config;
use SergiiBuinii\Catalog\Helper\Data;
use SergiiBuinii\ExtendCustomer\Helper\Data as ExtendCustomerHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Pricing\Price;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\Format;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Swatches\Block\Product\Renderer\Configurable as OriginalClass;
use Magento\CatalogInventory\Api\StockStateInterface as StockManagerInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface as StockRegistry;

class Configurable
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
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    private $stockManagerInterface;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    private $configurable;
    
    /**
     * @var \Magento\Framework\Locale\Format
     */
    protected $localeFormat;
    
    /**
     * @var \SergiiBuinii\ExtendCustomer\Helper\Data
     */
    protected $extendCustomerHelper;
    
    /**
     * Configurable constructor.
     * @param \SergiiBuinii\Catalog\Helper\Config $config
     * @param \SergiiBuinii\Catalog\Helper\Data $helper
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\CatalogInventory\Api\StockStateInterface $stockManagerInterface
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     * @param \Magento\Framework\Locale\Format $localeFormat
     * @param \SergiiBuinii\ExtendCustomer\Helper\Data $extendCustomerHelper
     */
    public function __construct(
        Config $config,
        Data $helper,
        SerializerInterface $serializer,
        ProductRepositoryInterface $productRepository,
        StockManagerInterface $stockManagerInterface,
        StockRegistry $stockRegistry,
        ConfigurableType $configurable,
        Format $localeFormat,
        ExtendCustomerHelper $extendCustomerHelper
    ) {
        $this->productRepository = $productRepository;
        $this->config = $config;
        $this->helper = $helper;
        $this->serializer = $serializer;
        $this->stockManagerInterface = $stockManagerInterface;
        $this->stockRegistry = $stockRegistry;
        $this->configurable = $configurable;
        $this->localeFormat = $localeFormat;
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
                $childrenProductIds = $currentProduct->getTypeInstance()->getChildrenIds($currentProduct->getId());
                foreach (reset($childrenProductIds) as $productId) {
                    $product = $this->productRepository->getById($productId);
                    $isDiscontinued = $discontinuedAttr ? $product->getData($discontinuedAttr) : false;
                    $isOutOfStock = $this->getProductStockStatus($product);
                    $stockItem = $this->stockRegistry->getStockItem($productId, $product->getStore()->getWebsiteId());

                    $suggestData = [
                        'product_name'  => '',
                        'product_url'   => ''
                    ];
                    $suggestProductSku = $suggestionAttr ? $product->getData($suggestionAttr) : false;
                    if ($isDiscontinued && $suggestProductSku) {
                        try {
                            /** @var \Magento\Catalog\Model\Product $suggestProduct */
                            $suggestProduct = $this->productRepository->get($suggestProductSku);
                            $suggestData = [
                                'product_name'  => $suggestProduct->getName(),
                                'product_url'   => $currentProduct->getProductUrl()
                            ];
                        } catch (NoSuchEntityException $e) {
                            //do nothing
                        }
                    }
                    $attributesData = [];
                    $attributes = $currentProduct->getTypeInstance()->getConfigurableAttributes($currentProduct);
                    foreach ($attributes as $attribute) {
                        $attributesData[$attribute->getAttributeId()] = $product->getData(
                            $attribute->getProductAttribute()->getAttributeCode()
                        );
                    }
                    if ($isDiscontinued == 1){
                        $isDiscontinued = true;
                    }else{
                        $isDiscontinued = false;
                    }
                    $discontinuedProducts[$productId] = [
                        'is_discontinued_enabled'   => $isDiscontinued,
                        'attributes'                => $attributesData,
                        'suggest_data'              => $suggestData,
                        'is_out_of_stock'           => $isOutOfStock,
                        'stock_status'              => $stockItem->getIsInStock(),
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
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductStockStatus($product)
    {
        $stockState = $this->stockManagerInterface->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
        return $stockState > 0 ? '0' : '1';
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
