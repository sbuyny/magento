<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\CartAbandonmentEmail\Plugin\Cron;

use Magento\Store\Model\StoresConfig;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;
use SergiiBuinii\ListrakBase\Helper\Data as ListrakBaseHelper;
use SergiiBuinii\ListrakBase\Api\ListrakBaseInterface;
use SergiiBuinii\CartAbandonmentEmail\Helper\Data;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Listrak\Remarketing\Helper\Product as ProductHelper;
use \Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Catalog\Helper\Product as ProductImage;

/**
 * Class CleanExpiredQuotes
 */
class CleanExpiredQuotesPlugin
{
    const LIFETIME = 86400;
    const LISTRAK_BASKET_ID_PATH = 'remarketing/cart/reload/ltskid/';
    const LISTRAK_REDIRECT_PATH = '?redirectUrl=checkout/cart';

    /**
     * @var StoresConfig
     */
    protected $storesConfig;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $quoteCollectionFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SergiiBuinii\CartAbandonmentEmail\Helper\Data
     */
    protected $cartAbandonHelper;

    /**
     * @var \SergiiBuinii\ListrakBase\Helper\Data
     */
    protected $listrakHelper;

    /**
     * @var \SergiiBuinii\ListrakBase\Api\ListrakBaseInterface
     */
    protected $listrakBase;

    /**
     * @var \Listrak\Remarketing\Helper\Cart
     */
    protected $cartHelper;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var \Listrak\Remarketing\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $image;

    /**
     * CleanExpiredQuotesPlugin constructor.
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
     * @param \SergiiBuinii\ListrakBase\Api\ListrakBaseInterface $listrakBase
     * @param \SergiiBuinii\ListrakBase\Helper\Data $listrakHelper
     * @param \SergiiBuinii\CartAbandonmentEmail\Helper\Data $cartAbandonHelper
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Listrak\Remarketing\Helper\Product $cartProductHelper
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Catalog\Helper\Product $image
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ListrakBaseHelper $listrakHelper,
        ListrakBaseInterface $listrakBase,
        Data $cartAbandonHelper,
        PriceHelper $priceHelper,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        ProductHelper $cartProductHelper,
        EncryptorInterface $encryptor,
        ProductImage $image
    ) {
        $this->cartAbandonHelper = $cartAbandonHelper;
        $this->listrakHelper = $listrakHelper;
        $this->quoteCollectionFactory = $collectionFactory;
        $this->listrakBase = $listrakBase;
        $this->listrakHelper = $listrakHelper;
        $this->cartAbandonHelper = $cartAbandonHelper;
        $this->priceHelper = $priceHelper;
        $this->productHelper = $cartProductHelper;
        $this->storeManager = $storeManager;
        $this->encryptor = $encryptor;
        $this->productRepository = $productRepository;
        $this->image = $image;
    }

    /**
     * After Plugin for CleanExpiredQuotes to include for Abandonment Cart
     *
     * @return void
     */
    public function afterExecute()
    {
        /* Quotes of abandoned Carts */
        $abandonLifetimes = $this->cartAbandonHelper->getAbandonmentCronLifetime();
        $abandonmentDaySchedule = explode(',', $this->cartAbandonHelper->getScheduledDays());
        $dateCurrent = date("Y-m-d", time());

        foreach ($abandonLifetimes as $storeId=>$abandonLifetime) {
            $abandonLifetime *= self::LIFETIME;

            $quotes = $this->quoteCollectionFactory->create();
            /* @var $quotes \Magento\Quote\Model\ResourceModel\Quote\Collection */

            $quotes->addFieldToFilter('store_id', $storeId);
            $quotes->addFieldToFilter(
                'updated_at',
                [
                    'to' => date("Y-m-d", time() - $abandonLifetime)
                ]
            );
            $quotes->addFieldToFilter('is_active', 1); // Active quotes only
            $quotes->addFieldToFilter('customer_email', ['notnull' => true]); // Email should not be empty
            if ($this->cartAbandonHelper->isAbandonmentFilterAllowed() == true) {
                $abandonFilters = $this->cartAbandonHelper->getAbandonmentFilters();
                foreach ($abandonFilters as $filter) {
                    $quotes->addFieldToFilter($filter['filter_name'],
                        [
                            $filter['filter_option'] => $filter['filter_value']
                        ]);
                }
            }
            $abandonCartTemplateId = $this->cartAbandonHelper->getTemplateIdAbandonedCart();
            if ($abandonCartTemplateId && $quotes->getSize() > 0) {
                $url = $this->listrakHelper->getTransactionalMessagegUrlFormat($abandonCartTemplateId);
                foreach ($quotes as $quoteData) {
                    $dateQuote = explode(' ', $quoteData->getUpdatedAt());
                    $abandonDays = (strtotime($dateCurrent)-strtotime($dateQuote[0]))/self::LIFETIME;

                    // If day is not on scheduled, abandonment email will not be sent
                    if (in_array((string)$abandonDays, $abandonmentDaySchedule) == false) {
                        continue;
                    }
                    $customerEmail = $quoteData->getCustomerEmail();
                    $customerGroupId = $quoteData->getCustomerGroupId();
                    $abandonData = [];
                    $mediaPath = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                        . 'catalog/product';
                    $basketId = $this->getBasketId($quoteData->getId());
                    $basketIdPath = $this->storeManager->getStore()->getBaseUrl() .
                        self::LISTRAK_BASKET_ID_PATH .
                        htmlentities($basketId) .
                        self::LISTRAK_REDIRECT_PATH;
                    $cartItems = $this->pullDataFromCart($quoteData);
                    foreach ($cartItems as $quoteItemDatum) {
                        $quoteItemSku = $quoteItemDatum->getSku();
                        $productData = $this->productRepository->get(
                            $quoteItemSku
                        );
                        $abandonData[$quoteItemSku]['sku'] = $quoteItemDatum->getSku();
                        $abandonData[$quoteItemSku]['base_price'] = $this->priceHelper
                            ->currency($productData->getPrice(), true, false);
                        if (in_array($customerGroupId, $this->cartAbandonHelper->getProGroupIds())) {
                            $tierPrice = $productData->getTierPrices();

                            if (isset($tierPrice) && !empty($tierPrice)) {
                                foreach ($tierPrice as $tp) {
                                    if ($customerGroupId == $tp->getCustomerGroupId()) {
                                        $abandonData[$quoteItemSku]['pro_price'] = $this->priceHelper
                                            ->currency($tp->getValue(), true, false);
                                    }
                                }
                            }
                        }
                        $image_url = $this->image->getThumbnailUrl($productData);

                        $abandonData[$quoteItemSku]['name'] = $quoteItemDatum->getName();
                        $abandonData[$quoteItemSku]['qty'] = $quoteItemDatum->getQty();
                        $abandonData[$quoteItemSku]['thumbnail'] = isset($image_url) && !empty($image_url) ? $image_url :
                            $mediaPath . $productData->getThumbnail();
                        $abandonData[$quoteItemSku]['product_url'] = $productData->getProductUrl();
                    }
                    $abandonEmailBasketId = $this->cartAbandonHelper->getSegmentationIdAbandonBasketId();
                    $segmentationIdAbandonHtml = $this->cartAbandonHelper->getSegmentationIdAbandonHtml();

                    $abandonEmailHtml = $this->cartAbandonHelper->generateHtmlAbandonItemsList($abandonData,$basketIdPath);
                    if ($basketIdPath && $abandonEmailHtml) {
                        // Listrak Segmentation Field Values
                        $data = [
                            "EmailAddress" => $customerEmail,
                            "segmentationFieldValues" => [
                                [
                                    "segmentationFieldId" => $segmentationIdAbandonHtml,
                                    "value" => $abandonEmailHtml
                                ],
                                [
                                    "segmentationFieldId" => $abandonEmailBasketId,
                                    "value" => $basketIdPath
                                ]
                            ]
                        ];
                        // Send Email To Listrak on Abandon Cart Transactional Message
                        $this->listrakBase->sendToListrak($url, 'POST', $data);
                    }
                }
            }
        }
    }

