<?php

namespace SergiiBuinii\Vip\Model\Config\Backend\Serialized;

use SergiiBuinii\Vip\Helper\Config;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

class DowngradeMapping extends Value
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;
    
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;
    
    /**
     * DowngradeMapping constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Random $mathRandom,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        Json $serializer = null
    ) {
        $this->mathRandom = $mathRandom;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(Json::class);
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }
    
    /**
     * Prepare data before save
     *
     * @return $this
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $result = [];
        foreach ($value as $data) {
            if (empty($data[Config::VIP_GROUP])
                || empty($data[Config::CUSTOMER_GROUP])) {
                continue;
            }
            $vipGroup = $data[Config::VIP_GROUP];
            if (array_key_exists($vipGroup, $result)) {
                throw new LocalizedException(__('Vip Group id: %1 already used', $vipGroup));
            } else {
                $result[$vipGroup] = trim($data[Config::CUSTOMER_GROUP]);
            }
        }
        $this->setValue($this->serializer->serialize($result));
        return $this;
    }
    
    /**
     * Process data after load
     *
     * @return $this
     */
    public function afterLoad()
    {
        if ($this->getValue()) {
            $value = $this->serializer->unserialize($this->getValue());
            if (is_array($value)) {
                $value = $this->encodeArrayFieldValue($value);
                $this->setValue($value);
            }
        }
        return $this;
    }
    
    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $vipGroupId => $customerGroup) {
            $id = $this->mathRandom->getUniqueHash('_');
            $result[$id] = [
                Config::VIP_GROUP => $vipGroupId,
                Config::CUSTOMER_GROUP => $customerGroup
            ];
        }
        return $result;
    }
}
