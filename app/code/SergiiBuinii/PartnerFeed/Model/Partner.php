<?php
/**
 * @author  Sergii Buinii <sbuyny@gmail.com>
 */

namespace SergiiBuinii\PartnerFeed\Model;

use Magento\Framework\UrlInterface;
use Magento\Framework\Model\AbstractModel;
use SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface;
use SergiiBuinii\PartnerFeed\Model\ResourceModel\Partner as PartnerResourceModel;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Partner
 */
class Partner extends AbstractModel implements PartnerInterface
{
    /**#@+
     * Partner's statuses
     */
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
    /**#@-  */

    /**#@+
     * Connection Types
     */
    const TYPE_HTTP = 0;
    const TYPE_FTP = 1;
    const TYPE_SFTP = 2;
    /**#@-  */

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Feed model constructor
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        Context $context,
        StoreManagerInterface $storeManager,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(PartnerResourceModel::class);
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
            self::STATUS_ENABLED => __('Enabled')
        ];
    }

    /**
     * Get Available connection types
     *
     * @return array
     */
    public function getAvailableConnectionTypes()
    {
        return [
            self::TYPE_HTTP => __('HTTP'),
            self::TYPE_FTP => __('FTP'),
            self::TYPE_SFTP => __('SFTP')
        ];
    }

    /**
     * Get Partner Id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(PartnerInterface::ENTITY_ID);
    }

    /**
     * Set Partner id
     *
     * @param int $id
     * @return \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface
     */
    public function setId($id)
    {
        return $this->setData(PartnerInterface::ENTITY_ID, $id);
    }

    /**
     * Get Partner Status
     *
     * @return int|null
     */
    public function getStatus()
    {
        return $this->getData(PartnerInterface::STATUS);
    }

    /**
     * Set Partner Id
     *
     * @param int $status
     * @return \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface
     */
    public function setStatus($status)
    {
        return $this->setData(PartnerInterface::STATUS, $status);
    }

    /**
     * Get Partner Name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(PartnerInterface::NAME);
    }

    /**
     * Set Partner Name
     *
     * @param string $name
     * @return \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface
     */
    public function setName($name)
    {
        return $this->setData(PartnerInterface::NAME, $name);
    }

    /**
     * Get Connection Type
     *
     * @return int|null
     */
    public function getConnectionType()
    {
        return $this->getData(PartnerInterface::CONNECTION_TYPE);
    }

    /**
     * Set Connection Type
     *
     * @param int $type
     * @return \SergiiBuinii\PartnerFeed\Api\Data\PartnerInterface
     */
    public function setConnectionType($type)
    {
        return $this->setData(PartnerInterface::CONNECTION_TYPE, $type);
    }

    /**
     * Get Connection Parameters
     *
     * @return array
     */
    public function getConnectionParameters()
    {
        switch ($this->getConnectionType()) {
            case self::TYPE_HTTP:
                return $this->getHttpParameters();
                break;
            case self::TYPE_FTP:
                return $this->getFtpParameters();
                break;
            case self::TYPE_SFTP:
                return $this->getSftpParameters();
                break;
        }
    }

    /**
     * Get Http Connection Parameters
     *
     * @return array
     */
    protected function getHttpParameters()
    {
        return [];
    }

    /**
     * Get Ftp Connection Parameters
     *
     * @return array
     */
    protected function getFtpParameters()
    {
        return [];
    }

    /**
     * Get Sftp Connection Parameters
     *
     * @return array
     */
    protected function getSftpParameters()
    {
        return [];
    }
}
