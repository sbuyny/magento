<?php

namespace SergiiBuinii\Catalog\Helper;

use SergiiBuinii\Catalog\Helper\Config;
use SergiiBuinii\Catalog\Model\LinkedProduct\HighestPriceOptionsProvider;
use Magento\Catalog\Model\Config\Source\Product\Thumbnail;
use Magento\Catalog\Model\ProductRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Directory\Model\Currency;
use Magento\Framework\Pricing\Adjustment\CalculatorInterface;
use Magento\Framework\Pricing\Price\AbstractPrice;

/**
 * Class Data
 */
class Data extends AbstractHelper
{
    /**
     * @const string Attribute code for badge
     */
    const ATTRIBUTE_CODE_BADGE = 'or_badge';
    
    /**
     * @const string product attribute or_product_weight_standard
     */
    const ATTRIBUTE_CODE_WEIGHT_STANDARD = 'or_product_weight_standard';
    
    /**
     * @const string product attribute or_product_weight_metric
     */
    const ATTRIBUTE_CODE_WEIGHT_METRIC = 'or_product_weight_metric';
    
    /**
     * @const string product attribute or_avg_size
     */
    const ATTRIBUTE_CODE_AVG_SIZE = 'or_avg_size';
    
    /**
     * @const string product attribute or_avg_weight_metric
     */
    const ATTRIBUTE_CODE_AVG_WEIGHT_METRIC = 'or_avg_weight_metric';
    
    /**
     * @const string product attribute or_avg_weight_standard
     */
    const ATTRIBUTE_CODE_AVG_WEIGHT_STANDARD = 'or_avg_weight_standard';
    
    /**
     *  @const array avg product attribute codes
     */
    const ATTRIBUTE_CODES_WEIGHT_SET = [
        self::ATTRIBUTE_CODE_AVG_SIZE,
        self::ATTRIBUTE_CODE_AVG_WEIGHT_METRIC,
        self::ATTRIBUTE_CODE_AVG_WEIGHT_STANDARD,
    ];
    
    /**
     * Product weight format
     */
    const PRODUCT_WEIGHT_FORMAT = '%s/%s %s';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $productResource;
    
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;
    
    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $currency;
    
    /**
     * @var \Magento\Framework\Pricing\Adjustment\CalculatorInterface
     */
    protected $calculator;
    
    /**
     * @var \SergiiBuinii\Catalog\Model\LinkedProduct\HighestPriceOptionsProvider
     */
    protected $highestPriceOptionsProvider;
    
    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magento\Framework\Pricing\Adjustment\CalculatorInterface $calculator
     * @param \SergiiBuinii\Catalog\Model\LinkedProduct\HighestPriceOptionsProvider $highestPriceOptionsProvider
     */
    public function __construct(
        Context $context,
        Product $productResource,
        ProductRepository $productRepository,
        Currency $currency,
        CalculatorInterface $calculator,
        HighestPriceOptionsProvider $highestPriceOptionsProvider
    ) {
        parent::__construct($context);
        $this->productResource = $productResource;
        $this->productRepository = $productRepository;
        $this->currency = $currency;
        $this->calculator = $calculator;
        $this->highestPriceOptionsProvider = $highestPriceOptionsProvider;
    }

    /**
     * Get Product Badges
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array
     */
    public function getProductBadgeHtml($product)
    {
        $html = [];
        $attribute = $this->productResource->getAttribute(self::ATTRIBUTE_CODE_BADGE);
        if ($attribute) {
            $badges = $attribute->getFrontend()->getValue($product);
            if (is_array($badges)) {
                foreach ($badges as $badge) {
                    $text = $badge['text'];
                    $class = str_replace(' ', '-', trim(strtolower($text)));
                    $html[] = "<span class='$class'>$text</span>";
                }
            }
        }
        return $html;
    }

    /**
     * Get Product Badge Image
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array
     */
    public function getBadgeImage($product)
    {
        $html = [];
        $attribute = $this->productResource->getAttribute(self::ATTRIBUTE_CODE_BADGE);
        if ($attribute) {
            $badges = $attribute->getFrontend()->getValue($product);

            if (is_array($badges)) {
                foreach ($badges as $badge) {
                    if ($badge['image']) {
                        $html[] = '<img src="' . $badge['image'] . '" alt="' . $badge['text'] . '"/>';
                    }
                }
            }
        }
        return $html;
    }

