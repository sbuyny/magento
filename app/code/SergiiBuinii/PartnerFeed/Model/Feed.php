<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use SergiiBuinii\PartnerFeed\Api\Data\FeedInterface;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Feed as FeedResourceModel;
use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use SergiiBuinii\PartnerFeed\Model\PartnerRepository;

/**
 * Class Feed
 */
class Feed extends AbstractModel implements FeedInterface
{
    /**
     * @const string
     */
    const PRODUCT_ATTRIBUTE_UPC_CODE = 'upc_code';

    /**#@+
     * Feed's statuses
     */
    const STATUS_DISABLED = 0;
    const STATUS_APPROVED = 1;
    const STATUS_PENDING = 2;
    /**#@-  */

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \SergiiBuinii\PartnerFeed\Model\PartnerRepository
     */
    protected $partnerRepository;

    /**
     * Feed constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \SergiiBuinii\PartnerFeed\Model\PartnerRepository $partnerRepository
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        Context $context,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        PartnerRepository $partnerRepository,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->partnerRepository = $partnerRepository;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(FeedResourceModel::class);
    }

    /**
     * Get Available statuses
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_DISABLED => __('Disabled'),
            self::STATUS_APPROVED => __('Approved'),
            self::STATUS_PENDING => __('Pending')
        ];
    }

    /**
     * Get data array
     *
     * @return \Magento\Framework\DataObject
     */
    public function getDecodedData()
    {
        return new DataObject(unserialize($this->getSerializedData()));
    }

    /**
     * Set Serialized data
     *
     * @param string $data
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface
     */
    public function setSerializedData($data)
    {
        return $this->setData(FeedInterface::DATA, $data);
    }

    /**
     * Get Partner Name
     *
     * @return string
     */
    public function getPartner()
    {
        return $this->partnerRepository->getById($this->getPartnerId())->getName();
    }

    /**
     * Get Feed Id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(FeedInterface::ENTITY_ID);
    }

    /**
     * Set Feed id
     *
     * @param int $id
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface
     */
    public function setId($id)
    {
        return $this->setData(FeedInterface::ENTITY_ID, $id);
    }

    /**
     * Get Partner Id
     *
     * @return int|null
     */
    public function getPartnerId()
    {
        return $this->getData(FeedInterface::PARTNER_ID);
    }

    /**
     * Set Partner Id
     *
     * @param int $partnerId
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface
     */
    public function setPartnerId($partnerId)
    {
        return $this->setData(FeedInterface::PARTNER_ID, $partnerId);
    }

    /**
     * Get Actual Price
     *
     * @return float|null
     */
    public function getActualPrice()
    {
        return $this->getData(FeedInterface::ACTUAL_PRICE);
    }

    /**
     * Set Actual price
     *
     * @param float $value
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface
     */
    public function setActualPrice($value)
    {
        return $this->setData(FeedInterface::ACTUAL_PRICE, $value);
    }

    /**
     * Get Product Sku
     *
     * @return string|null
     */
    public function getSku()
    {
        return $this->getData(FeedInterface::SKU);
    }

    /**
     * Set Product Sku
     *
     * @param string $value
     * @return \SergiiBuinii\PartnerFeed\Api\Data\FeedInterface
     */
    public function setSku($value)
    {
        return $this->setData(FeedInterface::SKU, $value);
    }
}
