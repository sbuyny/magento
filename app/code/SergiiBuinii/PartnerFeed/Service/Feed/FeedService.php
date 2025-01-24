<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Service\Feed;

use SergiiBuinii\PartnerFeed\Service\File\Download\Service;
use SergiiBuinii\PartnerFeed\Service\File\ParseService;
use SergiiBuinii\PartnerFeed\Model\FeedFactory;
use SergiiBuinii\PartnerFeed\Model\Feed;
use SergiiBuinii\PartnerFeed\Model\FeedRepository;
use SergiiBuinii\PartnerFeed\Api\Data\FeedInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Model\ProductFactory;

/**
 * Class FeedService
 */
class FeedService
{
    /**
     * @var \SergiiBuinii\PartnerFeed\Service\File\Download\Service
     */
    protected $downloadService;

    /**
     * @var \SergiiBuinii\PartnerFeed\Service\File\ParseService
     */
    protected $parseService;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\FeedFactory
     */
    protected $feedFactory;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\FeedRepository
     */
    protected $feedRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * FeedService constructor.
     * @param \SergiiBuinii\PartnerFeed\Service\File\ParseService $parseService
     * @param \SergiiBuinii\PartnerFeed\Service\File\Download\Service $downloadService
     * @param \SergiiBuinii\PartnerFeed\Model\FeedFactory $feedFactory
     * @param \SergiiBuinii\PartnerFeed\Model\FeedRepository $feedRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        ParseService $parseService,
        Service $downloadService,
        FeedFactory $feedFactory,
        FeedRepository $feedRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        ProductFactory $productFactory
    ) {
        $this->parseService = $parseService;
        $this->downloadService = $downloadService;
        $this->feedFactory = $feedFactory;
        $this->feedRepository = $feedRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->productFactory = $productFactory;
    }
    
    /**
     * Process data and create feed
     *
     * @param string|array $data
     */
    public function execute($data)
    {
        $filedata = $this->downloadService->execute($data);
        $feeds = $this->parseService->parseFileData($filedata);
        $parentId = $data['entity_id'];
        $collection = (array)$feeds;
        $this->removeFeeds($parentId);
        foreach ($collection['product'] as $feed) {
            $this->createFeed((array)$feed, $parentId);
        }
    }

    /**
     * Create Feed.
     *
     * @param array $data
     * @param int $parentId
     * @return \SergiiBuinii\PartnerFeed\Model\Feed
     */
    private function createFeed($data, $parentId)
    {
        $feed = $this->feedFactory->create();
        $serializedData = serialize($data);
        $product = $this->productFactory->create()->loadByAttribute(Feed::PRODUCT_ATTRIBUTE_UPC_CODE, $data['upc']);
        $sku = '';
        if ($product) {
            $sku = $product->getSku();
        }
        $feed->setData(
            [
                FeedInterface::PARTNER_ID => $parentId,
                FeedInterface::UPC => $data['upc'],
                FeedInterface::STATUS => Feed::STATUS_APPROVED,
                FeedInterface::ACTUAL_PRICE => $data['price'],
                FeedInterface::AVAILABLE => $data['available'],
                FeedInterface::DATA => $serializedData,
                FeedInterface::SKU => $sku
            ]
        );
        $this->feedRepository->save($feed);
        return $feed;
    }

    /**
     * Remove old feeds before creating new
     *
     * @param int $parentId
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    private function removeFeeds($parentId)
    {
        $criteria = $this->criteriaBuilder
            ->addFilter(FeedInterface::PARTNER_ID, $parentId)
            ->create();
        $items = $this->feedRepository->getList($criteria)->getItems();
        foreach ($items as $item) {
            $this->feedRepository->delete($item);
        }
    }
}