    /**
     * Calculate Discount percentage
     *
     * @param \Magento\Framework\Pricing\Price\PriceInterface|float $regularPrice
     * @param \Magento\Framework\Pricing\Price\PriceInterface|float $finalPrice
     * @return \Magento\Framework\Phrase|string
     */
    public function getDiscountPercentage($regularPrice, $finalPrice)
    {
        if ($regularPrice instanceof AbstractPrice) {
            $regularPrice = $regularPrice->getAmount()->getValue();
        }
        if ($finalPrice instanceof AbstractPrice) {
            $finalPrice = $finalPrice->getAmount()->getValue();
        }
        if ($finalPrice < $regularPrice) {
            $percentage = (($regularPrice - $finalPrice)/$regularPrice)*100;
            $percentage = round((float)$percentage);
            return __('(Save %1%)', $percentage);
        }
        return '';
    }
    
    /**
     * Calculate Extra percentage
     *
     * @param \Magento\Catalog\Pricing\Price\FinalPrice $finalPriceModel
     * @param \Magento\Catalog\Pricing\Price\TierPrice $tierPriceModel
     * @return \Magento\Framework\Phrase|string
     */
    public function getExtraPercentage($finalPriceModel, $tierPriceModel)
    {
        $finalPrice = $finalPriceModel->getAmount()->getValue();
        $tierPrice = $tierPriceModel->getAmount()->getValue();
        if (!$finalPrice && !$tierPrice) {
            return '';
        }
        if ($finalPrice < $tierPrice) {
            $percentage = (($tierPrice - $finalPrice)/$tierPrice)*100;
            return __('Extra %1%', round((float)$percentage));
        }
        return '';
    }
    
    /**
     * Get composite product weight
     *
     * - or_product_weight_standard / or_product_weight_metric
     * - "/" should be the separator for or_product_weight_standard and or_product_weight_metric the
     * - or_avg_size, or_avg_weight_metric, or_avg_weight_standard(optional), only with attributes that have value
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getCompositeProductWeight($product)
    {
        if (!$product->getData(self::ATTRIBUTE_CODE_WEIGHT_STANDARD)
            || !$product->getData(self::ATTRIBUTE_CODE_WEIGHT_METRIC)) {
            return '';
        }
        $avgValue = '';
        foreach (self::ATTRIBUTE_CODES_WEIGHT_SET as $code) {
            $avgValue = $product->getData($code);
            if ($avgValue) {
                break;
            }
        }
        
        return sprintf(
            self::PRODUCT_WEIGHT_FORMAT,
            $product->getData(self::ATTRIBUTE_CODE_WEIGHT_STANDARD),
            $product->getData(self::ATTRIBUTE_CODE_WEIGHT_METRIC),
            $avgValue
        );
    }
    
    /**
     * Get product for thumbnail
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductForThumbnail($item)
    {
        if ($this->scopeConfig->getValue(Config::XML_PATH_CONFIGURABLE_PRODUCT_IMAGE) == Thumbnail::OPTION_USE_OWN_IMAGE
            && $item->getProduct() && $item->getProduct()->getTypeId() === Configurable::TYPE_CODE) {
            try {
                return $this->productRepository->get($item->getProductOptionByCode('simple_sku'));
            } catch (\Exception $e) {
                // do nothing
            }
        }
        return $item->getProduct();
    }
    
    /**
     * Create Amount
     *
     * @param float $value
     * @return string
     */
    public function renderAmount($value)
    {
        return $this->currency->formatTxt($value);
    }
    
    /**
     * Get max price amount
     *
     * @param \Magento\Catalog\Pricing\Price\FinalPrice $finalPrice
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getMaxPriceAmount($finalPrice)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $finalPrice->getProduct();
        $maxPrice = $product->getData('max_price')
            ? $product->getData('max_price')
            : $finalPrice->getMaximalPrice()->getValue();
        return  $this->calculator->getAmount($maxPrice, $finalPrice->getProduct());
    }
    
    /**
     * Get highest option price amount
     *
     * @param \Magento\Catalog\Pricing\Price\FinalPrice $finalPrice
     * @return \Magento\Framework\Pricing\Amount\AmountInterface
     */
    public function getHighestOptionPriceAmount($finalPrice)
    {
        $products = $this->highestPriceOptionsProvider->getProducts($finalPrice->getProduct());
        $price = null;
        foreach ($products as $subProduct) {
            $price = isset($price) ? min($price, $subProduct->getFinalPrice()) : $subProduct->getFinalPrice();
        }
        return  $this->calculator->getAmount((float)$price, $finalPrice->getProduct());
    }
}
