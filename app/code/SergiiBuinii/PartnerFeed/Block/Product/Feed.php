<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Block\Product;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use SergiiBuinii\PartnerFeed\Api\Data\FeedInterface;
use SergiiBuinii\PartnerFeed\Model\FeedRepository;
use SergiiBuinii\PartnerFeed\Model\Feed as FeedModel;
use SergiiBuinii\PartnerFeed\Model\PartnerRepository;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\CollectionFactory;
use SergiiBuinii\PartnerFeed\Helper\Config as ConfigHelper;
use \Magento\Framework\Registry;

/**
 * Class Feed
 */
class Feed extends Template
{
    /**
     * @var string
     */
    protected $_template = "SergiiBuinii_PartnerFeed::product/feed.phtml";

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\FeedRepository
     */
    protected $feedRepository;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\PartnerRepository
     */
    protected $partnerRepository;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\CollectionFactory
     */
    protected $feedCollectionFactory;

    /**
     * @var \SergiiBuinii\PartnerFeed\Helper\Config
     */
    protected $configHelper;

    /**
     * Feed constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \SergiiBuinii\PartnerFeed\Model\FeedRepository $feedRepository
     * @param \SergiiBuinii\PartnerFeed\Model\PartnerRepository $partnerRepository
     * @param \SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\CollectionFactory $collectionFactory
     * @param \SergiiBuinii\PartnerFeed\Helper\Config $configHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FeedRepository $feedRepository,
        PartnerRepository $partnerRepository,
        CollectionFactory $collectionFactory,
        ConfigHelper $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->feedRepository = $feedRepository;
        $this->partnerRepository = $partnerRepository;
        $this->feedCollectionFactory = $collectionFactory;
        $this->configHelper = $configHelper;
    }

    /**
     * Get Partner Feeds
     *
     * @return \SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed\Collection | array
     */
    public function getPartnerFeeds()
    {
        $product = $this->getProduct();
        if ($product) {
            $upcCode = $product->getUpcCode();
            if (!$upcCode) {
                return [];
            }

            $collection = $this->feedCollectionFactory->create()
                ->addFieldToFilter(FeedInterface::UPC, $upcCode)
                ->addFieldToFilter(FeedInterface::STATUS, FeedModel::STATUS_APPROVED)
                ->addFieldToFilter(FeedInterface::ACTUAL_PRICE, $this->getPriceRangeFilter($product));

            return $collection;
        }
        return [];
    }

    /**
     * Get Partner Feeds for configurable product
     *
     * @return array
     */
    public function getFeedsForConfigurable()
    {
        $result = [];
        $product = $this->getProduct();
        if ($product) {
            $children = $product->getTypeInstance()->getUsedProducts($product);
            foreach ($children as $child) {
                $upcCode = $child->getData(FeedModel::PRODUCT_ATTRIBUTE_UPC_CODE);
                if (!$upcCode) {
                    continue;
                }
                $collection = $this->feedCollectionFactory->create()
                    ->addFieldToFilter(FeedInterface::UPC, $upcCode)
                    ->addFieldToFilter(FeedInterface::STATUS, FeedModel::STATUS_APPROVED)
                    ->addFieldToFilter(FeedInterface::ACTUAL_PRICE, $this->getPriceRangeFilter($child));
                $result[$child->getId()] = $collection;
            }
        }
        return $result;
    }

    /**
     * Get current product
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProduct()
    {
        return $this->registry->registry('product');
    }

    /**
     * Return Filter condition
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return array
     */
    private function getPriceRangeFilter($product)
    {
        $price = $product->getPrice();
        $minPrice = $price - $this->configHelper->getPriceDifference();
        $maxPrice = $price + $this->configHelper->getPriceDifference();

        return [
            "from" => $minPrice,
            "to" => $maxPrice
        ];
    }
}