    /**
     * Retrieve Cart Items Url
     *
     * @param $quoteId
     * @return string
     */
    public function getBasketId($quoteId)
    {
        $storeId = $this->storeManager->getStore()->getId();

        $str = $storeId . ' ' . $quoteId;
        while (strlen($str) < 16) {
            // 5 for store ID, 1 for the space, and 10 for the quote ID
            $str .= ' ' . $quoteId;
        }
        $str = substr($str, 0, 16);

        $encrypted = $this->encryptor->encrypt($str);
        return rawurlencode(str_replace('/', '_', $encrypted));
    }

    /**
     * Retrieve all items associated to quote
     *
     * @param \Magento\Quote\Model\Quote $quoteData
     * @return array
     */
    private function pullDataFromCart($quoteData)
    {
        $cartItems = $quoteData->getAllVisibleItems();

        $productIds = [];
        foreach ($cartItems as $item) {
            if ($item && $item->hasProductId()) {
                $productIds[] = $item->getProductId();
            }
        }

        $products = $this->productHelper->load(
            $productIds,
            [ 'name', 'thumbnail', 'product_url']
        );

        $result = [];
        foreach ($products as $product) {
            foreach ($cartItems as $item) {
                if ($item->hasProductId() && $item->getProductId() == $product->getEntityId()) {
                    $cartProduct = clone $product;

                    /* The following line of code clears the type ID so that a
                     * bundle product plugin wouldn't result in the getPrice()
                     * returning a zero.
                     */
                    $cartProductType = $cartProduct->getTypeId();
                    $cartProduct->unsTypeId();
                    if ($cartProduct->getSku() != $item->getSku()
                        && $cartProductType === ConfigurableType::TYPE_CODE
                    ) {
                        $cartProduct->setSku($item->getSku());
                    }
                    $cartProduct->setPrice($item->getPrice());
                    $cartProduct->setQty($item->getQty());

                    $result[] = $cartProduct;
                }
            }
        }

        return $result;
    }
}
